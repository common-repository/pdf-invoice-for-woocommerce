<?php
defined( 'ABSPATH' ) || exit;

use Dompdf\Dompdf;

class WEXP_Hub_Invoice{

	protected $settings = array();

	function __construct($invoice_setting){
		$this->settings = $invoice_setting;
	}

	function dump_invoice($order){
		$invoice_html = $this->get_invoice_html($order);
		$invoice_num = $this->get_invoice_num($order->get_id());
		$upload_dir = wp_upload_dir();
		require_once(wexphub_invoice()->plugin_path().'/dompdf/autoload.inc.php');
		$dompdf = new Dompdf();
		$dompdf->loadHtml($invoice_html);
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$output = $dompdf->output();
		file_put_contents($upload_dir['basedir'].'/wphub-invoice/invoice-'.$invoice_num.'.pdf', $output);
		$order->update_meta_data('wphub_invoice_url',$upload_dir['baseurl'].'/wphub-invoice/invoice-'.$invoice_num.'.pdf');
		$order->save();
	}

	function preview_invoice($template,$get_template=true){
		$template_html = $get_template ? $this->get_template_html($template) : $template;
		require_once(wexphub_invoice()->plugin_path().'/dompdf/autoload.inc.php');
		$dompdf = new Dompdf();
		$dompdf->loadHtml($template_html);
		$dompdf->setPaper('A4','portrait');
		$dompdf->render();
		$dompdf->stream($template.".pdf",array("Attachment"=>false));
	}

	function get_invoice_path($order){
		$invoice_num = $this->get_invoice_num($order->get_id());
		$upload_dir = wp_upload_dir();
		$invoice_path = $upload_dir['basedir'].'/wphub-invoice/invoice-'.$invoice_num.'.pdf';
		if(!file_exists($invoice_path)){
			$this->dump_invoice($order);
		}
		return $invoice_path;
	}

	function get_vat_number($order_id){
		$order = wc_get_order($order_id);
		$vat_num = $order->get_meta('wphub_company_vat');
		return $vat_num!='' ? 'VAT NO: '.$vat_num : '';
	}

	function get_invoice_num($order_id){
		$invoice_num = null;
		if(isset($this->settings['sequential_number']) && $this->settings['sequential_number']){
			$order = wc_get_order($order_id);
			$invoice_num = $order->get_meta('_wphub_invoice_num');
			if(!$invoice_num){
				$last_invoice_number = get_option('_wphub_last_invoice_number');
				if(is_numeric($last_invoice_number) && $last_invoice_number>0){
					$invoice_num = $last_invoice_number+1;
				}
				else
				{
					$invoice_num = $this->settings['start_number']+1;
				}
				update_option('_wphub_last_invoice_number',$invoice_num,'no');
				$order->update_meta_data('_wphub_invoice_num',$invoice_num);
				$order->save();
			}
		}
		return $invoice_num;
	}

	function get_invoice_html($order){
		$format = new WEXP_Hub_Invoice_Format();
		return $format->format_invoice(wc_get_template_html(
			'template-1.php',
			array(
				'order'         => $order,
				'settings'      => $this->settings,
			),
			'invoice-for-wc/',
			wexphub_invoice()->plugin_path().'/templates/'
		),
			$order,
			$this->settings
		);
	}

	function get_template_html($template){
		$format = new WEXP_Hub_Invoice_Format();
		return $format->preview_format(wc_get_template_html(
			$template.'.php',
			array(
				'settings'      => $this->settings,
			),
			'invoice-for-wc/',
			wexphub_invoice()->plugin_path().'/preview/'
		),
			$this->settings
		);
	}


	function get_data($field,$order){
		$value = '';
		if($field=='logo'){
			if(isset($this->settings['logo']) && trim($this->settings['logo'])!=''){
				$value = '<img src="'.trim($this->settings['logo']).'" alt="'.$this->settings['company_name'].'">';
			}
		}
		elseif($field=='company_name'){
			$value = $this->settings['company_name'];
		}
		elseif($field=='company_address'){
			$value = nl2br($this->settings['company_address']);
		}
		elseif($field=='company_email'){
			$value = $this->settings['company_email'];
		}
		elseif($field=='company_phone'){
			$value = $this->settings['company_phone'];
		}
		elseif($field=='footer_note'){
			$value = $this->settings['footer_note'];
		}
		elseif($field=='copyright_note'){
			$value = $this->settings['copyright_info'];
		}
		elseif($field=='invoice_status'){
			if(isset($this->settings['paid_invoice']) && is_array($this->settings['paid_invoice']) && in_array($order->get_status(),$this->settings['paid_invoice'])){
				$value = '<h4 class="in-label in-paid">'.__('PAID','wphub-wc-invoice').'</h4>';
			}
		}
		elseif($field=='invoice_date'){
			$value = __('Invoice Date:','wphub-wc-invoice').' '.wc_format_datetime($order->get_date_created(),'d/m/Y');
		}
		elseif($field=='order_date'){
			$value = __('Order Date:','wphub-wc-invoice').' '.wc_format_datetime($order->get_date_created(),'d/m/Y');
		}
		elseif($field=='order_number'){
			$value = __('Order Number:','wphub-wc-invoice').' '.$order->get_order_number();
		}
		elseif($field=='invoice_number'){
			$value = __('Invoice No:','wphub-wc-invoice').' '.$this->get_invoice_num($order->get_id());
		}
		elseif($field=='label_due_amount'){
			if(isset($this->settings['paid_invoice']) && is_array($this->settings['paid_invoice']) && in_array($order->get_status(),$this->settings['paid_invoice'])){
				$value = __('Amount Due:','wphub-wc-invoice').' '.wc_price(0.00,array('currency'=>$order->get_currency()));
			}
			else
			{
				$value = __('Amount Due:','wphub-wc-invoice').' '.wc_price($order->get_total(),array('currency'=>$order->get_currency()));
			}
		}
		elseif($field=='amount_due'){
			if(isset($this->settings['paid_invoice']) && is_array($this->settings['paid_invoice']) && in_array($order->get_status(),$this->settings['paid_invoice'])){
				$value = wc_price(0.00,array('currency'=>$order->get_currency()));
			}
			else
			{
				$value = wc_price($order->get_total(),array('currency'=>$order->get_currency()));
			}
		}
		return $value;
	}
}