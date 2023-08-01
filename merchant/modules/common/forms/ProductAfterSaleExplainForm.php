<?php

namespace addons\TinyShop\merchant\modules\common\forms;

use yii\base\Model;

/**
 * Class ProductAfterSaleExplainForm
 * @package addons\TinyShop\merchant\modules\common\forms
 * @author jianyan74 <751393839@qq.com>
 */
class ProductAfterSaleExplainForm extends Model
{
    public $product_after_sale_explain = '<p>
    <span style="color: rgb(255, 0, 0);"><strong>权利声明：</strong></span><br/><span style="font-size: 12px; color: rgb(165, 165, 165);">商城上的所有商品信息、客户评价、商品咨询、网友讨论等内容，是商城重要的经营资源，未经许可，禁止非法转载使用。</span>
</p>
<p>
    <span style="font-size: 12px; color: rgb(165, 165, 165);">注：本站商品信息均来自于合作方，其真实性、准确性和合法性由信息拥有者（合作方）负责。本站不提供任何保证，并不承担任何法律责任。</span>
</p>
<p style="white-space: normal;">
    <br/><span style="color: rgb(255, 0, 0);"><strong>价格说明：</strong></span><br/>
</p>
<p>
    <span style="font-size: 12px; color: rgb(165, 165, 165);">价格：价格为商品的销售价，是您最终决定是否购买商品的依据。</span>
</p>
<p>
    <span style="font-size: 12px; color: rgb(165, 165, 165);">划线价：商品展示的划横线价格为参考价，该价格可能是品牌专柜标价、商品吊牌价或由品牌供应商提供的正品零售价（如厂商指导价、建议零售价等）或该商品在商城平台上曾经展示过的销售价；由于地区、时间的差异性和市场行情波动，品牌专柜标价、商品吊牌价等可能会与您购物时展示的不一致，该价格仅供您参考。</span>
</p>
<p>
    <span style="font-size: 12px; color: rgb(165, 165, 165);">折扣：如无特殊说明，折扣指销售商在原价、或划线价（如品牌专柜标价、商品吊牌价、厂商指导价、厂商建议零售价）等某一价格基础上计算出的优惠比例或优惠金额；如有疑问，您可在购买前联系销售商进行咨询。</span>
</p>
<p>
    <span style="font-size: 12px; color: rgb(165, 165, 165);">异常问题：商品促销信息以商品详情页“促销”栏中的信息为准；商品的具体售价以订单结算页价格为准；如您发现活动商品售价或促销信息有异常，建议购买前先联系销售商咨询。</span>
</p>';

    public function rules()
    {
        return [
            [['product_after_sale_explain'], 'required'],
            [['product_after_sale_explain'], 'string', 'max' => 1500],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'product_after_sale_explain' => '售后保障',
        ];
    }
}
