<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use yii\base\Model;
use common\helpers\RegularHelper;
use common\enums\MemberTypeEnum;
use common\enums\SmsUsageEnum;

/**
 * Class MobileResetForm
 * @package api\modules\v1\models
 * @author jianyan74 <751393839@qq.com>
 */
class MobileResetForm extends Model
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
     * @var
     */
    protected $_user;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['mobile', 'code'], 'required'],
            ['code', '\common\models\validators\SmsCodeValidator', 'usage' => SmsUsageEnum::RESET_MOBILE],
            ['code', 'filter', 'filter' => 'trim'],
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
            return;
        }

        if ($this->mobile != $this->getUser()->mobile) {
            $this->addError($attribute, '该号码不属于当前登录用户');
        }
    }

    /**
     * 获取用户信息
     *
     * @return mixed|null|static
     */
    public function getUser()
    {
        if ($this->_user == false) {
            $this->_user = Yii::$app->services->member->findByCondition([
                'mobile' => $this->mobile,
                'type' => MemberTypeEnum::MEMBER,
                'merchant_id' => Yii::$app->services->merchant->getNotNullId()
            ]);
        }

        return $this->_user;
    }
}