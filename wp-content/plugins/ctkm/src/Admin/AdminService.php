<?php

namespace CTKM\Admin;

use CTKM\Admin\Controllers\CampaignController;
use CTKM\Admin\Controllers\CampaignCreateController;
use CTKM\Admin\Controllers\CampaignSyncController;
use CTKM\Admin\Controllers\MasterDataController;
use CTKM\Repository\CampaignRepository;
use CTKM\Admin\Controllers\CampaignMetaBox;

class AdminService
{
    public function register()
    {
        add_action('admin_menu', [$this, 'addMenu']);
        // CPT
        add_action('init', [$this, 'registerCampaignPostType']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);

        // Meta box
        add_action('add_meta_boxes', [$this, 'addCampaignMetaBox']);

        // Save post → dùng controller
        add_action('save_post_ctkm', function ($post_id, $post, $update) {
            $controller = new CampaignCreateController(new CampaignRepository());
            $controller->save_meta_data($post_id, $post, $update);
        }, 10, 3);

        // Redirect submenu "Tạo mới CTKM" sang post-new.php
        add_action('admin_init', [$this, 'maybeRedirectCreatePage']);

        add_action('admin_head', function () {
            global $pagenow;
            if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'ctkm') {
                echo '<style>#post-body-content { display: none; }</style>';
            }
        });

        // Giữ highlight menu khi đang trên CPT CTKM
        add_filter('parent_file', [$this, 'highlightParentMenu']);
        add_filter('submenu_file', [$this, 'highlightSubMenu']);
    }


    public function enqueue_styles() {
        wp_enqueue_style('edit-create', plugin_dir_url(__DIR__) . 'Admin/Assets/css/ctkm-create-edit.css', array(), filemtime(plugin_dir_path(__DIR__) . 'Admin/Assets/css/ctkm-create-edit.css'));
    }


    public function enqueue_scripts() {
        $inline_script = 'var ajax_url = "' . admin_url( 'admin-ajax.php' ) . '";';
        $inline_script .= 'var admin_url = "' . admin_url( 'admin-post.php' ) . '";';
        wp_enqueue_script( 'edit-create', plugin_dir_url( __DIR__ ) . 'Admin/Assets/js/edit-create.js', array( 'jquery'), filemtime( plugin_dir_path( __DIR__ ) . 'Admin/Assets/js/edit-create.js' ), true );
        wp_add_inline_script( 'edit-create', $inline_script, 'before' );
    }


    public function enqueueAssets($hook_suffix)
    {
        // Chỉ load khi đang ở trang edit hoặc add new của post type "campaign"
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'ctkm') {
            $this->enqueue_styles();
            $this->enqueue_scripts();
        }
    }


    // Tạo menu chính và submenu
    public function addMenu()
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
            '__return_null'    // Callback
        );
        // Submenu 2: Tạo mới CTKM
        add_submenu_page(
            'ctkm',
            'Tạo mới CTKM',
            'Tạo mới',
            'manage_options',
            'ctkm_create',
            '__return_null'
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

    // Redirect submenu "Tạo mới CTKM" sang post-new.php
    public function maybeRedirectCreatePage()
    {
        if (isset($_GET['page']) && $_GET['page'] === 'ctkm_create') {
            wp_redirect(admin_url('post-new.php?post_type=ctkm'));
            exit;
        }
    }

    // Giữ highlight menu khi đang trên CPT CTKM
    public function highlightParentMenu($parent_file)
    {
        global $pagenow;
        if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'ctkm') {
            return 'ctkm'; // menu cha highlight
        }
        return $parent_file;
    }

    public function highlightSubMenu($submenu_file)
    {
        global $pagenow;
        if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'ctkm') {
            return 'ctkm_create'; // submenu highlight
        }
        return $submenu_file;
    }

    // Callback page "Danh sách CTKM"
    public function render_dashboard()
    {
        $controller = new CampaignController(new CampaignRepository());
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

    public function registerCampaignPostType()
    {
        $labels = [
            'name' => 'CTKM',
            'singular_name' => 'Khuyến mãi',
            'add_new' => 'Tạo mới CTKM',
            'add_new_item' => 'Thêm khuyến mãi mới',
            'edit_item' => 'Chỉnh sửa CTKM',
            'all_items' => 'Danh sách CTKM'
        ];

        register_post_type('ctkm', [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => ['title']
        ]);

    }

    public function addCampaignMetaBox()
    {
        add_meta_box(
            'ctkm_meta_box_id',
            'Thông tin chương trình khuyến mãi',
            function ($post) {
                $controller = new CampaignCreateController(new CampaignRepository());
                $controller->render_meta_box($post);
            },
            'ctkm',
            'normal',
            'high'
        );
    }
}
