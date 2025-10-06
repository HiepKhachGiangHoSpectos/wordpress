<?php

namespace CTKM\PostTypes;

class CampaignPostType
{
    public function register()
    {
        add_action('init', [$this, 'registerPostType'], 0);
    }

    public function registerPostType()
    {
        $labels = [
            'name' => __('Danh sách chương trình khuyến mãi'),
            'singular_name' => __('Khuyến mãi'),
            'menu_name' => __('CTKM'),
            'name_admin_bar' => __('CTKM'),
            'add_new' => __('Tạo mới CTKM'),
            'add_new_item' => __('Thêm khuyến mãi mới'),
            'new_item' => __('Khuyến mãi mới'),
            'edit_item' => __('Chỉnh sửa khuyến mãi'),
            'view_item' => __('Xem khuyến mãi'),
            'all_items' => __('Danh sách chương trình khuyến mãi'),
            'search_items' => __('Tìm kiếm khuyến mãi'),
            'not_found' => __('Không tìm thấy kết quả phù hợp. Vui lòng thử lại'),
            'not_found_in_trash' => __('Không có khuyến mãi trong thùng rác'),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,      // menu do AdminService tạo
            'query_var' => true,
            'rewrite' => ['slug' => 'ctkm'],
            'capability_type' => 'ctkm',
            'capabilities' => [
                'publish_posts' => 'publish_ctkms',
                'edit_posts' => 'edit_ctkms',
                'edit_others_posts' => 'edit_others_ctkms',
                'delete_posts' => 'delete_ctkms',
                'delete_others_posts' => 'delete_others_ctkms',
                'read_private_posts' => 'read_private_ctkms',
                'edit_post' => 'edit_ctkm',
                'delete_post' => 'delete_ctkm',
                'read_post' => 'read_ctkm',
                'create_posts' => 'create_ctkms',
                'edit_published_posts' => 'edit_published_ctkms',
                'delete_published_posts' => 'delete_published_ctkms',
                'edit_private_posts' => 'edit_private_ctkms',
                'delete_private_posts' => 'delete_private_ctkms',
                'read' => 'read_ctkm',
            ],
            'has_archive' => true,
            'hierarchical' => false,
            'supports' => ['title'],  // post-body-content trống
        ];

        register_post_type('ctkm', $args);
    }
}
