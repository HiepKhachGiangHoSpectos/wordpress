<?php
/**
 * Plugin Name: CTKM Plugin
 * Description: Plugin quản lý chương trình khuyến mãi.
 * Version: 1.0.0
 * Author: Công ty bạn
 */

if (!defined('ABSPATH')) exit;

// Autoload class từ composer
require_once __DIR__ . '/vendor/autoload.php';

CTKM\Plugin::get_instance(__FILE__)->run();
