<?php
defined( 'ABSPATH' ) || exit;

class WEXP_Hub_Invoice_Settings{

	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array',__CLASS__.'::add_settings_tab',50);
		add_action( 'woocommerce_settings_tabs_wphub_invoice',__CLASS__ .'::settings_tab');
		add_action( 'woocommerce_update_options_wphub_invoice',__CLASS__ .'::update_settings');
	}

	public static function add_settings_tab($settings_tabs){
		$settings_tabs['wphub_invoice'] = __('Invoice','wphub-wc-invoice');
		return $settings_tabs;
	}

	public static function settings_tab(){
		woocommerce_admin_fields(self::get_settings());
	}

	public static function update_settings(){
		woocommerce_update_options(self::get_settings());
	}

	public static function get_settings(){

		$settings = array(
			array(
				'name'     => 'PDF Invoice for Woocommerce',
				'type'     => 'title',
				'id'       => 'wphub_invoice_section_title'
			),
			array(
				'title'   => __('Invoice Template','wphub-wc-invoice'),
				'desc'    => __('Use template to generate pdf invoice.','wphub-wc-invoice').' <a class="invoice-preview" href="#">Preview</a>',
				'id'      => 'wphub_invoice_template',
				'type'    => 'select',
				'default' => 'no',
				'autoload'=> false,
				'desc_tip'=> false,
				'field_name' => 'wphub-invoice[template]',
				'options' => array('template-1'=>'Template 1 (A4 Size)'),
				'css'     => 'width:80%;max-width:80%;',
				'value'   => 'template-1'
			),
			array(
				'title'   => __('Sequential Invoice Number','wphub-wc-invoice'),
				'desc'    => __('generate sequential invoice number for PDF.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_number',
				'type'    => 'checkbox',
				'default' => 'no',
				'autoload'=> false,
				'desc_tip'=> false,
				'field_name' => 'wphub-invoice[sequential_number]',
				'value'   => wexphub_invoice()->get_value('sequential_number','checkbox'),
			),
			array(
				'title'   => __('Invoice Number Start','wphub-wc-invoice'),
				'desc'    => __('Start sequential invoice number from.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_number_start',
				'type'    => 'text',
				'default' => '4500',
				'css'     => 'width:80%;',
				'autoload'=> false,
				'desc_tip'=> true,
				'field_name' => 'wphub-invoice[start_number]',
				'value'   => wexphub_invoice()->get_value('start_number'),
			),
			array(
				'title'   => __('Add VAT Number','wphub-wc-invoice'),
				'desc'    => __('Add VAT number Input on Checkout Page.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_vat_input',
				'type'    => 'checkbox',
				'default' => 'no',
				'autoload'=> false,
				'desc_tip'=> false,
				'field_name' => 'wphub-invoice[vat_number]',
				'value'   => wexphub_invoice()->get_value('vat_number','checkbox'),
			),
			array(
				'type'     => 'wphub_invoice_setting',
				'id'       => 'wphub_invoice_setting'
			),
			array(
				'title'   => __('Your Company Logo','wphub-wc-invoice'),
				'desc'    => __('URL to an image you want to display as logo in PDF Invoice. Upload images using the media uploader (Admin > Media).','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_logo_image',
				'type'    => 'text',
				'css'     => 'width:80%;',
				'default' => '',
				'autoload'=> false,
				'desc_tip'=> true,
				'field_name' => 'wphub-invoice[logo]',
				'value'   => wexphub_invoice()->get_value('logo'),
			),
			array(
				'title'   => __('Your Company Name','wphub-wc-invoice'),
				'desc'    => __('Your Company Name in Invoice.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_company_name',
				'type'    => 'text',
				'css'     => 'width:80%;',
				'placeholder' => __('Your Company Name','woocommerce' ),
				'autoload'=> false,
				'desc_tip'=> true,
				'field_name' => 'wphub-invoice[company_name]',
				'value'   => wexphub_invoice()->get_value('company_name'),
			),
			array(
				'title'   => __('Your Company Address','wphub-wc-invoice'),
				'desc'    => __('Your Company Address in Invoice.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_company_address',
				'type'    => 'textarea',
				'css'     => 'width:80%;',
				'placeholder' => __('Your Company Address','woocommerce' ),
				'autoload'=> false,
				'desc_tip'=> true,
				'custom_attributes' => array(
					'rows'=>6,
				),
				'field_name' => 'wphub-invoice[company_address]',
				'value'   => wexphub_invoice()->get_value('company_address'),
			),
			array(
				'title'   => __('Your Company Email','wphub-wc-invoice'),
				'desc'    => __('Your Company Email in Invoice.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_company_email',
				'type'    => 'text',
				'css'     => 'width:80%;',
				'placeholder' => __('Your Company Email','woocommerce' ),
				'autoload'=> false,
				'desc_tip'=> true,
				'field_name' => 'wphub-invoice[company_email]',
				'value'   => wexphub_invoice()->get_value('company_email'),
			),
			array(
				'title'   => __('Your Company Phone','wphub-wc-invoice'),
				'desc'    => __('Your Company Email in Phone.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_company_phone',
				'type'    => 'text',
				'css'     => 'width:80%;',
				'placeholder' => __('Your Company Phone','woocommerce' ),
				'autoload'=> false,
				'desc_tip'=> true,
				'field_name' => 'wphub-invoice[company_phone]',
				'value'   => wexphub_invoice()->get_value('company_phone'),
			),
			array(
				'title'   => __('Footer Note','wphub-wc-invoice'),
				'desc'    => __('Invoice Footer Note.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_footer_note',
				'type'    => 'text',
				'css'     => 'width:80%;',
				'default' => __('Thank you for your order.','wphub-wc-invoice'),
				'autoload'=> false,
				'desc_tip'=> true,
				'field_name' => 'wphub-invoice[footer_note]',
				'value'   => wexphub_invoice()->get_value('footer_note'),
			),
			array(
				'title'   => __('Footer Copyright Info','wphub-wc-invoice'),
				'desc'    => __('Invoice Footer Copyright Info.','wphub-wc-invoice'),
				'id'      => 'wphub_invoice_footer_copyright',
				'type'    => 'text',
				'css'     => 'width:80%;',
				'default' => __('© Copyright {your_company_name} {current_year}, All Rights Reserved.','wphub-wc-invoice'),
				'autoload'=> false,
				'desc_tip'=> true,
				'field_name' => 'wphub-invoice[copyright_info]',
				'value'   => wexphub_invoice()->get_value('copyright_info'),
			),
			array(
				'type' => 'sectionend',
				'id' => 'wphub_invoice_section_end'
			)
		);

		return apply_filters('wphub_invoice_settings',$settings);
	}
}
?>