<?php

namespace CTKM\Repository;

class CampaignRepository
{
    // (Trước kia: query WP_Post trực tiếp)
    public function all() {
        return get_posts(['post_type'=>'ctkm_campaign','post_status'=>'publish']);
    }
}
