<?php

namespace addons\TinyShop\common\interfaces;

use addons\TinyShop\common\forms\CartItemForm;

/**
 * Interface CartItemInterface
 * @package addons\TinyShop\common\interfaces
 */
interface CartItemInterface
{
    /**
     * 购物车列表
     *
     * @param $member_id
     * @return mixed
     */
    public function all($member_id);

    /**
     * 创建
     *
     * @param CartItemForm $form
     * @return mixed
     */
    public function create(CartItemForm $form);

    /**
     * 修改购物车数量
     *
     * @param CartItemForm $form
     * @return mixed
     */
    public function updateNumber(CartItemForm $form);

    /**
     * 修改规格
     *
     * @param CartItemForm $form
     * @return mixed
     */
    public function updateSku(CartItemForm $form);

    /**
     * 删除一组
     *
     * @param array $ids
     * @param $member_id
     * @return mixed
     */
    public function deleteIds(array $ids, $member_id);

    /**
     * 清空购物车
     *
     * @param $member_id
     * @param bool $lose_status
     * @return mixed
     */
    public function clear($member_id, $lose_status = false);

    /**
     * 让sku失效
     *
     * @param $skus
     * @return mixed
     */
    public function loseBySkus($skus);

    /**
     * 让所有商品失效
     *
     * @param $product_id
     */
    public function loseByProductIds(array $product_idsa);
}
