<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; color: #000; }
        .custom-invoice-print { padding: 20px; }
        table.layout { width: 100%; margin-bottom: 20px; }
        .company-left h2 { margin: 0; font-size: 24px; font-weight: bold; }
        .company-left p { margin: 5px 0; font-size: 14px; }
        .bill-to { border: 1px solid #000; padding: 10px; margin-bottom: 20px; }
        .bill-to .title { font-weight: bold; margin-bottom: 5px; }
        .bill-to .name { font-weight: bold; font-size: 16px; margin-bottom: 5px; }
        .bill-to .contact, .bill-to .supply { margin-bottom: 5px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th, table.items td { border: 1px solid #000; padding: 8px; }
        table.items th { background-color: #e5b8b7; text-align: center; font-weight: bold; }
        table.items .subtotal-row td { border-top: 1px dashed #000; border-bottom: 1px dashed #000; }
        table.items .roundoff-row td { border-bottom: 1px solid #000; }
        table.items .total-row { background-color: #e5b8b7; }
        .amount-words, .terms { border: 1px solid #000; margin-bottom: 20px; }
        .amount-words .title, .terms .title { font-weight: bold; padding: 5px 10px; }
        .amount-words .content, .terms .content { padding: 5px 10px; border-top: 1px solid #000; }
        .footer { font-size: 14px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
<?php $invoice_id = isset($invoice_id) ? $invoice_id : 0; ?>
<?php
    $invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row();
    $tenant = $this->db->get_where('tenant', array('tenant_id' => $invoice->tenant_id))->row();
    $room = $this->db->get_where('room', array('room_id' => $tenant->room_id))->row();
    $address = $this->db->get_where('setting', array('name' => 'address'))->row()->content;
    $system_name = $this->db->get_where('setting', array('name' => 'system_name'))->row()->content;

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
    <table class="layout">
        <tr>
            <td style="font-size: 12px;">GSTIN: 18CLWPM0939F1ZL •</td>
            <td style="font-size: 12px; text-align: right;">Original • #Sales Invoice no. <?php echo $invoice->invoice_number; ?></td>
        </tr>
    </table>
    <table class="layout">
        <tr>
            <td class="company-left">
                <h2><?php echo strtoupper($system_name); ?></h2>
                <p><?php echo strip_tags($address); ?></p>
            </td>
            <td class="company-right text-right" style="vertical-align: top;">
                Date: <?php echo date('d M Y', $invoice->created_on); ?>
            </td>
        </tr>
    </table>
    <div class="bill-to">
        <div class="title">BILL TO</div>
        <div class="name">
            <?php echo $invoice->tenant_name; ?> ( Plan Start Date - <?php echo $tenant->lease_start ? date('d/m/Y', $tenant->lease_start) : 'N/A'; ?> )
        </div>
        <div class="contact">
            Phone: <?php echo $tenant->mobile_number; ?>
        </div>
        <div class="supply">
            Place Of Supply: Assam
        </div>
    </div>
    <table class="items">
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
                $display_plan_names = array(
                    'per_day' => 'Per Day Plan',
                    'monthly' => 'Monthly Plan',
                    'quarterly' => '3 Month Plan',
                    'half_yearly' => '6 Month Plan',
                    'yearly' => '12 Month Plan'
                );
                $display_plan_label = !empty($invoice->plan_type) ? $invoice->plan_type : '';

                if ($invoice->invoice_type == 0) {
                    $item_name = (isset($display_plan_names[$display_plan_label]) ? $display_plan_names[$display_plan_label] : 'Study Plan')
                        . " (" . date('d M, Y', $invoice->start_date) . " to " . date('d M, Y', $invoice->end_date) . ")";
                } else {
                    $months_total = $this->db->get_where('tenant_rent', array('invoice_id' => $invoice_id))->result_array();
                    $months = array();
                    foreach ($months_total as $mt) {
                        $months[] = $mt['month'] . " " . $mt['year'];
                    }
                    $item_name = (isset($display_plan_names[$display_plan_label]) ? $display_plan_names[$display_plan_label] : 'Study Plan')
                        . " (" . implode(", ", $months) . ")";
                }

                if (!empty($invoice->shift_name)) {
                    $item_name .= " - " . $invoice->shift_name;
                }

                if (!empty($invoice->seat_label)) {
                    $item_name = $invoice->seat_label . " - " . $item_name;
                } elseif ($room) {
                    $item_name = "Seat " . $room->room_number . " - " . $item_name;
                }
            ?>
            <tr>
                <td class="text-center"><?php echo $sno++; ?></td>
                <td><?php echo $item_name; ?></td>
                <td class="text-center">996311</td>
                <td class="text-center">1.0 MON</td>
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
                <td class="text-right"><strong><?php echo number_format($grand_total, 2, '.', ''); ?></strong></td>
            </tr>
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
            <tr>
                <td colspan="7" class="text-left"><strong>RECEIVED AMOUNT</strong></td>
                <td class="text-right"><strong><?php echo number_format($paid_amount, 2, '.', ''); ?></strong></td>
            </tr>
            <tr>
                <td colspan="7" class="text-left"><strong>INVOICE BALANCE</strong></td>
                <td class="text-right"><strong><?php echo number_format($balance, 2, '.', ''); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="amount-words">
        <div class="title">TOTAL AMOUNT IN WORDS</div>
        <div class="content"><?php echo amount_in_words($grand_total); ?></div>
    </div>

    <div class="terms">
        <div class="title">TERMS & CONDITIONS</div>
        <div class="content">1. Thank You for doing business with us.</div>
    </div>

    <div class="footer">
        Is reverse charge applicable? : No
    </div>
</div>
</body>
</html>
