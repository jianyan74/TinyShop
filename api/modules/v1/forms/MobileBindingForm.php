<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use yii\base\Model;
use common\enums\MemberTypeEnum;
use common\helpers\RegularHelper;
use common\enums\SmsUsageEnum;
use common\models\member\Member;

/**
 * Class MobileBindingForm
 * @package addons\TinyShop\api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class MobileBindingForm extends Model
{
    /**
     * @var
     */
    public $mobile;

    /**
     * @var
     */
    public $code;

    /**
     * 手机号码重置token
     *
     * @var string
     */
    public $mobile_reset_token;

    /**
     * @var Member
     */
    public $user;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['mobile', 'code', 'user'], 'required'],
            ['code', '\common\models\validators\SmsCodeValidator', 'usage' => SmsUsageEnum::BINDING_MOBILE],
            ['code', 'filter', 'filter' => 'trim'],
            ['mobile_reset_token', 'string'],
            ['user', 'validateUser'],
            ['mobile', 'match', 'pattern' => RegularHelper::mobile(), 'message' => '请输入正确的手机号'],
            ['mobile', 'validateMobile'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'mobile_reset_token' => '手机重置token',
            'code' => '验证码',
        ];
    }

    /**
     * @param $attribute
     */
    public function validateMobile($attribute)
    {
        $user = Yii::$app->services->member->findByCondition([
            'mobile' => $this->mobile,
            'type' => MemberTypeEnum::MEMBER,
            'merchant_id' => Yii::$app->services->merchant->getNotNullId()
        ]);

        if ($user) {
            $this->addError($attribute, '该号码已经被绑定');
        }
    }

    /**
     * @param $attribute
     */
    public function validateUser($attribute)
    {
        if (!$this->user) {
            $this->addError($attribute, '找不到用户');
            return;
        }

        if ($this->user->mobile) {
            if (empty($this->mobile_reset_token)) {
                $this->addError($attribute, '找不到重置令牌');
                return;
            }

            $token = $this->mobile_reset_token;
            if ($token != $this->user->mobile_reset_token) {
                $this->addError($attribute, '重置令牌错误');
                return;
            }

            $timestamp = (int)substr($token, strrpos($token, '_') + 1);
            $expire = 3600 * 2;

            // 验证有效期
            if ($timestamp + $expire <= time()) {
                $this->addError($attribute, '重置令牌已过期，请重新绑定');
            }
        }
    }
}