<?php

namespace CTKM\Admin\Controllers;

use CTKM\Repository\CampaignRepository;

class CampaignController
{
    private $repo;

    public function __construct(CampaignRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $campaigns = $this->repo->all();
        include __DIR__ . '/../Views/campaign-list.php';
    }
}
