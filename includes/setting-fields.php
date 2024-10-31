<?php
defined( 'ABSPATH' ) || exit;

$statuses = wc_get_order_statuses();
$invoice_settings = wexphub_invoice()->get_settings();
$emails = WC()->mailer()->get_emails();
?>
<tr>
    <th scope="row"><label><?php echo __('Generate Invoice','wphub-wc-invoice'); ?></label></th>
    <td class="forminp">
        <select name="wphub-invoice[generate_invoice][]" class="wc-enhanced-select" multiple="multiple" style="width:80%;">
		    <?php
		    if(is_array($statuses) && !empty($statuses)){
			    foreach($statuses as $status_key=>$status_label){
                    $status = wexphub_invoice()->get_status_key($status_key);
				    $selected = in_array($status,$invoice_settings['generate_invoice']) ? 'selected' : '';
				    echo '<option value="'.esc_attr($status).'" '.$selected.'>'.esc_html($status_label).'</option>';
			    }
		    }
		    ?>
        </select>
        <p class="description"><?php echo __('Generate invoice if order status is any of selected above.','wphub-wc-invoice'); ?></p>
    </td>
</tr>
<tr>
    <th scope="row"><label><?php echo __('Mark as Paid','wphub-wc-invoice'); ?></label></th>
    <td class="forminp">
        <select name="wphub-invoice[paid_invoice][]" class="wc-enhanced-select" multiple="multiple" style="width:80%;">
			<?php
			if(is_array($statuses) && !empty($statuses)){
				foreach($statuses as $status_key=>$status_label){
					$status = wexphub_invoice()->get_status_key($status_key);
					$selected = in_array($status,$invoice_settings['paid_invoice']) ? 'selected' : '';
					echo '<option value="'.esc_attr($status).'" '.$selected.'>'.esc_html($status_label).'</option>';
				}
			}
			?>
        </select>
        <p class="description"><?php echo __('Mark invoice as paid if order status is any of selected above.','wphub-wc-invoice'); ?></p>
    </td>
</tr>
<tr>
    <th scope="row"><label><?php echo __('Attach to Emails','wphub-wc-invoice'); ?></label></th>
    <td class="forminp">
        <select name="wphub-invoice[emails][]" class="wc-enhanced-select" multiple="multiple" style="width:80%;">
			<?php
			if(is_array($emails) && !empty($emails)){
				foreach($emails as $email_key=>$email){
					$selected = in_array($email->id,$invoice_settings['emails']) ? 'selected' : '';
					echo '<option value="'.esc_attr($email->id).'" '.$selected.'>'.esc_html($email->title).'</option>';
				}
			}
			?>
        </select>
        <p class="description"><?php echo __('Attach invoice to the above selected emails.','wphub-wc-invoice'); ?></p>
    </td>
</tr>