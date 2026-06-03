<style>
	@page {
		size: A4
	}
</style>
<?php $invoice_id = isset($invoice_id) ? $invoice_id : 0; ?>

<!-- begin #content -->
<div id="content" class="content">
	<!-- begin breadcrumb -->
	<ol class="breadcrumb hidden-print pull-right">
		<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>invoices">All Plan Invoices</a></li>
		<li class="breadcrumb-item active"><?php echo $this->lang->line('invoice'); ?></li>
	</ol>
	<!-- end breadcrumb -->
	<!-- begin page-header -->
	<h1 class="page-header hidden-print">
	<?php echo $this->lang->line('invoice'); ?>#<?php echo $invoice_number = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->invoice_number; ?>
	</h1>
	<!-- end page-header -->
	<?php
	$tenant_rent_row = $this->db->get_where('tenant_rent', array('invoice_id' => $invoice_id))->row();
	$tenant_id = $tenant_rent_row ? $tenant_rent_row->tenant_id : 0;
	?>
	<!-- begin invoice -->
	<div class="invoice print-body" id="printableArea">
		<!-- begin invoice-company -->
		<div class="invoice-company text-inverse f-w-600 hidden-print">
			<span class="pull-right hidden-print">
				<a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5 hidden-print">
					<i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> <?php echo $this->lang->line('print'); ?>
				</a>
				<?php
					$whatsapp_pre_invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row();
					$whatsapp_tenant = $this->db->get_where('tenant', array('tenant_id' => $whatsapp_pre_invoice ? $whatsapp_pre_invoice->tenant_id : 0))->row();
					$whatsapp_mobile = ($whatsapp_tenant && !empty($whatsapp_tenant->mobile_number)) ? $whatsapp_tenant->mobile_number : '';
					$whatsapp_name = ($whatsapp_tenant && !empty($whatsapp_tenant->name)) ? $whatsapp_tenant->name : ($whatsapp_pre_invoice ? $whatsapp_pre_invoice->tenant_name : '');
				?>
				<?php if (!empty($whatsapp_mobile)): ?>
					<a href="javascript:;" onclick="sendInvoiceViaWhatsApp('<?php echo htmlspecialchars($whatsapp_mobile, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($whatsapp_name, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($whatsapp_pre_invoice ? $whatsapp_pre_invoice->invoice_number : '', ENT_QUOTES, 'UTF-8'); ?>', '<?php echo number_format((float)($whatsapp_pre_invoice->amount ?? 0), 2, '.', ''); ?>', '<?php echo base_url(); ?>uploads/invoices/<?php echo htmlspecialchars($whatsapp_pre_invoice->invoice_number, ENT_QUOTES, 'UTF-8'); ?>.pdf');" class="btn btn-sm m-b-10 p-l-5 hidden-print" style="background-color: #25D366; border-color: #25D366; color: #fff; margin-left: 6px;" title="Open WhatsApp Web with a pre-filled invoice message">
						<i class="fab fa-whatsapp fa-lg"></i> Send via WhatsApp
					</a>
				<?php endif; ?>
			</span>
			<?php echo html_escape($this->db->get_where('setting', array('name' => 'tagline'))->row()->content); ?>
		</div>
		<!-- end invoice-company -->
		
		<?php
			$invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row();
			$tenant = $tenant_id ? $this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row() : null;
			$room = ($tenant && !empty($tenant->room_id)) ? $this->db->get_where('room', array('room_id' => $tenant->room_id))->row() : null;
			$address = $this->db->get_where('setting', array('name' => 'address'))->row()->content;
			$system_name = $this->db->get_where('setting', array('name' => 'system_name'))->row()->content;
			$display_tenant_name = ($tenant && !empty($tenant->name)) ? $tenant->name : $invoice->tenant_name;
			$display_joining_date = ($tenant && !empty($tenant->lease_start)) ? date('d/m/Y', $tenant->lease_start) : 'N/A';
			$display_mobile_number = ($tenant && !empty($tenant->mobile_number)) ? $tenant->mobile_number : 'N/A';
			$display_plan_label = !empty($invoice->plan_type) ? $invoice->plan_type : ($tenant && !empty($tenant->plan_type) ? $tenant->plan_type : '');
			$display_plan_names = array(
				'per_day' => 'Per Day Plan',
				'monthly' => 'Monthly Plan',
				'quarterly' => '3 Month Plan',
				'half_yearly' => '6 Month Plan',
				'yearly' => '12 Month Plan'
			);
			
			// Extract phone number from address if available, otherwise just use a placeholder or remove
			// Assuming phone number is provided in address setting or another setting.
			// The user said: "here the adress need to take from the db , mobile . Here date of joining is taken from db , when the teanent is joined / lease period start date , also take tenent's mobile no. from db"

			if (!function_exists('amount_in_words')) {
				function amount_in_words($number) {
					$no = floor($number);
					$point = round($number - $no, 2) * 100;
					$hundred = null;
					$digits_1 = strlen($no);
					$i = 0;
					$str = array();
					$words = array('0' => '', '1' => 'One', '2' => 'Two',
					'3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
					'7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
					'10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
					'13' => 'Thirteen', '14' => 'Fourteen',
					'15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
					'18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
					'30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
					'60' => 'Sixty', '70' => 'Seventy',
					'80' => 'Eighty', '90' => 'Ninety');
					$digits_2 = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
					while ($i < $digits_1) {
						$divider = ($i == 2) ? 10 : 100;
						$number = floor($no % $divider);
						$no = floor($no / $divider);
						$i += ($divider == 10) ? 1 : 2;
						if ($number) {
							$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
							$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
							$str [] = ($number < 21) ? $words[(int)$number] . " " . $digits_2[(int)$counter] . $plural . " " . $hundred
								:
								$words[(int)(floor($number / 10) * 10)] . " " . $words[(int)($number % 10)] . " " . $digits_2[(int)$counter] . $plural . " " . $hundred;
						} else $str[] = null;
					}
					$str = array_reverse($str);
					$result = implode('', $str);
					$points = ($point) ?
						"." . $words[(int)floor($point / 10)] . " " . 
						$words[(int)($point % 10)] : '';
					return $result . "Rupees Only";
				}
			}

			// Calculate Total
			$this->db->select_sum('amount');
			$this->db->from('tenant_rent');
			$this->db->where('invoice_id', $invoice_id);
			$rent_query = $this->db->get();
			$rent_amount = $rent_query->row()->amount;

			$invoice_services_total = 0;
			$invoice_services = $this->db->get_where('invoice_service', array('invoice_id' => $invoice_id))->result_array();
			foreach ($invoice_services as $invoice_service) {
				$invoice_services_total += $this->db->get_where('service', array('service_id' => $invoice_service['service_id']))->row()->cost;
			}
			$late_fee = $invoice->late_fee;
			
			$grand_total = $rent_amount + $invoice_services_total + $late_fee;

			// --- GST calculation (controlled from website_settings) ---
			$gst_number_row  = $this->db->get_where('setting', array('name' => 'gst_number'))->row();
			$gst_enabled_row = $this->db->get_where('setting', array('name' => 'gst_enabled'))->row();
			$gst_number      = ($gst_number_row && $gst_number_row->content !== '') ? $gst_number_row->content : '';
			$gst_enabled     = ($gst_enabled_row && $gst_enabled_row->content == '1') ? true : false;
			$gst_percent     = 18;
			$gst_amount      = 0;
			if ($gst_enabled && $gst_number !== '') {
				$gst_amount = round($grand_total * ($gst_percent / 100), 2);
				$grand_total = $grand_total + $gst_amount;
			}
			
			// Calculate paid amount from transactions
			$this->db->select_sum('amount');
			$this->db->from('invoice_transaction');
			$this->db->where('invoice_id', $invoice_id);
			$trans_query = $this->db->get();
			$trans_amount = $trans_query->row()->amount;

			if ($trans_amount > 0) {
				$paid_amount = $trans_amount;
			} else {
				$paid_amount = $invoice->status ? $grand_total : 0;
			}
			$balance = $grand_total - $paid_amount;
		?>

		<div class="custom-invoice-print">
			<div class="custom-header">
				<div class="header-left">
					<?php if (!empty($gst_number)): ?>
						GSTIN: <?php echo html_escape($gst_number); ?> •
					<?php else: ?>
						GSTIN: N/A •
					<?php endif; ?>
				</div>
				<div class="header-right">
					Original • #Sales Invoice no. <?php echo $invoice->invoice_number; ?>
				</div>
			</div>

			<div class="custom-company-info">
				<div class="company-left">
					<h2><?php echo strtoupper($system_name); ?></h2>
					<p><?php echo strip_tags($address); ?></p>
				</div>
				<div class="company-right">
					Date: <?php echo date('d M Y', $invoice->created_on); ?>
				</div>
			</div>

			<div class="custom-bill-to">
				<div class="bill-to-title">BILL TO</div>
				<div class="bill-to-name">
					<?php echo html_escape($display_tenant_name); ?> ( Plan Start - <?php echo $display_joining_date; ?> )
				</div>
				<div class="bill-to-contact">
					Phone: <?php echo html_escape($display_mobile_number); ?>
				</div>
				<div class="bill-to-contact">
					Plan: <?php echo isset($display_plan_names[$display_plan_label]) ? $display_plan_names[$display_plan_label] : 'N/A'; ?>
				</div>
				<div class="bill-to-contact">
					Shift: <?php echo !empty($invoice->shift_name) ? html_escape($invoice->shift_name) : 'N/A'; ?>
				</div>
				<div class="bill-to-supply">
					Place Of Supply: Assam
				</div>
			</div>

			<table class="custom-invoice-table">
				<thead>
					<tr>
						<th width="5%">S.No.</th>
						<th width="40%">ITEMS</th>
						<th width="10%">HSN/SAC</th>
						<th width="10%">QTY</th>
						<th width="10%">RATE</th>
						<th width="8%">DISC.</th>
						<th width="7%">TAX</th>
						<th width="10%">AMOUNT</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$sno = 1;
						$item_name = (isset($display_plan_names[$display_plan_label]) ? $display_plan_names[$display_plan_label] : 'Study Plan')
							. " (" . date('d M, Y', $invoice->start_date) . " to " . date('d M, Y', $invoice->end_date) . ")";
						if (!empty($invoice->shift_name)) {
							$item_name .= " - " . $invoice->shift_name;
						}
						if (!empty($invoice->seat_label)) {
							$item_name = $invoice->seat_label . " - " . $item_name;
						}
					?>
					<tr>
						<td class="text-center"><?php echo $sno++; ?></td>
						<td><?php echo $item_name; ?></td>
						<td class="text-center">996311</td>
						<td class="text-center">1</td>
						<td class="text-right"><?php echo number_format($rent_amount, 2, '.', ''); ?></td>
						<td class="text-right">0.00</td>
						<td class="text-right">0.00</td>
						<td class="text-right"><?php echo number_format($rent_amount, 2, '.', ''); ?></td>
					</tr>

					<?php foreach ($invoice_services as $invoice_service): 
						$service_cost = $this->db->get_where('service', array('service_id' => $invoice_service['service_id']))->row()->cost;
					?>
					<tr>
						<td class="text-center"><?php echo $sno++; ?></td>
						<td><?php echo $this->db->get_where('service', array('service_id' => $invoice_service['service_id']))->row()->name; ?></td>
						<td class="text-center">-</td>
						<td class="text-center">1.0</td>
						<td class="text-right"><?php echo number_format($service_cost, 2, '.', ''); ?></td>
						<td class="text-right">0.00</td>
						<td class="text-right">0.00</td>
						<td class="text-right"><?php echo number_format($service_cost, 2, '.', ''); ?></td>
					</tr>
					<?php endforeach; ?>

					<?php if ($late_fee > 0): ?>
					<tr>
						<td class="text-center"><?php echo $sno++; ?></td>
						<td>Late Fee</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-right"><?php echo number_format($late_fee, 2, '.', ''); ?></td>
						<td class="text-right">0.00</td>
						<td class="text-right">0.00</td>
						<td class="text-right"><?php echo number_format($late_fee, 2, '.', ''); ?></td>
					</tr>
					<?php endif; ?>

					<tr class="subtotal-row">
						<td></td>
						<td><em>Subtotal</em></td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-right"><strong><?php echo number_format($rent_amount + $invoice_services_total + $late_fee, 2, '.', ''); ?></strong></td>
					</tr>
					<?php if ($gst_enabled && $gst_amount > 0): ?>
					<tr class="gst-row">
						<td></td>
						<td><em>GST (<?php echo $gst_percent; ?>%)</em></td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-right"><strong><?php echo number_format($gst_amount, 2, '.', ''); ?></strong></td>
					</tr>
					<?php endif; ?>
					<tr class="roundoff-row">
						<td></td>
						<td><em>Round Off</em></td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-center">-</td>
						<td class="text-right"></td>
					</tr>
					<tr class="total-row">
						<td colspan="5" class="text-left"><strong>TOTAL</strong></td>
						<td class="text-right"><strong>0.00</strong></td>
						<td class="text-right"><strong>0.00</strong></td>
						<td class="text-right"><strong><?php echo number_format($grand_total, 2, '.', ''); ?></strong></td>
					</tr>
					<tr class="received-row">
						<td colspan="7" class="text-left"><strong>RECEIVED AMOUNT</strong></td>
						<td class="text-right"><strong><?php echo number_format($paid_amount, 2, '.', ''); ?></strong></td>
					</tr>
					<tr class="balance-row">
						<td colspan="7" class="text-left"><strong>INVOICE BALANCE</strong></td>
						<td class="text-right"><strong><?php echo number_format($balance, 2, '.', ''); ?></strong></td>
					</tr>
				</tbody>
			</table>

			<div class="custom-amount-words">
				<div class="title">TOTAL AMOUNT IN WORDS</div>
				<div class="content"><?php echo amount_in_words($grand_total); ?></div>
			</div>

			<div class="custom-terms">
				<div class="title">TERMS & CONDITIONS</div>
				<div class="content">1. Thank You for doing business with us.</div>
			</div>

			<div class="custom-footer">
				Is reverse charge applicable? : No
			</div>
		</div>

		<!-- Hide original invoice-header and invoice-content during print, but keep for screen if needed, 
			 or we can replace it completely for both screen and print to match the new layout.
			 Let's replace completely so they see the same design. -->
	</div>
	<!-- end invoice -->
</div>
<!-- end #content -->




<script>
function printPageArea(areaID){
    var printContent = document.getElementById(areaID).innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
}
</script>








<style>
    .custom-invoice-print {
        background: #fff;
        padding: 20px;
        color: #000;
        font-family: Arial, sans-serif;
    }
    .custom-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        font-size: 12px;
    }
    .custom-company-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .company-left h2 {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
    }
    .company-left p {
        margin: 5px 0;
        font-size: 14px;
        max-width: 300px;
    }
    .company-right {
        font-size: 14px;
        text-align: right;
    }
    .custom-bill-to {
        border: 1px solid #000;
        padding: 10px;
        margin-bottom: 20px;
    }
    .custom-bill-to .bill-to-title {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .custom-bill-to .bill-to-name {
        font-weight: bold;
        font-size: 16px;
    }
    .custom-bill-to div {
        margin-bottom: 5px;
    }
    .custom-invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .custom-invoice-table th, .custom-invoice-table td {
        border: 1px solid #000;
        padding: 8px;
    }
    .custom-invoice-table th {
        background-color: #e5b8b7;
        text-align: center;
        font-weight: bold;
    }
    .custom-invoice-table .subtotal-row td {
        border-top: 1px dashed #000;
        border-bottom: 1px dashed #000;
    }
    .custom-invoice-table .roundoff-row td {
        border-bottom: 1px solid #000;
    }
    .custom-invoice-table .total-row {
        background-color: #e5b8b7;
    }
    .custom-invoice-table .received-row, .custom-invoice-table .balance-row {
        background-color: #fff;
    }
    .custom-amount-words, .custom-terms {
        border: 1px solid #000;
        margin-bottom: 20px;
    }
    .custom-amount-words .title, .custom-terms .title {
        font-weight: bold;
        padding: 5px 10px;
    }
    .custom-amount-words .content, .custom-terms .content {
        padding: 5px 10px;
        border-top: 1px solid #000;
    }
    .custom-footer {
        font-size: 14px;
    }
	@media print {
		.hidden-print {
			display: none !important;
		}
        .content {
            padding: 0 !important;
            background: #fff;
        }
        .custom-invoice-print {
            padding: 0;
        }
        body {
            background: #fff;
        }
        .custom-invoice-table th {
            background-color: #e5b8b7 !important;
            -webkit-print-color-adjust: exact;
        }
        .custom-invoice-table .total-row {
            background-color: #e5b8b7 !important;
            -webkit-print-color-adjust: exact;
        }
        }
</style>

<script>
function sendInvoiceViaWhatsApp(mobile, name, invoiceNumber, amount, pdfLink) {
    if (!mobile) { return; }
    var digits = String(mobile).replace(/\D/g, '');
    if (!digits) { return; }
    if (digits.length > 10) {
        digits = digits.slice(-10);
    }
    var prettyAmount = (parseFloat(amount) || 0).toFixed(2);
    var text = 'Dear ' + name + ', please find your invoice #' + invoiceNumber + ' for Rs.' + prettyAmount + '. Download: ' + pdfLink;
    var url = 'https://wa.me/91' + digits + '?text=' + encodeURIComponent(text);
    window.open(url, '_blank');
}
</script>
