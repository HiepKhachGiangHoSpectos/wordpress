<?php

namespace CTKM\Admin\Controllers;

class CampaignMetaBox
{
    // Dữ liệu region + brand (cache 1h)
    public function getRegionWithBrand(): array
    {
        $cacheKey = 'ctkm_region_brand';
        $data = get_transient($cacheKey);
        if ($data === false) {
            $data = $this->queryRegionWithBrand();
            set_transient($cacheKey, $data, HOUR_IN_SECONDS);
        }
        return $data;
    }

    // Dữ liệu tất cả nhà hàng (cache 1h)
    public function getAllRestaurant(): array
    {
        $cacheKey = 'ctkm_all_restaurant';
        $data = get_transient($cacheKey);
        if ($data === false) {
            $data = $this->queryAllRestaurant();
            set_transient($cacheKey, $data, HOUR_IN_SECONDS);
        }
        return $data;
    }

    // Contacts theo loại (IT, MKT, RECEIVER)
    public function getContactsByType(string $type): array
    {
        $cacheKey = 'ctkm_contacts_' . $type;
        $data = get_transient($cacheKey);
        if ($data === false) {
            $data = $this->queryContactsByType($type);
            set_transient($cacheKey, $data, HOUR_IN_SECONDS);
        }
        return $data;
    }

    public function getSelectedContactsByType($ctkm_id, $type): array
    {
        // ví dụ: get từ meta hoặc bảng liên kết
        return [];
    }

    public function getRestaurantSelected($ctkm_id): array
    {
        return [];
    }

    public function refreshCache()
    {
        delete_transient('ctkm_region_brand');
        delete_transient('ctkm_all_restaurant');
        delete_transient('ctkm_contacts_IT');
        delete_transient('ctkm_contacts_MKT');
        delete_transient('ctkm_contacts_RECEIVER');
    }

    // Hàm query giả lập (thực tế query DB / API)
    private function queryRegionWithBrand(): array { return []; }
    private function queryAllRestaurant(): array { return []; }
    private function queryContactsByType(string $type): array { return []; }
}
