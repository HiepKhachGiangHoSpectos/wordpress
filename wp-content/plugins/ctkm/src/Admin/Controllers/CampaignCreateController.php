<?php
namespace CTKM\Admin\Controllers;

use CTKM\Repository\CampaignRepository;

class CampaignCreateController {
    private $repo;

    public function __construct(CampaignRepository $repo) {
        $this->repo = $repo;
    }

    public function index() {
        include __DIR__ . '/../Views/campaign-create.php';
    }
}
