<?php
namespace CTKM\Frontend\Controllers;

use CTKM\Repository\CampaignRepository;

class CampaignController {
    private $repo;
    public function __construct(CampaignRepository $repo) {
        $this->repo = $repo;
    }

    public function show_campaigns() {
        $campaigns = $this->repo->all();
        ob_start();
        foreach ($campaigns as $c) {
            echo "<div>{$c->post_title}</div>";
        }
        return ob_get_clean();
    }
}
