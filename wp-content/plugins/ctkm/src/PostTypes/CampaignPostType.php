<?php

namespace CTKM\PostTypes;

class CampaignPostType
{
    public function register()
    {
        add_action('init', function () {
            register_post_type('ctkm', [
                'label' => 'Chương trình KM',
                'public' => true,
                'show_in_rest' => true,
                'show_in_menu' => false, // ✅ menu do AdminService tạo
                'supports' => ['title', 'editor', 'custom-fields'],
            ]);
        });
    }
}
