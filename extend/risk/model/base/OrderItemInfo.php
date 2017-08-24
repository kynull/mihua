<?php

namespace risk\model\base;


class OrderItemInfo
{
    public $sku;                 // 商品编号
    public $goods_name;          // 商品名称
    public $category_level1_id;  // 分类A
    public $category_level2_id;  // 分类B
    public $category_level3_id;  // 分类C
    public $quantity;            // 数量
    public $price;               // 价格
}