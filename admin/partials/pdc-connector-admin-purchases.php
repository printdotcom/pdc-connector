
<?php

use PdcConnector\Admin\PurchaseOrder;
use PdcConnector\Admin\PurchaseOrderRepository;

class PDCOrders_List_Table extends WP_List_Table
{
    private $table_data;
    private $pdc_order_repository;

    function __construct()
    {
        parent::__construct();
        $this->pdc_order_repository = new PurchaseOrderRepository();
    }

    // Define table columns
    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'id'                    => "ID",
            'created_at'            => "Order date",
            'pdc_ordernumber'          => "Print.com Order",
            'wp_order_id'         => 'WooCommerce Order',
            'pdc_status'   => "Print.com Status"
        );
        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        $this->table_data = $this->get_table_data();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $primary  = 'pdc_ordernumber';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        $this->items = $this->table_data;
    }

    private function get_table_data()
    {
        return $this->pdc_order_repository->list();
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'wp_order_id':
                $order_id = $item->wp_order_id;
                $edit_link = admin_url('post.php?post=' . absint($order_id) . '&action=edit');
                return sprintf('<a href="%s">%s</a>', esc_url($edit_link), esc_html($order_id));
            case 'id':
                return $item->id;
            case 'created_at':
                return $item->created_at;
            case 'pdc_ordernumber':
                return $item->pdc_order_number;
            case 'pdc_status':
                return $item->pdc_status;
            default:
                return $item->$column_name;
        }
    }
}

$table = new PDCOrders_List_Table();

// Prepare table
$table->prepare_items();
// Display table
$table->display();
?>