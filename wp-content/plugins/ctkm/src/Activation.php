<?php
namespace CTKM;

class Activation {
    // (Trước kia: function activate_ctkm_plugin)
    public static function activate() {
        // Tạo table, default options nếu cần
        flush_rewrite_rules();
    }

    // (Trước kia: function deactivate_ctkm_plugin)
    public static function deactivate() {
        // Cleanup nếu cần
        flush_rewrite_rules();
    }
}
