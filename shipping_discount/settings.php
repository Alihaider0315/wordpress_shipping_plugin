<?php
/**
 * Shipping method settings for My Shipping Plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

return array(
    'enabled' => array(
        'title'   => __('Enable', 'my-shipping-plugin'),
        'type'    => 'checkbox',
        'label'   => __('Enable this shipping method', 'my-shipping-plugin'),
        'default' => 'yes',
    ),
    'title' => array(
        'title'       => __('Method Title', 'my-shipping-plugin'),
        'type'        => 'text',
        'description' => __('Enter a custom title for this shipping method', 'my-shipping-plugin'),
        'default'     => __('My Shipping Method', 'my-shipping-plugin'),
        'desc_tip'    => true,
    ),
);
