<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Annual Report - Financial Year <?php echo html_escape($fy); ?></title>
<style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #222; }
    h1 { font-size: 18px; margin: 0 0 4px 0; }
    h2 { font-size: 14px; margin: 16px 0 6px 0; }
    .sub { color: #666; margin-bottom: 12px; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th { background: #f0f0f0; border: 1px solid #bbb; padding: 5px 6px; text-align: left; }
    td { border: 1px solid #ddd; padding: 4px 6px; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    tfoot th, .total-row td { background: #e8e8e8; font-weight: bold; }
    .summary-table th { background: #d9e8f5; }
    .net-positive { background: #dff0d8; }
    .net-negative { background: #f2dede; }
</style>
</head>
<body>
<?php
$system_name_row = $this->db->get_where('setting', array('name' => 'system_name'))->row();
$system_name = $system_name_row ? $system_name_row->content : 'Library';

// Compute everything (same logic as the UI view)
list($start_year, $end_year) = array_map('intval', explode('-', $fy));
$fy_start_str = $start_year . '-04-01';
$fy_end_str   = $end_year   . '-03-31';
$fy_start_ts  = strtotime($fy_start_str . ' 00:00:00');
$fy_end_ts    = strtotime($fy_end_str   . ' 23:59:59');

$fy_months = [];
for ($i = 0; $i < 12; $i++) {
    $m = ($i % 12) + 4;
    if ($m > 12) { $m -= 12; $y = $start_year + 1; } else { $y = $start_year; }
    $fy_months[] = ['month' => date('F', mktime(0,0,0,$m,1,2000)), 'year' => $y];
}

// Sales
$total_sales_rent = 0; $total_sales_services = 0; $total_sales_late = 0; $total_sales_collected = 0; $sales_invoice_count = 0;
$sales_rows = [];
foreach ($fy_months as $mm) {
    $rows = $this->db->get_where('tenant_rent', array('month' => $mm['month'], 'year' => (int)$mm['year']))->result_array();
    foreach ($rows as $r) {
        $inv_id = (int)$r['invoice_id']; $amt = (float)$r['amount']; $status = (int)$r['status'];
        $inv = $this->db->get_where('invoice', array('invoice_id' => $inv_id))->row();
        $late = $inv ? (float)$inv->late_fee : 0;
        $created_on = $inv ? (int)$inv->created_on : 0;
        $invoice_number = $inv ? $inv->invoice_number : '-';
        $svc_total = 0; $svc_names = [];
        $svcs = $this->db->get_where('invoice_service', array('invoice_id' => $inv_id))->result_array();
        foreach ($svcs as $s) {
            $svc = $this->db->get_where('service', array('service_id' => $s['service_id']))->row();
            if ($svc) { $svc_total += (float)$svc->cost; $svc_names[] = $svc->name; }
        }
        $this->db->select_sum('amount');
        $this->db->from('invoice_transaction');
        $this->db->where('invoice_id', $inv_id);
        $paid = (float)($this->db->get()->row()->amount ?? 0);
        if ($paid <= 0 && $status == 1) $paid = $amt + $svc_total + $late;
        $t = $this->db->get_where('tenant', array('tenant_id' => (int)$r['tenant_id']))->row();
        $tenant_name = $t ? $t->name : '-';

        $sales_rows[] = ['date'=>$created_on,'inv'=>$invoice_number,'tenant'=>$tenant_name,'rent'=>$amt,'svc_amt'=>$svc_total,'late'=>$late,'paid'=>$paid,'status'=>$status];
        $total_sales_rent += $amt; $total_sales_services += $svc_total; $total_sales_late += $late; $total_sales_collected += $paid; $sales_invoice_count++;
    }
}
$total_sales_gross = $total_sales_rent + $total_sales_services + $total_sales_late;

// Purchases
$purchases_in_fy = [];
$all_purchases = $this->db->get('purchase')->result_array();
$total_purchases = 0;
foreach ($all_purchases as $p) {
    $pd = $p['purchase_date'];
    if ($pd && strtotime($pd) >= $fy_start_ts && strtotime($pd) <= $fy_end_ts) {
        $total_purchases += (float)$p['amount'];
        $purchases_in_fy[] = $p;
    }
}

// Expenses
$total_expenses = 0; $expenses_in_fy = [];
foreach ($fy_months as $mm) {
    $rows = $this->db->get_where('expense', array('month' => $mm['month'], 'year' => (int)$mm['year']))->result_array();
    foreach ($rows as $r) { $total_expenses += (float)$r['amount']; $expenses_in_fy[] = $r; }
}

// Salary
$total_salary = 0; $salary_in_fy = [];
foreach ($fy_months as $mm) {
    $rows = $this->db->get_where('staff_salary', array('month' => $mm['month'], 'year' => (int)$mm['year']))->result_array();
    foreach ($rows as $r) {
        $total_salary += (float)$r['amount'];
        $s = $this->db->get_where('staff', array('staff_id' => (int)$r['staff_id']))->row();
        $r['staff_name'] = $s ? $s->name : '-';
        $salary_in_fy[] = $r;
    }
}

$total_outgoing = $total_purchases + $total_expenses + $total_salary;
$net_position   = $total_sales_collected - $total_outgoing;
?>

<h1><?php echo html_escape($system_name); ?> — Annual Report</h1>
<div class="sub">Financial Year: <strong><?php echo html_escape($fy); ?></strong> &nbsp; (<?php echo date('d M Y', $fy_start_ts); ?> to <?php echo date('d M Y', $fy_end_ts); ?>) &nbsp; | &nbsp; Generated: <?php echo date('d M Y, h:i A'); ?></div>

<!-- ITR / CA Summary -->
<h2>Combined Summary (for ITR / CA use)</h2>
<table class="summary-table">
    <thead>
        <tr><th>Category</th><th class="text-right">Count</th><th class="text-right">Amount (Rs.)</th></tr>
    </thead>
    <tbody>
        <tr><td>Sales — Rent (Plan / Fees)</td><td class="text-right"><?php echo $sales_invoice_count; ?></td><td class="text-right"><?php echo number_format($total_sales_rent, 2); ?></td></tr>
        <tr><td>Sales — Service charges</td><td class="text-right">-</td><td class="text-right"><?php echo number_format($total_sales_services, 2); ?></td></tr>
        <tr><td>Sales — Late fees</td><td class="text-right">-</td><td class="text-right"><?php echo number_format($total_sales_late, 2); ?></td></tr>
        <tr class="total-row"><td>Gross Sales</td><td class="text-right"><?php echo $sales_invoice_count; ?></td><td class="text-right"><?php echo number_format($total_sales_gross, 2); ?></td></tr>
        <tr><td>Amount Collected (from transactions)</td><td class="text-right">-</td><td class="text-right"><?php echo number_format($total_sales_collected, 2); ?></td></tr>
        <tr><td>Purchases</td><td class="text-right"><?php echo count($purchases_in_fy); ?></td><td class="text-right"><?php echo number_format($total_purchases, 2); ?></td></tr>
        <tr><td>Expenses</td><td class="text-right"><?php echo count($expenses_in_fy); ?></td><td class="text-right"><?php echo number_format($total_expenses, 2); ?></td></tr>
        <tr><td>Salary</td><td class="text-right"><?php echo count($salary_in_fy); ?></td><td class="text-right"><?php echo number_format($total_salary, 2); ?></td></tr>
        <tr class="total-row"><td>Total Outgoing</td><td class="text-right">-</td><td class="text-right"><?php echo number_format($total_outgoing, 2); ?></td></tr>
        <tr class="<?php echo $net_position >= 0 ? 'net-positive' : 'net-negative'; ?>">
            <td><strong>Net Position (Collected - Outgoing)</strong></td>
            <td class="text-right">-</td>
            <td class="text-right"><strong>Rs. <?php echo number_format($net_position, 2); ?></strong></td>
        </tr>
    </tbody>
</table>

<!-- Sales detail -->
<h2>Sales — Invoices</h2>
<table>
    <thead>
        <tr><th>#</th><th>Date</th><th>Invoice</th><th>Tenant</th><th class="text-right">Rent</th><th class="text-right">Services</th><th class="text-right">Late Fee</th><th class="text-right">Paid</th></tr>
    </thead>
    <tbody>
        <?php if (empty($sales_rows)): ?>
            <tr><td colspan="8" class="text-center">No sales for this FY.</td></tr>
        <?php else: $i=1; foreach($sales_rows as $r): ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo $r['date'] ? date('d-M-Y', $r['date']) : '-'; ?></td>
                <td><?php echo html_escape($r['inv']); ?></td>
                <td><?php echo html_escape($r['tenant']); ?></td>
                <td class="text-right"><?php echo number_format($r['rent'], 2); ?></td>
                <td class="text-right"><?php echo number_format($r['svc_amt'], 2); ?></td>
                <td class="text-right"><?php echo number_format($r['late'], 2); ?></td>
                <td class="text-right"><?php echo number_format($r['paid'], 2); ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>

<!-- Purchases detail -->
<h2>Purchases</h2>
<table>
    <thead>
        <tr><th>#</th><th>Date</th><th>Head</th><th>Vendor</th><th>Description</th><th class="text-right">Amount (Rs.)</th></tr>
    </thead>
    <tbody>
        <?php if (empty($purchases_in_fy)): ?>
            <tr><td colspan="6" class="text-center">No purchases for this FY.</td></tr>
        <?php else: $i=1; foreach($purchases_in_fy as $p): ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo html_escape($p['purchase_date']); ?></td>
                <td><?php echo html_escape($p['purchase_head']); ?></td>
                <td><?php echo html_escape($p['vendor_name']); ?></td>
                <td><?php echo html_escape($p['description'] ?: '-'); ?></td>
                <td class="text-right"><?php echo number_format($p['amount'], 2); ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="5" class="text-right">Total Purchases</th><th class="text-right"><?php echo number_format($total_purchases, 2); ?></th></tr>
    </tfoot>
</table>

<!-- Expenses detail -->
<h2>Expenses</h2>
<table>
    <thead>
        <tr><th>#</th><th>Month</th><th>Year</th><th>Name</th><th>Description</th><th class="text-right">Amount (Rs.)</th></tr>
    </thead>
    <tbody>
        <?php if (empty($expenses_in_fy)): ?>
            <tr><td colspan="6" class="text-center">No expenses for this FY.</td></tr>
        <?php else: $i=1; foreach($expenses_in_fy as $e): ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo html_escape($e['month']); ?></td>
                <td class="text-right"><?php echo html_escape($e['year']); ?></td>
                <td><?php echo html_escape($e['name']); ?></td>
                <td><?php echo html_escape($e['description'] ?: '-'); ?></td>
                <td class="text-right"><?php echo number_format($e['amount'], 2); ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="5" class="text-right">Total Expenses</th><th class="text-right"><?php echo number_format($total_expenses, 2); ?></th></tr>
    </tfoot>
</table>

<!-- Salary detail -->
<h2>Salary Paid</h2>
<table>
    <thead>
        <tr><th>#</th><th>Month</th><th>Year</th><th>Staff</th><th class="text-right">Amount (Rs.)</th></tr>
    </thead>
    <tbody>
        <?php if (empty($salary_in_fy)): ?>
            <tr><td colspan="5" class="text-center">No salary payments for this FY.</td></tr>
        <?php else: $i=1; foreach($salary_in_fy as $s): ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo html_escape($s['month']); ?></td>
                <td class="text-right"><?php echo html_escape($s['year']); ?></td>
                <td><?php echo html_escape($s['staff_name']); ?></td>
                <td class="text-right"><?php echo number_format($s['amount'], 2); ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="4" class="text-right">Total Salary</th><th class="text-right"><?php echo number_format($total_salary, 2); ?></th></tr>
    </tfoot>
</table>

<p class="sub" style="margin-top: 20px;">
    This is a system-generated financial summary for the Financial Year <?php echo html_escape($fy); ?> (Indian FY: April to March). It can be used for ITR / CA filing purposes.
</p>

</body>
</html>
