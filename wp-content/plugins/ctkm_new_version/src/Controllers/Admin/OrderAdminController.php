<?php
namespace CTKM\Controllers\Admin;

use CTKM\Services\OrderService;

class OrderAdminController {
    protected OrderService $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function listOrders() {
        $orders = $this->orderService->getAllOrders();
        include plugin_dir_path(__DIR__) . '../../../templates/admin/order-list.php';
    }
}
