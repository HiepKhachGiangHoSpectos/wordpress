<?php

namespace CTKM\Frontend;

use CTKM\Repository\CampaignRepository;

class FrontendService
{
    public function register()
    {
        add_shortcode('ctkm_list', [$this, 'render_campaigns']);
    }

    // (Trước kia: function ctkm_render_shortcode)
    public function render_campaigns()
    {
        $repo = new CampaignRepository();
        $campaigns = $repo->all();
        ob_start();
        echo '<ul>';
        foreach ($campaigns as $c) {
            echo '<li>' . esc_html($c->post_title) . '</li>';
        }
        echo '</ul>';
        return ob_get_clean();
    }
}
