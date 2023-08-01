<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use yii\base\Model;
use common\enums\MemberTypeEnum;
use common\helpers\RegularHelper;
use common\models\member\Member;
use common\enums\SmsUsageEnum;
use addons\TinyShop\common\enums\AccessTokenGroupEnum;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class MobileLogin
 * @package api\modules\v1\models
 * @author jianyan74 <751393839@qq.com>
 */
class MobileLogin extends Model
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
    public $group;

    /**
     * @var
     */
    public $promoter_code;

    /**
     * @var
     */
    protected $_user;

    /**
     * @var Member
     */
    public $_parent;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['mobile', 'code', 'group'], 'required'],
            ['code', '\common\models\validators\SmsCodeValidator', 'usage' => SmsUsageEnum::LOGIN],
            ['code', 'filter', 'filter' => 'trim'],
            ['promoter_code', 'promoCodeVerify'],
            ['mobile', 'match', 'pattern' => RegularHelper::mobile(), 'message' => '请输入正确的手机号'],
            ['group', 'in', 'range' => AccessTokenGroupEnum::getKeys()]
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
            'group' => '组别',
        ];
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
            ]);
        }

        return $this->_user;
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
