<?php
// Đảm bảo rằng tệp này không được truy cập trực tiếp
use CTKM\Enums\PromotionType;
use CTKM\Enums\RegionMapping;
use CTKM\Enums\CtkmConsts;
use CTKM\Enums\PromotionStatuses;

if (!defined('ABSPATH')) {
    exit;
}
global $post;
// Lấy dữ liệu các trường meta từ biến $fields
$is_edit_page = isset($_GET['action']) && $_GET['action'] === 'edit';

var_dump($is_edit_page);
$old_value = get_transient('old_value');
if ($old_value) {
    $fields = $old_value;
}
delete_transient('old_value');

function selectedMultiple($queryParam, $key)
{
    $data = $_GET[$queryParam] ?? '';
    if ($data) {
        return in_array($key, $data);
    }
    return false;
}

function isSelected($searchItem, $dataForCheck)
{
    // Chuẩn hóa chuỗi (loại bỏ khoảng trắng dư thừa và ký tự không cần thiết)
    $normalize = function ($str) {
        return trim(preg_replace('/\s+/', ' ', $str));
    };

    // Chuẩn hóa giá trị tìm kiếm
    $searchItem = $normalize($searchItem);

    // Chuẩn hóa và kiểm tra từng phần tử trong mảng
    foreach ((array)$dataForCheck as $item) {
        if ($normalize($item) === $searchItem) {
            return true;
        }
    }

    return false;
}

$promotion_code = $fields['promotion_code'][0] ?? '';
$start_date = $fields['start_date'][0] ?? '';
$end_date = $fields['end_date'][0] ?? '';
$post_status = get_post_status($post->ID);

// Lấy dữ liệu region + brand + restaurant
$regionBrandSelected = $this->metaBox->getRestaurantSelected($post->ID);
$regionBrand = $this->metaBox->getRegionWithBrand();
$allRestaurant = $this->metaBox->getAllRestaurant();

// Lấy danh sách contacts
$itContacts = $this->metaBox->getContactsByType("IT");
$mktContacts = $this->metaBox->getContactsByType("MKT");
$receiverContacts = $this->metaBox->getContactsByType("RECEIVER");

// Lấy contact đã chọn
$selectedITContacts = $this->metaBox->getSelectedContactsByType($post->ID, "IT");
$selectedMKTContacts = $this->metaBox->getSelectedContactsByType($post->ID, "MKT");
$selectedReceiverContacts = $this->metaBox->getSelectedContactsByType($post->ID, "RECEIVER");

?>

    <style>
        <?php if($post->post_status == 'publish'): ?>
        .edit-post-status, .edit-timestamp, #delete-action {
            display: none;
        }

        <?php endif; ?>


    </style>

    <div id="custom-confirm" class="custom-modal hidden">
        <div class="modal-content">
            <p>Đồng bộ sẽ ghi đè dữ liệu hiện tại, bạn có chắc chắn muốn đồng bộ không?</p>
            <div class="modal-actions">
                <button id="confirm-cancel" class="button button-secondary" type="button">Hủy</button>
                <button id="confirm-ok" class="button button-primary" type="button">Đồng ý</button>

            </div>
        </div>
    </div>


    <div id="custom-alert" class="custom-modal hidden">
        <div class="modal-content">
            <p id="alert-message">Thông báo</p>
            <div class="modal-actions">
                <!-- <button id="alert-ok"  type="button">OK</button> -->
                <button id="alert-ok" class="button button-primary" type="button">OK</button>

            </div>
        </div>
    </div>


    <table class="form-table">
        <tr class="<?php if ($is_edit_page) echo "hidden" ?>">
            <th>
                <label class="label" for="source_api_select">Nguồn CMS</label>
                <div class="source-api">
                    <select class="input" id="source_api_select" name="source_api_select">
                        <option selected value="mkt_mien_nam">None</option>
                        <option value="get_tgs_data">CMS TGS</option>
                        <option value="get_ecom_data">CMS E-com</option>
                    </select>
                    <!--                <span class="dashicons dashicons-update" id="source-api"></span>-->
                </div>
            </th>
            <td class="source-api-result" id="source-api-result">
                <label class="label label-check-validate" for="ctkm_api">Tìm kiếm chương trình khuyến mại</label><span
                        class="text-danger"> *</span>

                <input class="input" type="text" id="ctkm_api" name="ctkm_api" size="25"/>
                <div class="dropdown-menu" id="dropdownMenu">
                </div>
            </td>
        </tr>
        <tr>
            <th>
                <label for="ctkm_id_field">ID Bài Viết:</label><span class="text-danger"> *</span>
            </th>
            <td>
                <input type="text" readonly value="<?php echo esc_attr($fields['ctkm_id']); ?>" id="ctkm_id_field"
                       name="ctkm_id_field" size="25"/>
            </td>
        </tr>
        <input type="text" class="hidden" id="ctkm_source" name="ctkm_source"
               value="<?php echo esc_textarea($fields['ctkm_source']); ?>"/>
        <tr class="form_item_div">
            <th>
                <label for="promotion_type_field label-check-validate">Loại chiến dịch</label><span class="text-danger"> *</span>
            </th>
            <td>
                <select class="text_input" id="promotion_type_field" name="promotion_type_field">
                    <option selected disabled readonly>Chọn loại chiến dịch</option>
                    <?php foreach (PromotionType::options() as $key => $value): ?>
                        <option value="<?php echo $key ?>" <?php if ($fields['promotion_type'] === $key): ?> selected <?php endif; ?>> <?php echo $value ?> </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr class="form_item_div">
            <th>
                <label for="promotion_name_field" class="label-check-validate">Tên chương trình khuyến mãi</label>
                <span class="text-danger">*</span>
            </th>
            <td>
                <input
                        class="text_input"
                        type="text"
                        id="promotion_name_field"
                        name="promotion_name_field"
                        value="<?php echo esc_attr($fields['promotion_name']); ?>"
                        size="25"
                        placeholder="Nhập tên chương trình khuyến mãi"
                />
            </td>
        </tr>

        <tr class="form_item_div">
            <th>
                <label for="promotion_code_field" class="label-check-validate">PromoCode</label>
                <span class="text-danger"> *</span>
            </th>
            <td>
                <input type="text" class="text_input" id="promotion_code_field" name="promotion_code_field"
                       placeholder="Nhập mã PromoCode"
                       value="<?php echo esc_attr($fields['promotion_code']); ?>" size="25" maxlength="40"/>
            </td>
        </tr>

        <tr class="form_item_div">
            <th>
                <label for="promotion_partner_field" class="label-check-validate">Tên đối tác (nếu có)</label>
            </th>
            <td>
                <input
                        type="text"
                        id="promotion_partner_field"
                        name="promotion_partner_field"
                        value="<?php echo esc_attr($fields['promotion_partner']); ?>"
                        size="25"
                        placeholder="Nhập tên đối tác"
                />
            </td>
        </tr>
        <tr>
            <th>
                <label for="promotion_status_field" class="label-check-validate">Tình trạng chiến dịch</label><span
                        class="text-danger">*</span>
            </th>
            <td>
                <input type="text" id="promotion_status_field" name="promotion_status_field" readonly
                       value="<?php
                       if (!empty($fields['start_date']) && !empty($fields['end_date'])) {
                           $key = get_ctkm_status($fields['start_date'], $fields['end_date'], $fields['is_end_before']);
                           echo PromotionStatuses::MAP[$key];
                       }
                       ?>" size="25"/>
            </td>
        </tr>
        <tr class="form_item_div">
            <th><label for="thoi_gian_hieu_luc_field" class="label-check-validate">Thời gian hiệu lực</label><span
                        class="text-danger"> *</span></th>
            <td>
                <div class="date-range">
                    <input
                            type="date"
                            id="start_date_field"
                            name="start_date_field"
                            value="<?php echo esc_attr($fields['start_date']); ?>"
                    /> ~
                    <input
                            type="date"
                            id="end_date_field"
                            name="end_date_field"
                            value="<?php echo esc_attr($fields['end_date']); ?>"
                    />
                </div>
            </td>
        </tr>
        <tr class="form_item_div">
            <th>
                <label for="promotion_content_field" class="label-check-validate">Nội dung chương trình</label>
                <span class="text-danger"> *</span>
            </th>
            <td>
                <?php echo wp_editor($fields['promotion_content'] ?? '', 'promotion_content_field', array('textarea_name' => 'promotion_content_field', 'textarea_rows' => 4, 'editor_height' => 200)); ?></textarea>
            </td>
        </tr>
        <tr class="form_item_div">
            <th>
                <label for="promotion_summary_field" class="label-check-validate">Tóm tắt chương trình</label>
                <span class="text-danger"> *</span>
            </th>
            <td>
                <?php echo wp_editor($fields['promotion_summary'] ?? '', 'promotion_summary_field', array('textarea_name' => 'promotion_summary_field', 'textarea_rows' => 4, 'editor_height' => 200)); ?></textarea>
            </td>
        </tr>
        <tr class="form_item_div">
            <th>
                <label for="apply_condition_field" class="label-check-validate">Điều kiện áp dụng chương
                    trình</label><span class="text-danger"> *</span>
            </th>
            <td><?php echo wp_editor($fields['apply_condition'] ?? '', 'apply_condition_field', array('textarea_name' => 'apply_condition_field', 'textarea_rows' => 4, 'editor_height' => 200)); ?></td>
        </tr>
        <tr class="form_item_div">
            <th>
                <label for="guideline_field" class="label-check-validate">Hướng dẫn thao tác</label>
                <span class="text-danger"> *</span>
            </th>
            <td><?php echo wp_editor($fields['guideline'] ?? '', 'guideline_field', array('textarea_name' => 'guideline_field', 'textarea_rows' => 4, 'editor_height' => 200)); ?></td>

        </tr>

        <tr>
            <th><label for="note_field">Lưu ý (nếu có)</label></th>
            <td>
                <?php echo wp_editor($fields['note'] ?? '', 'note_field', array('textarea_name' => 'note_field', 'textarea_rows' => 4, 'editor_height' => 200)); ?>
            </td>
        </tr>

        <tr class="form_item_div">
            <th>
                <label for="it_contact_field" class="label-check-validate">Đầu mối liên hệ IT</label><span
                        class="text-danger"> *</span>
            </th>
            <td>
                <select id="it_contact_field" class="multi_select" name="it_contact_field[]" multiple>
                    <?php
                    // Hiển thị các lựa chọn trong dropdown
                    foreach ($itContacts as $itInfo):
                        $selected = in_array($itInfo->id, $selectedITContacts) ? 'selected' : '';
                        ?>
                        <option value="<?= $itInfo->id ?>" <?= $selected ?>><?= $itInfo->name ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr class="form_item_div">
            <!-- HTML cho dropdown với các tùy chọn lấy từ CSV -->
            <th><label for="mkt_contact_field" class="label-check-validate">Đầu mối liên hệ MKT/GSO/S&P</label><span
                        class="text-danger"> *</span></th>
            <td>
                <select id="mkt_contact_field" class="multi_select" name="mkt_contact_field[]" multiple>
                    <?php
                    // Hiển thị các lựa chọn trong dropdown
                    foreach ($mktContacts as $mktInfo):
                        $selected = in_array($mktInfo->id, $selectedMKTContacts) ? 'selected' : '';
                        ?>
                        <option value="<?= $mktInfo->id ?>" <?= $selected ?>><?= $mktInfo->name ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <!-- HTML cho dropdown với các tùy chọn lấy từ CSV -->
            <th><label for="receiver_contact_field">Người nhận thông tin</label></th>
            <td>
                <select id="receiver_contact_field" name="receiver_contact_field[]" multiple>
                    <?php
                    // Hiển thị các lựa chọn trong dropdown
                    foreach ($receiverContacts as $receiver):
                        $selected = in_array($receiver->id, $selectedReceiverContacts) ? 'selected' : '';
                        ?>
                        <option value="<?= $receiver->id ?>" <?= $selected ?>><?= $receiver->name ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr class="form_item_div">
            <th>
                <label for="restaurant_field">Nhà hàng áp dụng</label><span class="text-danger"> *</span>
            </th>
            <td>

                <div class="dropdown ">
                    <input type="text" readonly placeholder="Chọn nhà hàng áp dụng"
                           class=" dropdown-toggle btn-restaurant" id="dropdownMenuButton" data-bs-toggle="dropdown"
                           data-bs-auto-close="outside" aria-expanded="false">

                    <!-- Dropdown Content -->
                    <ul class="dropdown-menu restaurant_dropdown" aria-labelledby="dropdownMenuButton">
                        <?php foreach ($regionBrand as $region): ?>
                            <?php $regionName = $region['region_name'] ?? "" ?>
                            <li class="region_li">
                                <div class="region_item">
                                    <svg class="toggle_arrow" width="6" height="10" viewBox="0 0 6 10" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.73779 5.02434L1.08936 1.01556C0.916016 0.86663 0.662109 1.00091 0.662109 1.24139V9.25896C0.662109 9.49944 0.916016 9.63372 1.08936 9.48479L5.73779 5.476C5.87085 5.36126 5.87085 5.13909 5.73779 5.02434Z"
                                              fill="black" fill-opacity="0.85"/>
                                    </svg>
                                    <input type="checkbox" class="region_checkbox" value="<?= $regionName ?>"
                                           name="region[<?= $regionName ?>]" <?php if (isset($regionBrandSelected[$regionName])): ?> checked <?php endif; ?> />
                                    <label>
                                        <?= RegionMapping::MAP[$regionName] ?>
                                    </label>
                                </div>
                                <ul class="brand_checkbox_ul">
                                    <?php foreach ($region['brands'] as $brand): ?>
                                        <?php $brandId = $brand['brand_id'] ?? '' ?>
                                        <li class="brand_li">
                                            <div class="brand_item">
                                                <svg class="toggle_arrow" width="6" height="10" viewBox="0 0 6 10"
                                                     fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.73779 5.02434L1.08936 1.01556C0.916016 0.86663 0.662109 1.00091 0.662109 1.24139V9.25896C0.662109 9.49944 0.916016 9.63372 1.08936 9.48479L5.73779 5.476C5.87085 5.36126 5.87085 5.13909 5.73779 5.02434Z"
                                                          fill="black" fill-opacity="0.85"/>
                                                </svg>
                                                <input type="checkbox" class="brand_checkbox"
                                                       name="region[<?= $regionName ?>][<?= $brandId ?>]"
                                                       value="<?= $brandId ?>"
                                                       <?php if (isset($regionBrandSelected[$regionName][$brandId])): ?>checked <?php endif; ?>>
                                                <label>
                                                    <?= $brand['brand_name'] ?>
                                                </label>
                                            </div>


                                            <ul class="restaurant_checkbox_ul">

                                                <?php
                                                $restaurantOfBrand = array_filter($allRestaurant, function ($item) use ($brandId, $regionName) {
                                                    return $item->brand_id === $brandId && $item->region_name === $regionName;
                                                });
                                                $selectedRestaurant = isset($regionBrandSelected[$regionName][$brandId]) ? $regionBrandSelected[$regionName][$brandId] : [];

                                                ?>
                                                <input class="hidden <?= str_replace(' ', '', $regionName) . '_' . $brandId ?> input_restaurant_after_sync"
                                                       type="text" value="">
                                                <?php foreach ($restaurantOfBrand as $restaurant): ?>
                                                    <li class="restaurant_checkbox_li">
                                                        <input id="<?= "restaurant_" . $restaurant->res_code ?>"
                                                               class="restaurant_checkbox"
                                                               type="checkbox"
                                                               value="<?= $restaurant->res_code ?>"
                                                               name="<?= "restaurants[]" ?>"
                                                               <?php if (in_array($restaurant->res_code, $selectedRestaurant)): ?>checked <?php endif; ?>
                                                        >
                                                        <label><?= $restaurant->name ?></label>
                                                    </li>
                                                <?php endforeach; ?>

                                            </ul>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                <label for="upload-pdf">Upload PDF</label>
            </th>
            <td>
                <input type="file" name="upload_pdf" accept="application/pdf" id="upload-pdf"">

                <?php
                $pdfFile = $fields['ctkm_pdf_file'] ?? null;
                $pdfPath = wp_upload_dir()['baseurl'] . CtkmConsts::PDF_UPLOAD_PATH . $pdfFile;
                ?>

                <?php if ($pdfFile): ?>
                    <div class="down-file-wrap">
                        <a id="pdf_link" class="down-file" href="<?= $pdfPath ?>" download>
                            <?php echo $pdfFile; ?>
                            <img src="<?= plugin_dir_url(__FILE__) . 'images/Vector.svg'; ?>" alt="Download Icon"
                                 style="display: inline; margin-left: 8px;">
                        </a>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none"
                             class="delete-pdf-icon icon-red">
                            <path d="M3.62476 1.87402H3.49976C3.56851 1.87402 3.62476 1.81777 3.62476 1.74902V1.87402H8.37476V1.74902C8.37476 1.81777 8.43101 1.87402 8.49976 1.87402H8.37476V2.99902H9.49976V1.74902C9.49976 1.19746 9.05132 0.749023 8.49976 0.749023H3.49976C2.94819 0.749023 2.49976 1.19746 2.49976 1.74902V2.99902H3.62476V1.87402ZM11.4998 2.99902H0.499756C0.223193 2.99902 -0.000244141 3.22246 -0.000244141 3.49902V3.99902C-0.000244141 4.06777 0.0560059 4.12402 0.124756 4.12402H1.06851L1.45444 12.2959C1.47944 12.8287 1.92007 13.249 2.45288 13.249H9.54663C10.081 13.249 10.5201 12.8303 10.5451 12.2959L10.931 4.12402H11.8748C11.9435 4.12402 11.9998 4.06777 11.9998 3.99902V3.49902C11.9998 3.22246 11.7763 2.99902 11.4998 2.99902ZM9.42632 12.124H2.57319L2.19507 4.12402H9.80445L9.42632 12.124Z"
                                  fill="black" fill-opacity="0.45"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <input type="hidden" id="is_end_before_field" name="is_end_before_field"
               value="<?php echo !empty($fields['is_end_before']) ? esc_attr($fields['is_end_before']) : '0'; ?>"/>
        <input type="hidden" id="active_field" name="active_field"
               value="<?php echo !empty($fields['active']) ? esc_attr($fields['active']) : '1'; ?>"/>
        <input type="hidden" name="delete_pdf" id="delete_pdf">
        <!--    <button id="taobang">-->
        <!--        Tao bang-->
        <!--    </button>-->
        <!---->
        <!--    <button id="luudata">-->
        <!--        luu data-->
        <!--    </button>-->
        <!--    <button id="syncRM">-->
        <!--        Sync RM data-->
        <!--    </button>-->
    </table>
<?php if (isset($_GET['action']) && $_GET['action'] === 'edit'): ?>
    <?php $ctkmId = esc_attr($fields['ctkm_id']) ?>
    <div>
        <a href="<?= admin_url("admin.php?page=ctkm-notification-history&ctkm_id=$ctkmId") ?>">Lịch sử gửi thông báo</a>
    </div>
<?php endif; ?>