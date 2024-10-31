<?php
/*
Plugin Name: PDF Invoice for Woocommerce
Plugin URI: https://wpexpertshub.com
Description: Automatically generate and attach customizable PDF Invoices to WooCommerce order emails.
Author: WpExperts Hub
Version: 1.2
Author URI: https://wpexpertshub.com
Text Domain: wphub-wc-invoice
License: GPLv3
WC requires at least: 5.6
WC tested up to: 8.6
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
*/

defined( 'ABSPATH' ) || exit;

class WEXP_Hub_PDF_Invoice{

	protected $settings = array();
	protected static $_instance = null;

	public static function instance(){

		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function __construct(){

		if(!defined('WPHUB_INV_VER')){
			define('WPHUB_INV_VER',1.2);
		}

		register_activation_hook(__FILE__,array($this,'wphub_invoice_activated'));
		add_filter('plugin_action_links_'.plugin_basename(__FILE__),array($this,'wc_invoice_wphub_action_links'),10,1);
		add_action('init',array($this,'load_text_domain'));
		add_action('init',array($this,'init_autoload'));
		add_action('init',array($this,'autoload_classes'));
		add_action('woocommerce_loaded',array($this,'load_settings'));
		add_action('admin_enqueue_scripts',array($this,'admin_scripts'),999);
		add_action('wp_ajax_wphub-preview',array($this,'pdf_preview'));
		add_action('woocommerce_order_status_changed',array($this,'check_status'),999,4);
		add_filter('woocommerce_email_attachments',array($this,'invoice_attachments'),999,4);
		add_filter('woocommerce_checkout_fields',array($this,'add_checkout_fields'),10,1);
		add_action('woocommerce_checkout_order_processed',array($this,'save_checkout_fields'),10,3);
		add_action('woocommerce_admin_field_wphub_invoice_setting',array($this,'invoice_setting_fields'),10,1);
		add_action('woocommerce_update_options_wphub_invoice',array($this,'invoice_save_fields'),10);
		add_action('before_woocommerce_init',array($this,'hpos_compatibility'));
	}

	function hpos_compatibility(){
		if(class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)){
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables',__FILE__,true);
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('analytics',__FILE__,true);
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('new_navigation',__FILE__,true);
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('product_block_editor',__FILE__,true);
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks',__FILE__,true);
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('marketplace',__FILE__,true);
		}
	}  

	function wphub_invoice_activated(){
		$upload_dir = wp_upload_dir();
		if(!is_dir($upload_dir['basedir'].'/wphub-invoice')){
			mkdir($upload_dir['basedir'].'/wphub-invoice',0777);
		}
	}

	function wc_invoice_wphub_action_links($links){
		$wxp_link = array(
			'<a href="'.admin_url('admin.php?page=wc-settings&tab=wphub_invoice').'">'.__('Settings','wphub-wc-invoice').'</a>'
		);
		return array_merge($links,$wxp_link);
	}

	function load_text_domain(){

		if(function_exists('determine_locale')){
			$locale = determine_locale();
		} else {
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		load_textdomain('wphub-wc-invoice',dirname(__FILE__).'/lang/wphub-wc-invoice-'.$locale.'.mo');
		load_plugin_textdomain('wphub-wc-invoice',false,basename(dirname(__FILE__)).'/lang');
	}

	function init_autoload(){
		spl_autoload_register(function($class){
			$class = strtolower($class);
			$class = str_replace('_','-',$class);
			if(is_file(dirname(__FILE__).'/classes/'.$class.'.php')) {
				include_once('classes/'.$class.'.php');
			}
		});
	}

	function admin_scripts(){
		$screen = get_current_screen();
		if(isset($screen->id) && $screen->id=='woocommerce_page_wc-settings' && isset($_REQUEST['tab']) && $_REQUEST['tab']=='wphub_invoice'){
			wp_register_script('invoice-preview',$this->plugin_url().'/assets/js/preview.js',array('jquery'),WPHUB_INV_VER,true);
			$translation_array = array(
				'ajax' => wp_nonce_url(add_query_arg('action','wphub-preview',admin_url('admin-ajax.php')),'wphub-preview','invoice-pdf-preview')
			);
			wp_localize_script('invoice-preview','wphub_pdf',$translation_array);
			wp_enqueue_script('invoice-preview');
		}
	}

	function load_settings(){
		$this->settings = get_option('_wphub_invoice');
	}

	function plugin_url(){
		return untrailingslashit(plugins_url('/', __FILE__ ));
	}

	function plugin_path(){
		return untrailingslashit(plugin_dir_path(__FILE__));
	}

	function autoload_classes(){
		$settings = new WEXP_Hub_Invoice_Settings();
		$settings->init();
	}

	function get_settings(){
		$this->settings['template'] = isset($this->settings['template']) ? $this->settings['template'] : 'template-1';
		$this->settings['generate_invoice'] = isset($this->settings['generate_invoice']) ? $this->settings['generate_invoice'] : array('processing');
		$this->settings['paid_invoice'] = isset($this->settings['paid_invoice']) ? $this->settings['paid_invoice'] : array('processing');
		$this->settings['emails'] = isset($this->settings['emails']) ? $this->settings['emails'] : array('new_order','customer_processing_order','customer_invoice','customer_completed_order');
		$this->settings['footer_note'] = isset($this->settings['footer_note']) ? $this->settings['footer_note'] : __('Thank you for your order.','wphub-wc-invoice');
		$this->settings['copyright_info'] = isset($this->settings['copyright_info']) ? $this->settings['copyright_info'] : __('© Copyright {your_company_name} {current_year}, All Rights Reserved.','wphub-wc-invoice');
		return map_deep($this->settings,'sanitize_textarea_field');
	}

	function get_status_key($status){
		$status   = 'wc-' === substr($status,0,3) ? substr($status,3) : $status;
		return $status;
	}


	function check_status($order_id,$status_from,$status_to,$order){
		$invoice_settings = $this->get_settings();
		if(isset($invoice_settings['generate_invoice']) && is_array($invoice_settings['generate_invoice']) && in_array($status_to,$invoice_settings['generate_invoice'])){
			$this->generate_invoice($order,$invoice_settings);
		}
		elseif(isset($invoice_settings['paid_invoice']) && is_array($invoice_settings['paid_invoice']) && in_array($status_to,$invoice_settings['paid_invoice'])){
			$this->generate_invoice($order,$invoice_settings);
		}
	}

	function generate_invoice($order,$invoice_settings){
		if(!is_a($order,'WC_Order')){
			return;
		}
		$invoice = new WEXP_Hub_Invoice($invoice_settings);
		do_action('wphub_before_pdf_invoice',$order);
		$invoice->dump_invoice($order);
		do_action('wphub_after_pdf_invoice',$order);
	}

	function invoice_attachments($attachments,$email_id,$order,$email){
		$invoice_settings = $this->get_settings();
		if(isset($invoice_settings['emails']) && is_array($invoice_settings['emails']) && in_array($email_id,$invoice_settings['emails'])){
			$invoice_settings = $this->get_settings();
			$invoice = new WEXP_Hub_Invoice($invoice_settings);
			$attachments[] = $invoice->get_invoice_path($order);
		}
		return $attachments;
	}

	function add_checkout_fields($fields){
		$invoice_settings = $this->get_settings();
		if(isset($invoice_settings['vat_number']) && $invoice_settings['vat_number']){
			$fields['billing']['company_vat'] = array(
				'label'=>__('VAT Number','wphub-wc-invoice'),
				'class'=>array('form-row-wide'),
				'required'=>false,
				'priority'=>130,
			);
		}
		return $fields;
	}

	function save_checkout_fields($order_id,$posted_data,$order){
		if(isset($posted_data['company_vat'])){
			$order->update_meta_data('wphub_company_vat',sanitize_text_field($posted_data['company_vat']));
			$order->save();
		}
	}

	function invoice_setting_fields($value){
		include(dirname(__FILE__).'/includes/setting-fields.php');
	}

	function invoice_save_fields(){
		if(isset($_POST['wphub-invoice'])){
			$options = map_deep($_POST['wphub-invoice'],'sanitize_textarea_field');
			update_option('_wphub_invoice',$options,'no');
			$this->load_settings();
		}
	}

	function get_value($key,$type=''){
		$values = $this->get_settings();
		$value = isset($values[$key]) ? $values[$key] : '';
		return $type=='checkbox' && $value==1 ? 'yes' : $value;
	}

	function pdf_preview(){
		$nonce = isset($_REQUEST['invoice-pdf-preview']) ? sanitize_text_field($_REQUEST['invoice-pdf-preview']) : '';
		if(wp_verify_nonce($nonce,'wphub-preview')){
			$template = isset($_REQUEST['template']) ? sanitize_text_field($_REQUEST['template']) : 'template-1';
			$invoice_settings = $this->get_settings();
			$invoice = new WEXP_Hub_Invoice($invoice_settings);
			$invoice->preview_invoice($template);
		}
	}

}

function wexphub_invoice(){
	return WEXP_Hub_PDF_Invoice::instance();
}

if(function_exists('is_multisite') && is_multisite()){
	if(!function_exists( 'is_plugin_active_for_network')){
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}
	if(is_plugin_active_for_network('woocommerce/woocommerce.php')){
		wexphub_invoice();
	}
}
elseif(in_array('woocommerce/woocommerce.php',apply_filters('active_plugins',get_option('active_plugins')))){
	wexphub_invoice();
}