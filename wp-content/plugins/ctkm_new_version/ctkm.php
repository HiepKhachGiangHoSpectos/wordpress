<?php
/**
 * Plugin Name: CTKM Plugin
 * Description: Plugin quản lý chương trình khuyến mãi.
 * Version: 1.0.0
 * Author: Công ty bạn
 */

use CTKM\Admin\AdminService;

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/vendor/autoload.php'; // composer autoload nếu có

CTKM\Plugin::get_instance(__FILE__)->run();
