<div class="wrap">
    <h1>Danh sách chương trình khuyến mãi</h1>
    <table class="widefat">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($campaigns)): ?>
            <tr>
                <td colspan="2">Chưa có campaign</td>
            </tr>
        <?php else: ?>
            <?php foreach ($campaigns as $c): ?>
                <tr>
                    <td><?php echo esc_html($c->ID); ?></td>
                    <td><?php echo esc_html($c->post_title); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
