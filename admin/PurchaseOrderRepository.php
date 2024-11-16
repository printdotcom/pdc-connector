<?php

namespace PdcConnector\Admin;

use PdcConnector\Admin\PurchaseOrder;

class PurchaseOrderRepository
{
    private mixed $db;
    private string $table_name;

    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
        $this->table_name = $this->db->prefix . 'pdc_orders';
    }

    public function insert_pdc_order($pdc_order, \WC_Order $order, \WC_Order_Item_Product $order_item)
    {
        $this->db->insert(
            $this->table_name,
            array(
                'pdc_order_number' => $pdc_order->orderNumber,
                'pdc_price'  => $pdc_order->grandTotal,
                'pdc_order_item_number' => $pdc_order->items[0]->orderItemNumber,
                'pdc_status' => $pdc_order->status,
                'wp_order_id' => $order->get_ID(),
                'wp_order_item_id' => $order_item->get_id(),
            )
        );
    }

    public function list(): array
    {
        $results = $this->db->get_results(
            "SELECT * from {$this->table_name}",
            ARRAY_A
        );

        return array_map(function ($item) {
            return new PurchaseOrder($item);
        }, $results);
    }

    public function get_pdc_order_by_order_item_number(string $order_item_number): PurchaseOrder
    {
        $result = $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM $this->table_name WHERE pdc_order_item_number = %s",
                $order_item_number
            ),
            ARRAY_A
        );

        return new PurchaseOrder($result);
    }
}
