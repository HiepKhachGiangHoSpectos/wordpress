<?php
namespace CTKM\Services;

use CTKM\Admin\AdminService;
use CTKM\Frontend\FrontendService;
use CTKM\PostTypes\CampaignPostType;

class ServiceLoader {
    public function init() {
        // (Trước kia: add_action init)
        (new CampaignPostType())->register();

        // (Trước kia: add_action admin_menu)
        (new AdminService())->register();

        // Shortcode / Frontend
        (new FrontendService())->register();
    }
}
