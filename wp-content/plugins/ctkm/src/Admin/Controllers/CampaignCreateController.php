<?php
namespace CTKM\Admin\Controllers;

use CTKM\Repository\CampaignRepository;

class CampaignCreateController {
    private $repo;
    private $metaBox;

    public function __construct(CampaignRepository $repo) {
        $this->repo = $repo;
        $this->metaBox = new CampaignMetaBox();
    }

    /**
     * Render meta box
     */
    public function render_meta_box($post)
    {
        $fields = get_post_meta($post->ID);

        $regionBrandSelected = $this->metaBox->getRestaurantSelected($fields['ctkm_id'] ?? null);
        $regionBrand = $this->metaBox->getRegionWithBrand();
        $allRestaurant = $this->metaBox->getAllRestaurant();

        $itContacts = $this->metaBox->getContactsByType("IT");
        $mktContacts = $this->metaBox->getContactsByType("MKT");
        $receiverContacts = $this->metaBox->getContactsByType("RECEIVER");

        $selectedITContacts = $this->metaBox->getSelectedContactsByType($fields['ctkm_id'] ?? '', "IT");
        $selectedMKTContacts = $this->metaBox->getSelectedContactsByType($fields['ctkm_id'] ?? '', "MKT");
        $selectedReceiverContacts = $this->metaBox->getSelectedContactsByType($fields['ctkm_id'] ?? '', "RECEIVER");

        include __DIR__ . '/../Views/meta-box-create.php';
    }

    /**
     * Save post meta data
     */
    public function save_meta_data($post_id, $post, $update)
    {
        if (!isset($_POST['ctkm_nonce']) || !wp_verify_nonce($_POST['ctkm_nonce'], 'ctkm_create_nonce')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        $this->repo->saveMeta($post_id, $_POST);

        // Nếu publish → xử lý state machine / gửi mail
        if (($_POST['post_status'] ?? '') === 'publish') {
            // $promotionStateMachine->process($post_id);
        }
    }
}
