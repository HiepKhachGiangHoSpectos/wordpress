<div class="wrap">
    <h1 class="wp-heading-inline">Danh sách chương trình khuyến mãi</h1>
    <a href="?page=ctkm_create" class="page-title-action">Thêm mới</a>
    <hr class="wp-header-end">

    <table class="widefat fixed striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên chương trình</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($campaigns)) : ?>
            <?php foreach ($campaigns as $campaign) : ?>
                <tr>
                    <td><?php echo esc_html($campaign->id); ?></td>
                    <td><?php echo esc_html($campaign->name); ?></td>
                    <td><?php echo esc_html($campaign->start_date); ?></td>
                    <td><?php echo esc_html($campaign->end_date); ?></td>
                    <td>
                        <?php echo $campaign->status == 1 ? 'Đang chạy' : 'Ngừng'; ?>
                    </td>
                    <td>
                        <a href="?page=ctkm-edit&id=<?php echo $campaign->id; ?>">Sửa</a> |
                        <a href="?page=ctkm-delete&id=<?php echo $campaign->id; ?>"
                           onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6">Chưa có CTKM nào</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
