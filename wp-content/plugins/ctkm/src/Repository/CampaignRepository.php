<?php

namespace CTKM\Repository;

class CampaignRepository
{
    // Lưu dữ liệu meta vào CPT / DB
    public function saveMeta($post_id, array $data)
    {
        if (isset($data['promotion_code'])) {
            update_post_meta($post_id, '_promotion_code_meta_key', sanitize_text_field($data['promotion_code']));
        }

        if (isset($data['start_date'])) {
            update_post_meta($post_id, '_start_date', sanitize_text_field($data['start_date']));
        }

        if (isset($data['end_date'])) {
            update_post_meta($post_id, '_end_date', sanitize_text_field($data['end_date']));
        }

        if (isset($data['post_status'])) {
            wp_update_post([
                'ID' => $post_id,
                'post_status' => sanitize_text_field($data['post_status'])
            ]);
        }

        // TODO: Lưu các field khác: restaurants, contacts, etc
    }

    // Lấy tất cả CTKM (cho danh sách)
    public function all(): array
    {
        $query = new \WP_Query([
            'post_type' => 'ctkm',
            'posts_per_page' => -1
        ]);
        return $query->posts;
    }
}
