jQuery(document).ready(function ($) {
    let debounceTimer;
    $('#save-post').val('Lưu Nháp');
    const $publishButton = $('#publish');
    // $publishButton.removeAttr('type');
    // $publishButton.attr('type', 'button');


    function customConfirm(message, callback) {
        $('#custom-confirm').removeClass('hidden');
        $('#custom-confirm .modal-content p').text(message);
    
        $('#confirm-ok').off('click').on('click', function () {
            $('#custom-confirm').addClass('hidden');
            callback(true);
        });
    
        $('#confirm-cancel').off('click').on('click', function () {
            $('#custom-confirm').addClass('hidden');
            callback(false);
        });
    }
    
    function customAlert(message) {
        $('#custom-alert').removeClass('hidden');
        $('#alert-message').text(message);
    
        $('#alert-ok').off('click').on('click', function () {
            $('#custom-alert').addClass('hidden');
        });
    }

    $('.delete-pdf-icon').on('click', function () {
        const pdfFile = $('#pdf_link').text().trim()
        $('.down-file-wrap').hide()
        $('#delete_pdf').val(pdfFile)
    })

    $('#synchronize').click(function () {
      customConfirm('Đồng bộ sẽ ghi đè dữ liệu hiện tại, bạn có chắc chắn muốn đồng bộ không?', function (confirmed) {
        if (!confirmed) return;

        const action = $('#ctkm_source').val();
        const id = $('#ctkm_id_field').val();
        var detailAction = 'get_detail_tgs_data';
        if (action == 'get_ecom_data') {
            detailAction = 'get_detail_ecom_data';
        }

        $.ajax({
            url: ajax_url,
            method: 'POST',
            data: {
                action: detailAction,
                ctkmId: id
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var ctkm = response.data;
                    if (ctkm.data){
                        synchronize(ctkm.data, action, true);
                        displaySelectedRestaurant();
                    } else {
                        $('#promotion_status_field').val("Dừng trước hạn")
                        $('#is_end_before_field').val(1)
                    }
                    customAlert('Đồng bộ thành công!');

                } else {
                    console.error('Error fetching ctkm:', response.data);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching ctkm:', error);
            }
        });
      });
    });

    $('input, select, textarea').on('input change', function() {
        var input = $(this);
        // Kiểm tra nếu trường không hợp lệ, ẩn thông báo lỗi
        if (input.val() !== '') {
            input.next('.error-message').fadeOut(); // Ẩn thông báo lỗi
        }
        if(input.attr('type') === 'date'){
            input.parent().next('.error-message').fadeOut();
        }
    });

    function validateTextInput(){
        let result = true;
        const allTextInputs = $('.text_input');
        allTextInputs.each(function (index, item) {
            if (!$(item).val() || $(item).val().trim() === '') {
                result = false;
                $(item).after(`<div class="error-message" >Trường này là bắt buộc</div>`)
                if ( !focusItem) {
                    focusItem = $(item)
                }

            }
        })
        return result;
    }

    function validateDate(){
        const startDate = $('#start_date_field').val();
        const endDate = $('#end_date_field').val();
        if(startDate === '' || endDate === ''){
            if (!focusItem) {
                if(startDate === ''){
                    focusItem = $('#start_date_field');
                } else {
                    focusItem = $('#end_date_field');
                }
            }
            $('.date-range').after(`<div class="error-message" >Trường này là bắt buộc</div>`)
            return false;
        }
        return true;
    }

    function validateRestaurantData() {
        if($('.restaurant_checkbox_ul input[type="checkbox"]:checked').length > 0){
            return true
        }
        $('.btn-restaurant').after(`<div class="error-message" >Trường này là bắt buộc</div>`);
        if (!focusItem) {
            focusItem = $('.btn-restaurant');
        }
        return false;
    }

    function validateEditor(){
        let tinyMCEIds = [
            'promotion_content_field',
            'promotion_summary_field',
            'apply_condition_field',
            'guideline_field',
        ];
        let result = true;

        $.each(tinyMCEIds, function(key, tinyId) {
            const content = tinymce.get(tinyId)?.getContent({ format: 'text' })?.trim();
            if (tinyMCE && content === '' || !content) {
                result = false;
                $('#wp-' + tinyId + '-wrap').after('<div class="error-message"><span>Trường này là bắt buộc</span></div>');
                if (!focusItem) {
                    focusItem = tinyMCE.get(tinyId)
                }
            }
        })
        return result;
    }

    function validateMultiSelect(){
        let result = true;
        const allTextInputs = $('.multi_select');
        allTextInputs.each(function (index, item) {

            if ($(item).val().length === 0) {
                result = false;
                $(item).parent().find(`.select2-container`).after(`<div class="error-message" >Trường này là bắt buộc</div>`);

                const textarea = $(item).parent().find('.select2-search__field');
                if (!focusItem) {
                    focusItem = $(textarea); // Thêm class để tránh focus nhiều lần
                }
            }
        })
        return result;
    }

    let focusItem = null;

    $publishButton.on('click', function (e) {
         // Ngăn form submit mặc định
        let isValid = true;

        const formId = $('#post');

        $('.form_item_div').find('.error-message').remove();
        focusItem = null;
        const validateText = validateTextInput();
        const validateDateResult = validateDate();
        const validateEditorResult = validateEditor();
        const validateMulti = validateMultiSelect();
        const validateRestaurant = validateRestaurantData();
        if (!$(focusItem).hasClass('focused')) {
            $(focusItem).focus().addClass('focused'); // Thêm class để tránh focus nhiều lần
        }

        if (validateText && validateMulti && validateRestaurant && validateDateResult && validateEditorResult) {
            
        } else {
            // Ngăn submit nếu không hợp lệ
            e.preventDefault();
        }
    })

    function setPromotionStatus(startDateStr, endDateStr) {
        var currentDate = new Date();
        var startDate = new Date(startDateStr);
        var endDate = new Date(endDateStr);

        if (startDate > currentDate) {
            if (subtractDays(startDate, 3) <= currentDate) {
                return 'coming_soon';
            }
            return 'chua_dien_ra';
        } else if (startDate <= currentDate && endDate > currentDate) {
            if (subtractDays(endDate, 3) <= currentDate) {
                return 'expire_soon';
            }
            return 'in_progress';
        } else if (currentDate > endDate) {
            return 'expired';
        }
        return 'stop_early';
    }
    
    // Hàm helper trừ số ngày
    function subtractDays(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() - days);
        return result;
    }

    $('#luudata').click(function (e){
        e.preventDefault()
        luuData();
    })

    $('#taobang').click(function (e){
        e.preventDefault()
        createTable();
    })

    $('#ctkm_api').on('input', function () {
        clearTimeout(debounceTimer); // Xóa timer trước đó (nếu có)
        var searchTerm = $(this).val().toLowerCase();
        debounceTimer = setTimeout(() => {
            fetchCtkm(searchTerm); // Gọi API sau khi ngừng nhập 300ms
        }, 700); // 300ms là thời gian chờ
    });

    $('.input_restaurant_after_sync').on('change', function (e) {
        let resIds = $(this).val();
        let resIdsArr = resIds.split(',');

        // Tìm tất cả các input trong restaurant_checkbox_li
        let $allInputs = $(this).closest('.restaurant_checkbox_ul').find('.restaurant_checkbox_li input[type="checkbox"]');

        // Duyệt qua từng input và kiểm tra giá trị của nó
        $allInputs.each(function () {
            let restaurantId = $(this).val(); // Lấy giá trị của input checkbox hiện tại

            // Nếu giá trị nằm trong mảng resIdsArr thì thêm checked, ngược lại bỏ checked
            if (resIdsArr.includes(restaurantId)) {
                $(this).prop('checked', true); // Đánh dấu checkbox
            } else {
                $(this).prop('checked', false); // Bỏ đánh dấu checkbox nếu không nằm trong mảng
            }
        });
    });

    function createTable(){
        $.ajax({
            url: ajax_url,
            method: 'POST',
            data: {
                action: 'create_nha_hang_table', // Tên action đã định nghĩa trong PHP
            },
            dataType: 'json',
        })
    }

    function luuData(){
        $.ajax({
            url: ajax_url,
            method: 'GET',
            data: {
                action: 'create_nha_hang_data', // Tên action đã định nghĩa trong PHP
            },
            dataType: 'json',
        })
    }
    function fetchCtkm(){
        const action = $('#source_api_select').val();
        const searchTerm = $('#ctkm_api').val().toLowerCase();
        if (!searchTerm.trim()) {
            updateCtkmList([]);
            return;
        }
        $.ajax({
            url: ajax_url,
            method: 'GET',
            data: {
                action: action,
                searchTerm: searchTerm
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var ctkms = response.data;
                    updateCtkmList(ctkms, action);
                    if (ctkms.length === 0) {
                        $('#ctkm_api').after('<div class="error-message">Không có CTKM khả dụng</div>');
                    }
                } else {
                    updateCtkmList([], action);
                    $('#ctkm_api').after('<div class="error-message">Không có CTKM khả dụng</div>');
                    console.error('Error fetching ctkm:', response.data);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching ctkm:', error);
            }
        });
    }

    function updateCtkmList(ctkm, action) {
        let $ctkmList = $('#dropdownMenu');
        $ctkmList.empty();

        if (ctkm.length > 0) {
            ctkm.forEach(function (item) {
                $ctkmList.append(
                    $('<div>', {
                        text: item.title,
                        click: function () {
                            $(this).addClass('selected');
                            updateSelectedCtkm(item, action);
                        }
                    })

                );
            });
            $('#dropdownMenu').show(); // Hiển thị danh sách kết quả
        } else {
            $('#dropdownMenu').hide(); // Ẩn danh sách nếu không có kết quả
        }
    }

    function updateSelectedCtkm(item, action) {
        $('#dropdownMenu').hide(); // Ẩn danh sách kết quả
        let ajaxDetailAction = 'get_detail_ecom_data';
        if(action === 'get_tgs_data'){
            ajaxDetailAction = 'get_detail_tgs_data';
        }
        $.ajax({
            url: ajax_url,
            method: 'POST',
            data: {
                action: ajaxDetailAction,
                ctkmId: item.id
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let ctkm = response.data;
                    synchronize(ctkm.data, action);
                    displaySelectedRestaurant();
                    console.log("Hide dropdown");
                } else {
                    console.error('Error fetching ctkm:', response.data);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching ctkm:', error);
            }
        });
    }

    function fillRestaurantData(restaurantData){
        for(const key in restaurantData){
            const result = restaurantData[key].join(',');
            const $restaurantCheckbox = $(`.${key}`);
            $restaurantCheckbox.val(result).trigger('change');
            $restaurantCheckbox.parents('.brand_li').find('.brand_checkbox').prop('checked', true);
            $restaurantCheckbox.parents('.region_li').find('.region_checkbox').prop('checked', true);
        }
    }

    function resetForm() {
        // Reset các input và textarea về rỗng
        const source = $('#ctkm_source').val();
        $('#ctkm_source').val('');
        $('#ctkm_id_field').val('');
        $('#promotion_name_field').val('');
        $('#promotion_status_field').val('');
        $('#start_date_field').val('');
        $('#end_date_field').val('');

        // Reset TinyMCE editors về rỗng
        if (tinymce.get('apply_condition_field')) {
            tinymce.get('apply_condition_field').setContent('');
        }
        if(source == 'get_tgs_data'){
            if (tinymce.get('promotion_content_field')) {
                tinymce.get('promotion_content_field').setContent('');
            }
        }
        else{
            $('#promotion_code_field').val('');
        }

        if (tinymce.get('promotion_summary_field')) {
            tinymce.get('promotion_summary_field').setContent('');
        }

        resetAllCheckboxes()
    }

    function resetAllCheckboxes() {
        // Reset tất cả các checkbox có liên quan
        $('.input_restaurant_after_sync').val('');
        $('.btn-restaurant').val('');
        $('.region_checkbox, .brand_checkbox, .restaurant_checkbox_li input[type="checkbox"]').prop('checked', false);
    }

    function synchronize(ctkm, action, isEdit = false){
        if (ctkm){
            activeStatus = 1
            if (action === 'get_tgs_data'){
                activeStatus = 2
            } else if (action === 'get_ecom_data'){
                activeStatus = 1
            }
            resetForm();
            $('#ctkm_api').val(ctkm.name);
            $('#ctkm_source').val(action)
            $('#ctkm_id_field').val(ctkm.id)
            $('#promotion_name_field').val(ctkm.name)
            $('#start_date_field').val(ctkm.startDate)
            if (isEdit && action === 'get_tgs_data' && ctkm.status == 5){
                // Status = Recalled, isDeleted = false
                if (ctkm.isDeleted === false) {
                    $('#end_date_field').val(ctkm.endDate)
                    $('#promotion_status_field').val(calculatePromotionStatus(ctkm.startDate, ctkm.endDate))
                    $('#is_end_before_field').val(0)
                } 
                // Status = Recalled, isDeleted = true
                else {
                    $('#end_date_field').val(getCurrentDate());
                    $('#promotion_status_field').val("Dừng trước hạn")
                    $('#is_end_before_field').val(1)
                }
            } else {
                $('#end_date_field').val(ctkm.endDate)
                $('#promotion_status_field').val(calculatePromotionStatus(ctkm.startDate, ctkm.endDate, ctkm.status == activeStatus))
                $('#is_end_before_field').val(isCtkmEndBefore(ctkm.endDate, ctkm.status == activeStatus))
            }
            $('#active_field').val("1")
            tinymce.get('apply_condition_field').setContent(ctkm.condition ?? '');
            if (action == 'get_tgs_data'){
                tinymce.get('promotion_content_field').setContent(ctkm.promotionContent ?? '');
            } else{
                $('#promotion_code_field').val(ctkm.promoCode)
            }
            tinymce.get('promotion_summary_field').setContent(ctkm.promotionSummary ?? '');
            fillRestaurantData(ctkm.restaurants)
        }
    }

    function getCurrentDate() {
        const now = new Date(); // Tự động lấy đúng giờ hệ thống
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
    
        return `${year}-${month}-${day}`;
    }    

    function calculatePromotionStatus(startDate, endDate, isPublished = true, warningDays = 3) {
        const currentDate = new Date(); // Ngày hiện tại
        const start = new Date(startDate); // Chuyển `start_date_field` thành loại Date
        const end = new Date(endDate); // Chuyển `end_date_field` thành loại Date

        // Kiểm tra ngày có hợp lệ không
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
            return ""; // Thông báo lỗi nếu ngày không hợp lệ
        }

        // Logic xác định trạng thái
        if (!isPublished) {
            // CTKM không ở trạng thái published => "Dừng trước hạn"
            return "Dừng trước hạn";
        }

        if (currentDate < start) {
            return "Sắp diễn ra"; // Ngày hiện tại trước ngày bắt đầu
        } else if (currentDate >= start && currentDate < end) {
            // Ngày hiện tại nằm trong thời gian hiệu lực
            const differenceToEnd = Math.ceil((end - currentDate) / (1000 * 60 * 60 * 24)); // Số ngày còn lại đến ngày kết thúc
            if (differenceToEnd <= warningDays) {
                return "Sắp hết hạn"; // Nếu ngày hiện tại còn lại <= số ngày "warningDays" (mặc định là 3)
            }
            return "Đang diễn ra"; // Trường hợp không gần hết hạn
        } else if (currentDate > end) {
            return "Đã kết thúc"; // Ngày hiện tại sau ngày kết thúc
        }

        return "Không xác định"; // Phòng trường hợp không rơi vào logic nào
    }

    function isCtkmEndBefore(endDate, isPublished = true) {
        const currentDate = new Date(); // Ngày hiện tại
        const end = new Date(endDate); // Chuyển `end_date_field` thành loại Date
        if (!isPublished & currentDate < end){   
            return 1;
        }
        return 0;
    }

    // Lắng nghe sự kiện nhập liệu trên ô tìm kiếm
    $('#nhahang_search_field').on('input', function () {
        var searchTerm = $(this).val().toLowerCase();
        fetchRestaurants(searchTerm); // Gọi AJAX khi nhập liệu
    });

    // Khi nhấn vào ô tìm kiếm, hiển thị kết quả tìm kiếm
    $('#nhahang_search_field').on('focus', function () {
        var searchTerm = $(this).val().toLowerCase();
        if (searchTerm) {
            fetchRestaurants(searchTerm); // Gọi lại AJAX nếu có nội dung tìm kiếm
        }
        $('#search_results').show(); // Hiển thị danh sách kết quả khi nhấn vào ô tìm kiếm
    });

    // Khi rời khỏi ô tìm kiếm (blur), ẩn danh sách kết quả
    $('#nhahang_search_field').on('blur', function () {
        setTimeout(function () { // Để đảm bảo rằng khi nhấn vào một kết quả sẽ không ẩn danh sách quá sớm
            $('#search_results').hide(); // Ẩn danh sách kết quả
        }, 200);
    });

    $('#source_api_select').change(function(){
        if ($(this).val() == 'get_tgs_data' || $(this).val() == 'get_ecom_data'){
            $('#source-api-result').show()
        } else{
            $('#source-api-result').hide()
        }
        resetForm()
    })

    let startDate = $('#start_date_field').val();
    $('#end_date_field').attr('min', startDate)
    //end_date_field
    $('#start_date_field').change(function () {
        $('#end_date_field').attr('min', $(this).val())
    })

    // Toggle children display
    $('.toggle').click(function () {
        $(this).parent().find('>.children').toggle();
        $(this).text($(this).text() === '▶' ? '▼' : '▶');
    });

    // Checkbox logic: Propagate selection to children
    $('input[type="checkbox"]').change(function () {
        let isChecked = $(this).prop('checked');
        $(this).siblings('.children').find('input[type="checkbox"]').prop('checked', isChecked);
    });

    // Save selection logic
    $('#saveSelection').click(function () {
        let selectedItems = [];

        $('input[type="checkbox"]:checked').each(function () {
            selectedItems.push({
                name: $(this).data('name'),
                level: $(this).data('level'),
            });
        });

        // Display selected items
        let displayText = selectedItems.map(item => `${item.name}`).join(', ');
        $('#selectedValues').val(displayText);

        // Save to local storage or send to server
        console.log("Selected Items:", selectedItems);
    });


    // check box nhà hàng áp dụng
    $('.region_item').click(
        function (e) {
            $(this).parent().find('.toggle_arrow').toggleClass('rotate');
            $(this).parents('.region_li').find('.brand_checkbox_ul').toggle()
        }
    )
    $('.brand_item').click(
        function (e) {
            $(this).parent().find('.toggle_arrow').toggleClass('rotate');
            $(this).parents('.brand_li').find('.restaurant_checkbox_ul').toggle()
        }
    )

    $('.region_checkbox').on('change', function () {
        // Lấy trạng thái của checkbox cha (tick/untick)
        let isChecked = $(this).prop('checked');
        // Tick/untick tất cả checkbox con trong cùng cấp
        $(this).closest('.region_li').find('.brand_checkbox, .restaurant_checkbox_li input').prop('checked', isChecked);
    });

    $('.brand_checkbox').on('change', function () {
        // Lấy trạng thái của checkbox thương hiệu
        let isChecked = $(this).prop('checked');
        // Tick/untick tất cả checkbox con (restaurant)
        $(this).closest('.brand_li').find('.restaurant_checkbox_li input').prop('checked', isChecked);
    });

    $('.region_checkbox').on('click', function (e) {
        e.stopPropagation();
    });

    $('.brand_checkbox').on('click', function (e) {
        e.stopPropagation();
    });

    displaySelectedRestaurant();

    // xử lý hiển thị những nhà hàng được chọn lên thanh input
    $('.region_checkbox, .brand_checkbox, .restaurant_checkbox').on('change', function () {
        displaySelectedRestaurant()

    })

    function displaySelectedRestaurant(){
        let restaurants = $('.restaurant_checkbox:checked').next('label');
        const totalAfterChange = $('.restaurant_checkbox:checked').length;

        let string = '';
        if (totalAfterChange > 3) {
            string = `......+${totalAfterChange - 3}`;
        }
        let firstRestaurants = [];
        for (let i = 0; i < 3; i++){
            if ($(restaurants[i]).text() !== ''){
                firstRestaurants[i] = $(restaurants[i]).text();
            }
        }
        $('.btn-restaurant').val(firstRestaurants.join(', ') + string)
    }

    $('#syncRM').on('click', function (e){
        e.preventDefault();
        $.ajax({
            url: ajax_url,
            method: 'POST',
            data: {
                action: 'sync_rm',
            },
            dataType: 'json',
            success: function (response) {

            },
            error: function (xhr, status, error) {

            }
        });
    })



});