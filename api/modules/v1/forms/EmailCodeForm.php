<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use yii\base\Model;
use common\enums\StatusEnum;
use common\enums\SmsUsageEnum;
use common\enums\MemberTypeEnum;
use common\models\extend\SmsLog;

/**
 * Class EmailCodeForm
 * @package api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class EmailCodeForm extends Model
{
    /**
     * @var
     */
    public $email;

    /**
     * @var
     */
    public $usage;

    /**
     * @var int
     */
    public $member_mobile_login_be_register;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'usage'], 'required'],
            [['email'], 'isBeforeSend'],
            [['usage'], 'in', 'range' => array_keys(SmsUsageEnum::getMap())],
            ['email', 'email'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => '邮箱',
            'usage' => '用途',
        ];
    }

    /**
     * @param $attribute
     */
    public function isBeforeSend($attribute)
    {
        $member = Yii::$app->services->member->findByCondition([
            'email' => $this->email,
            'type' => MemberTypeEnum::MEMBER,
        ]);

        if ($this->usage == SmsUsageEnum::REGISTER && $member) {
            $this->addError($attribute, '该邮箱已注册');
        }

        if (
            in_array($this->usage, [SmsUsageEnum::LOGIN, SmsUsageEnum::UP_PWD]) &&
            !$member &&
            $this->member_mobile_login_be_register == StatusEnum::DISABLED
        ) {
            $this->addError($attribute, '该邮箱未注册');
        }
    }

    /**
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function send()
    {
        $code = rand(1000, 9999);
        // 发送邮件
        Yii::$app->services->mailer->realSend([], $this->email, '注册验证码', 'registerCode', [
            'code' => $code
        ]);

        $log = new SmsLog();
        $log = $log->loadDefaultValues();
        $log->attributes = [
            'mobile' => $this->email,
            'code' => $code,
            'member_id' => 0,
            'usage' => $this->usage,
            'error_code' => 200,
            'error_msg' => 'ok',
            'error_data' => '',
        ];

        $log->save();

        return true;
    }
}
