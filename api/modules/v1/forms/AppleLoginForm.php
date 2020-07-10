<?php

namespace addons\TinyShop\api\modules\v1\forms;

use yii\base\Model;

/**
 * Class AppleLoginForm
 * @package addons\TinyShop\api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class AppleLoginForm extends Model
{
    public $user;
    public $email;
    public $authorizationCode;
    public $identityToken;
    public $fullName;
    public $realUserStatus;

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['user', 'identityToken'], 'required'],
            [['user', 'email', 'authorizationCode', 'identityToken'], 'string'],
            [['fullName'], 'safe'],
            [['realUserStatus'], 'integer'],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'user' => 'openId',
            'identityToken' => '授权 token',
            'email' => '邮箱',
            'authorizationCode' => '授权码',
            'fullName' => '用户名称',
            'realUserStatus' => '用户状态',
        ];
    }
}