<?php

namespace CTKM\Admin;

use CTKM\Admin\Controllers\CampaignController;
use CTKM\Admin\Controllers\CampaignCreateController;
use CTKM\Admin\Controllers\CampaignSyncController;
use CTKM\Admin\Controllers\MasterDataController;
use CTKM\Repository\CampaignRepository;

class AdminService
{
    public function register()
    {
        add_action('admin_menu', [$this, 'add_menu']);
    }

    // Tạo menu chính và submenu
    public function add_menu()
    {
        // Menu chính: CTKM
        add_menu_page(
            'CTKM',                    // Title page
            'CTKM',                    // Menu title
            'manage_options',          // Capability
            'ctkm',                    // Menu slug
            [$this, 'render_dashboard'], // Callback page đầu tiên
            'dashicons-admin-post',        // Icon
            6                          // Position
        );

        // Submenu 1: Danh sách CTKM
        add_submenu_page(
            'ctkm',                        // Parent slug
            'Danh sách chương trình khuyến mãi',     // Page title
            'Danh sách chương trình khuyến mãi',     // Menu title
            'manage_options',              // Capability
            'ctkm',                   // Submenu slug
            [$this, 'render_dashboard']    // Callback
        );

        // Submenu 2: Tạo mới CTKM
        add_submenu_page(
            'ctkm',
            'Tạo mới CTKM',
            'Tạo mới',
            'manage_options',
            'ctkm_create',
            [$this, 'render_create']
        );

        // Submenu 3: Đồng bộ nhà hàng
        add_submenu_page(
            'ctkm',
            'Đồng bộ nhà hàng',
            'Đồng bộ nhà hàng',
            'manage_options',
            'ctkm_sync',
            [$this, 'render_sync']
        );


        // Submenu 4: Master Data
        add_submenu_page(
            'ctkm',
            'Master Data',
            'Master Data',
            'manage_options',
            'ctkm_master',
            [$this, 'render_master']
        );
    }

    // Callback page "Danh sách CTKM"
    public function render_dashboard()
    {
        $controller = new CampaignController(new CampaignRepository());
        $controller->index();
    }

    // Callback page Tạo mới CTKM
    public function render_create()
    {
        $controller = new CampaignCreateController(new CampaignRepository());
        $controller->index();
    }

    // Callback page Đồng bộ nhà hàng
    public function render_sync()
    {
        $controller = new CampaignSyncController(new CampaignRepository());
        $controller->index();
    }

    // Callback page Master Data
    public function render_master()
    {
        $controller = new MasterDataController(new CampaignRepository());
        $controller->index();
    }
}
