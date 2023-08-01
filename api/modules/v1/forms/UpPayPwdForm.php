<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use common\enums\MemberTypeEnum;
use common\helpers\RegularHelper;
use common\enums\SmsUsageEnum;
use common\models\member\Member;
use common\models\validators\SmsCodeValidator;

/**
 * Class UpPayPwdForm
 * @package api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class UpPayPwdForm extends \common\forms\LoginForm
{
    public $mobile;
    public $password;
    public $code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'group', 'code', 'password'], 'required'],
            [['password'], 'string', 'min' => 6, 'max' => 6],
            ['mobile', 'match', 'pattern' => RegularHelper::mobile(), 'message' => '请输入正确的手机号码'],
            ['password', 'validateMobile'],
            ['code', SmsCodeValidator::class, 'usage' => SmsUsageEnum::UP_PAY_PWD],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'password' => '密码',
            'code' => '验证码',
        ];
    }

    /**
     * @param $attribute
     */
    public function validateMobile($attribute)
    {
        if (!$this->getUser()) {
            $this->addError($attribute, '找不到用户');
        }
    }

    /**
     * @return Member|mixed|null
     */
    public function getUser()
    {
        if ($this->_user == false) {
            $this->_user = Yii::$app->services->member->findByCondition([
                'mobile' => $this->mobile,
                'type' => MemberTypeEnum::MEMBER,
            ]);
        }

        return $this->_user;
    }
}
