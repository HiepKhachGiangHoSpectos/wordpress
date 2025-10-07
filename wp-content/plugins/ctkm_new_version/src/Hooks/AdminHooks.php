<?php
namespace CTKM\Hooks;

use CTKM\Controllers\Admin\OrderAdminController;
use CTKM\Controllers\Admin\InventoryAdminController;
use CTKM\Controllers\Admin\SettingsAdminController;

class AdminHooks {
    protected $orderController;
    protected $inventoryController;
    protected $settingsController;

    public function __construct() {
        $this->orderController = new OrderAdminController();
        $this->inventoryController = new InventoryAdminController();
        $this->settingsController = new SettingsAdminController();
    }

    public function register() {
        add_action('admin_menu', [$this, 'registerAdminMenu']);
    }

    public function registerAdminMenu() {
        $parent_slug = 'ctkm';

        add_menu_page(
            'CTKM',                     // Page title
            'CTKM',                     // Menu title
            'manage_options',           // Capability
            $parent_slug,               // Menu slug
            [$this->settingsController, 'renderSettingsPage'], // Callback
            'dashicons-admin-generic',  // Icon
            2                           // Position
        );

        add_submenu_page(
            $parent_slug,
            'Orders',
            'Orders',
            'manage_options',
            'ctkm_orders',
            [$this->orderController, 'listOrders']
        );

        add_submenu_page(
            $parent_slug,
            'Inventory',
            'Inventory',
            'manage_options',
            'ctkm_inventory',
            [$this->inventoryController, 'listInventory']
        );
    }
}
