<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use yii\base\Model;
use common\helpers\RegularHelper;
use common\enums\StatusEnum;
use common\enums\SmsUsageEnum;
use common\enums\MemberTypeEnum;

/**
 * Class SmsCodeForm
 * @package api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class SmsCodeForm extends Model
{
    /**
     * @var
     */
    public $mobile;

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
            [['mobile', 'usage'], 'required'],
            [['mobile'], 'isBeforeSend'],
            [['usage'], 'in', 'range' => array_keys(SmsUsageEnum::getMap())],
            ['mobile', 'match', 'pattern' => RegularHelper::mobile(), 'message' => '请输入正确的手机号'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'usage' => '用途',
        ];
    }

    /**
     * @param $attribute
     */
    public function isBeforeSend($attribute)
    {
        $member = Yii::$app->services->member->findByCondition([
            'mobile' => $this->mobile,
            'type' => MemberTypeEnum::MEMBER,
        ]);

        if ($this->usage == SmsUsageEnum::REGISTER && $member) {
            $this->addError($attribute, '该手机号码已注册');
        }

        if (
            in_array($this->usage, [SmsUsageEnum::LOGIN, SmsUsageEnum::UP_PWD]) &&
            !$member &&
            $this->member_mobile_login_be_register == StatusEnum::DISABLED
        ) {
            $this->addError($attribute, '该手机号码未注册');
        }
    }

    /**
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function send()
    {
        $code = rand(1000, 9999);

        return Yii::$app->services->extendSms->send($this->mobile, $code, $this->usage);
    }
}
