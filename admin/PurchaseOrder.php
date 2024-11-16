<?php

namespace PdcConnector\Admin;

class PurchaseOrder
{
    public string $id;
    public string $created_at;
    public string $pdc_order_number;
    public string $pdc_price;
    public string $pdc_order_item_number;
    public string $pdc_status;
    public int $wp_order_id;
    public int $wp_order_item_id;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->created_at = $data['created_at'];
        $this->pdc_order_number = $data['pdc_order_number'];
        $this->pdc_price = $data['pdc_price'];
        $this->pdc_order_item_number = $data['pdc_order_item_number'];
        $this->pdc_status = $data['pdc_status'];
        $this->wp_order_id = $data['wp_order_id'];
        $this->wp_order_item_id = $data['wp_order_item_id'];
    }
}
