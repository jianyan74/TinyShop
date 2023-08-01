<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use yii\base\Model;
use yii\web\UnprocessableEntityHttpException;
use common\models\member\Member;
use common\enums\SmsUsageEnum;
use common\enums\MemberTypeEnum;
use common\models\validators\SmsCodeValidator;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;

/**
 * Class RegisterEmailForm
 * @package api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class RegisterEmailForm extends Model
{
    public $email;
    public $password;
    public $code;
    public $group;
    public $realname;
    public $nickname;
    public $head_portrait;
    public $promoter_code;
    /**
     * @var Member
     */
    public $_parent;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'group', 'code', 'password'], 'required'],
            [['realname', 'nickname', 'head_portrait', 'promoter_code'], 'string'],
            [['password'], 'string', 'min' => 6, 'max' => 15],
            [['email'], 'isRegister'],
            ['promoter_code', 'promoCodeVerify'],
            ['code', SmsCodeValidator::class, 'usage' => SmsUsageEnum::REGISTER, 'email'],
            ['email', 'email'],
            ['group', 'in', 'range' => AccessTokenGroupEnum::getKeys()],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => '邮箱',
            'head_portrait' => '头像',
            'promoter_code' => '推广码',
            'realname' => '姓名',
            'nickname' => '昵称',
            'password' => '密码',
            'group' => '类型',
            'code' => '验证码',
        ];
    }

    /**
     * @param $attribute
     * @throws UnprocessableEntityHttpException
     */
    public function isRegister($attribute)
    {
        if (Yii::$app->services->member->findByCondition([
            'email' => $this->email,
            'type' => MemberTypeEnum::MEMBER,
        ])) {
            throw new UnprocessableEntityHttpException('该手机号码已注册');
        }
    }

    /**
     * @param $attribute
     * @throws UnprocessableEntityHttpException
     */
    public function promoCodeVerify($attribute)
    {
        if ($this->promoter_code && $this->promoter_code != 'undefined') {
            $this->_parent = Yii::$app->services->member->findByPromoterCode($this->promoter_code);
            if (!$this->_parent) {
                throw new UnprocessableEntityHttpException('找不到推广员');
            }
        }
    }

    /**
     * @return Member
     */
    public function getParent()
    {
        return $this->_parent;
    }
}
