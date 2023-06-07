<?php
/**
 * Plugin Name: Coupon Base Shipping Discount
 * Description: Custom shipping method with discount based on a WooCommerce coupon.
 * Version: 1.0
 * Author: Ali Haider Developer
 */
 

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('woocommerce_cart_calculate_fees', 'dynamic_shipping_discount_apply_discount');
function dynamic_shipping_discount_apply_discount()
{
    $applied_coupons = WC()->cart->get_applied_coupons();
    $coupon_code = get_option('dynamic_shipping_discount_coupon_code');
    $discount = 0;

foreach ($applied_coupons as $code) {
    if (strtolower($code) === strtolower($coupon_code)) {
        $applied_coupon = true;
        $coupon = new WC_Coupon($code);

        if ($coupon->get_discount_type() === 'percent') {
            $discount = $shipping_including_tax * ($coupon->get_amount() / 100);
        } else {
            $discount = $coupon->get_amount();
        }

        break;
    }
}


    if ($applied_coupon) {
        $discount = 0;
        $percentage = floatval(get_option('dynamic_shipping_discount_percentage'));

        $shipping_total = WC()->cart->get_shipping_total();
        $shipping_tax_total = WC()->cart->get_shipping_tax();
        $shipping_including_tax = $shipping_total + $shipping_tax_total;

        if ($shipping_including_tax > 0) {
            $discount = $shipping_including_tax * ($percentage / 100);
            $discount = round($discount, wc_get_price_decimals());
        }

        WC()->cart->add_fee('Shipping Discount', -$discount, true, '');
    }
}



add_action('admin_menu', 'dynamic_shipping_discount_add_settings_page');
function dynamic_shipping_discount_add_settings_page()
{
    add_options_page(
        'Dynamic Shipping Discount',
        'Shipping Discount',
        'manage_options',
        'dynamic-shipping-discount',
        'dynamic_shipping_discount_render_settings_page'
    );
}

function dynamic_shipping_discount_render_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Dynamic Shipping Discount Settings</h1>
        <p>

        <strong>Note : </strong>
        Please follow these steps to create a coupon in WooCommerce: <br><br>

Go to your WordPress admin dashboard.<br><br>
Navigate to "WooCommerce" > "Coupons" in the left-hand menu.<br><br>
Click on the "Add Coupon" button to create a new coupon. <br><br>
Fill in the necessary details for the coupon, including the coupon code. For example, you can use "SAVE50" as the coupon code. <br><br>
Set the desired discount type and amount or percentage in the "General" tab of the coupon settings. <br><br>
Save the coupon. <br><br>
After creating the coupon with the desired code and discount, you can go to the "Shipping Discount" settings page (under "Settings" > "Shipping Discount") and enter the same coupon code in the "Coupon Code" field. Make sure to save the settings.
<br><br>
Now, when the configured coupon code is applied during checkout, the shipping discount will be automatically calculated and displayed based on the discount percentage specified in the settings.
<br><br>
        
        </p>
        <form method="post" action="options.php">
            <?php
            settings_fields('dynamic_shipping_discount_settings');
            do_settings_sections('dynamic_shipping_discount_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'dynamic_shipping_discount_register_settings');
function dynamic_shipping_discount_register_settings()
{
    add_settings_section(
        'dynamic_shipping_discount_section',
        'Coupon Settings',
        'dynamic_shipping_discount_section_callback',
        'dynamic_shipping_discount_settings'
    );

    add_settings_field(
        'dynamic_shipping_discount_coupon_code',
        'Coupon Code',
        'dynamic_shipping_discount_coupon_code_callback',
        'dynamic_shipping_discount_settings',
        'dynamic_shipping_discount_section'
    );

    add_settings_field(
        'dynamic_shipping_discount_percentage',
        'Discount Percentage',
        'dynamic_shipping_discount_percentage_callback',
        'dynamic_shipping_discount_settings',
        'dynamic_shipping_discount_section'
    );

    register_setting('dynamic_shipping_discount_settings', 'dynamic_shipping_discount_coupon_code');
    register_setting('dynamic_shipping_discount_settings', 'dynamic_shipping_discount_percentage');
}

function dynamic_shipping_discount_section_callback()
{
    echo 'Configure the coupon code and discount percentage for the shipping discount.';
}

function dynamic_shipping_discount_coupon_code_callback()
{
    $coupon_code = get_option('dynamic_shipping_discount_coupon_code');
    echo "<input type='text' name='dynamic_shipping_discount_coupon_code' value='$coupon_code' />";
}

function dynamic_shipping_discount_percentage_callback()
{
    $percentage = get_option('dynamic_shipping_discount_percentage');
    echo "<input type='number' name='dynamic_shipping_discount_percentage' value='$percentage' step='0.01' />";
}





?>