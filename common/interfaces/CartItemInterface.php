<?php

namespace addons\TinyShop\common\interfaces;

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
     * @param $sku
     * @param $num
     * @param $member_id
     * @return mixed
     */
    public function create($sku, $num, $member_id);

    /**
     * 修改购物车数量
     *
     * @param $sku
     * @param $num
     * @param $member_id
     * @return mixed
     */
    public function updateNum($sku, $num, $member_id);

    /**
     * 删除一组
     *
     * @param array $sku_ids
     * @param $member_id
     * @return mixed
     */
    public function deleteBySkuIds(array $sku_ids, $member_id);

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
     * 让所有产品失效
     *
     * @param $product_id
     */
    public function loseByProductIds(array $product_idsa);
}