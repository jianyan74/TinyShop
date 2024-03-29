<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use common\enums\MemberTypeEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;

/**
 * Class LoginForm
 * @package api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class LoginForm extends \common\forms\LoginForm
{
    public $group;
    public $mobile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'password', 'group'], 'required'],
            ['password', 'validatePassword'],
            ['group', 'in', 'range' => AccessTokenGroupEnum::getKeys()]
        ];
    }

    public function attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'password' => '登录密码',
            'group' => '组别',
        ];
    }

    /**
     * 用户登录
     *
     * @return mixed|null|static
     */
    public function getUser()
    {
        if ($this->_user == false) {
            $this->_user = Yii::$app->services->member->findByCondition([
                'mobile' => $this->mobile,
                'type' => MemberTypeEnum::MEMBER,
                'merchant_id' => Yii::$app->services->merchant->getNotNullId(),
            ]);
        }

        return $this->_user;
    }
}
