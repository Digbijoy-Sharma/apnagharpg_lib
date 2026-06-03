<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Monthly Sales Report - <?php echo html_escape($month . ' ' . $year); ?></title>
<style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #222; }
    h1 { font-size: 18px; margin: 0 0 4px 0; }
    .sub { color: #666; margin-bottom: 12px; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f0f0f0; border: 1px solid #bbb; padding: 5px 6px; text-align: left; }
    td { border: 1px solid #ddd; padding: 4px 6px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    tfoot th { background: #e8e8e8; }
    .summary { margin-top: 14px; padding: 8px 10px; background: #f7f7f7; border: 1px solid #ddd; }
</style>
</head>
<body>
<?php
$system_name_row = $this->db->get_where('setting', array('name' => 'system_name'))->row();
$system_name = $system_name_row ? $system_name_row->content : 'Library';
?>
<h1><?php echo html_escape($system_name); ?> — Monthly Sales Report</h1>
<div class="sub">Period: <strong><?php echo html_escape($month . ' ' . $year); ?></strong> &nbsp; | &nbsp; Generated: <?php echo date('d M Y, h:i A'); ?></div>

<?php
$tenant_rents = $this->db->get_where('tenant_rent', array('month' => $month, 'year' => (int)$year))->result_array();
$total_rent = 0; $total_services = 0; $total_late_fee = 0; $total_collected = 0;
$rows = [];
foreach ($tenant_rents as $tr) {
    $invoice_id = (int)$tr['invoice_id']; $tenant_id = (int)$tr['tenant_id'];
    $amount = (float)$tr['amount']; $status = (int)$tr['status'];
    $invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row();
    $invoice_number = $invoice ? $invoice->invoice_number : '-';
    $late_fee = $invoice ? (float)$invoice->late_fee : 0;
    $created_on = $invoice ? (int)$invoice->created_on : 0;
    $svc_total = 0; $svc_names = [];
    $services = $this->db->get_where('invoice_service', array('invoice_id' => $invoice_id))->result_array();
    foreach ($services as $s) {
        $svc = $this->db->get_where('service', array('service_id' => $s['service_id']))->row();
        if ($svc) { $svc_total += (float)$svc->cost; $svc_names[] = $svc->name; }
    }
    $this->db->select_sum('amount');
    $this->db->from('invoice_transaction');
    $this->db->where('invoice_id', $invoice_id);
    $paid = (float)($this->db->get()->row()->amount ?? 0);
    if ($paid <= 0 && $status == 1) $paid = $amount + $svc_total + $late_fee;
    $t = $this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row();
    $tenant_name = $t ? $t->name : '-';
    $rows[] = ['inv'=>$invoice_number,'date'=>$created_on,'tenant'=>$tenant_name,'svc'=>implode(', ', $svc_names),'rent'=>$amount,'svc_amt'=>$svc_total,'late'=>$late_fee,'paid'=>$paid,'status'=>$status];
    $total_rent += $amount; $total_services += $svc_total; $total_late_fee += $late_fee; $total_collected += $paid;
}
$grand = $total_rent + $total_services + $total_late_fee;
?>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice</th>
            <th>Date</th>
            <th>Tenant</th>
            <th>Services</th>
            <th class="text-right">Rent</th>
            <th class="text-right">Services</th>
            <th class="text-right">Late Fee</th>
            <th class="text-right">Paid</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="10" class="text-center">No sales records for this period.</td></tr>
        <?php else: $i=1; foreach($rows as $r): ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo html_escape($r['inv']); ?></td>
                <td><?php echo $r['date'] ? date('d-M-Y', $r['date']) : '-'; ?></td>
                <td><?php echo html_escape($r['tenant']); ?></td>
                <td><?php echo html_escape($r['svc'] ?: '-'); ?></td>
                <td class="text-right"><?php echo number_format($r['rent'], 2); ?></td>
                <td class="text-right"><?php echo number_format($r['svc_amt'], 2); ?></td>
                <td class="text-right"><?php echo number_format($r['late'], 2); ?></td>
                <td class="text-right"><?php echo number_format($r['paid'], 2); ?></td>
                <td><?php echo $r['status']==1 ? 'Paid' : 'Due'; ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" class="text-right">Totals:</th>
            <th class="text-right"><?php echo number_format($total_rent, 2); ?></th>
            <th class="text-right"><?php echo number_format($total_services, 2); ?></th>
            <th class="text-right"><?php echo number_format($total_late_fee, 2); ?></th>
            <th class="text-right"><?php echo number_format($total_collected, 2); ?></th>
            <th></th>
        </tr>
    </tfoot>
</table>

<div class="summary">
    <strong>Summary:</strong>
    Gross Sales = Rs. <?php echo number_format($grand, 2); ?> &nbsp;|&nbsp;
    Collected = Rs. <?php echo number_format($total_collected, 2); ?> &nbsp;|&nbsp;
    Outstanding = Rs. <?php echo number_format(max(0, $grand - $total_collected), 2); ?>
</div>

</body>
</html>
