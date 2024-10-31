<?php
defined( 'ABSPATH' ) || exit;

class WEXP_Hub_Invoice_Format{

	function __construct(){

	}

	function format_invoice($invoice_html,$order,$settings){

		$invoice = new WEXP_Hub_Invoice($settings);

		$default_placeholder = array(
			'{{invoice_logo}}',
			'{{billing_company_name}}',
			'{{billing_first_name}}',
			'{{billing_last_name}}',
			'{{billing_address}}',
			'{{billing_phone}}',
			'{{billing_email}}',
			'{{billing_vat_number}}',
			'{{shipping_company_name}}',
			'{{shipping_first_name}}',
			'{{shipping_last_name}}',
			'{{shipping_address}}',
			'{{invoice_footer_note}}',
			'{{invoice_copyright_note}}',
			'{{your_company_name}}',
			'{{your_company_address}}',
			'{{your_company_phone}}',
			'{{your_company_email}}',
			'{{invoice_number}}',
			'{{invoice_order_number}}',
			'{{invoice_date}}',
			'{{order_date}}',
			'{{invoice_status}}',
			'{{label_due_amount}}',
			'{{amount_due}}',
			'{{current_year}}',
		);
		$default_values = array(
			$invoice->get_data('logo',$order),
			$order->get_billing_company(),
			$order->get_billing_first_name(),
			$order->get_billing_last_name(),
			$order->get_formatted_billing_address(),
			$order->get_billing_phone(),
			$order->get_billing_email(),
			$invoice->get_vat_number($order->get_id()),
			$order->get_shipping_company(),
			$order->get_shipping_first_name(),
			$order->get_shipping_last_name(),
			$order->get_formatted_shipping_address(),
			$invoice->get_data('footer_note',$order),
			$invoice->get_data('copyright_note',$order),
			$invoice->get_data('company_name',$order),
			$invoice->get_data('company_address',$order),
			$invoice->get_data('company_phone',$order),
			$invoice->get_data('company_email',$order),
			$invoice->get_data('invoice_number',$order),
			$invoice->get_data('order_number',$order),
			$invoice->get_data('invoice_date',$order),
			$invoice->get_data('order_date',$order),
			$invoice->get_data('invoice_status',$order),
			$invoice->get_data('label_due_amount',$order),
			$invoice->get_data('amount_due',$order),
			date('Y'),

		);
		$invoice_html = $this->format($invoice_html,$default_placeholder,$default_values);
		return $invoice_html;
	}

	function preview_format($invoice_html,$settings){

		$invoice = new WEXP_Hub_Invoice($settings);
		$default_placeholder = array(
			'{{invoice_logo}}',
			'{{invoice_footer_note}}',
			'{{invoice_copyright_note}}',
			'{{your_company_name}}',
			'{{your_company_address}}',
			'{{your_company_phone}}',
			'{{your_company_email}}',
			'{{current_year}}',
		);
		$default_values = array(
			$invoice->get_data('logo',''),
			$invoice->get_data('footer_note',''),
			$invoice->get_data('copyright_note',''),
			$invoice->get_data('company_name',''),
			$invoice->get_data('company_address',''),
			$invoice->get_data('company_phone',''),
			$invoice->get_data('company_email',''),
			date('Y'),

		);
		$invoice_html = $this->format($invoice_html,$default_placeholder,$default_values);
		return $invoice_html;
	}

	function quote($values){
		if(is_array($values) && !empty($values)){
			foreach($values as $key=>$value){
				$values[$key] = preg_quote($value, '$');
			}
		}
		return $values;
	}

	function format($values,$default_placeholder,$default_values){
		$values = $this->quote($values);
		$values = preg_replace($default_placeholder,$default_values,stripslashes_deep($values));
		return $values;
	}


}