<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Monthly Expense Report - <?php echo html_escape($month . ' ' . $year); ?></title>
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
</style>
</head>
<body>
<?php
$system_name_row = $this->db->get_where('setting', array('name' => 'system_name'))->row();
$system_name = $system_name_row ? $system_name_row->content : 'Library';
?>
<h1><?php echo html_escape($system_name); ?> — Monthly Expense Report</h1>
<div class="sub">Period: <strong><?php echo html_escape($month . ' ' . $year); ?></strong> &nbsp; | &nbsp; Generated: <?php echo date('d M Y, h:i A'); ?></div>

<?php
$expenses = $this->db->get_where('expense', array('month' => $month, 'year' => (int)$year))->result_array();
$total = 0;
?>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Name</th>
            <th>Description</th>
            <th class="text-right">Amount (Rs.)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($expenses)): ?>
            <tr><td colspan="5" class="text-center">No expenses for this period.</td></tr>
        <?php else: $i=1; foreach($expenses as $e): $total += (float)$e['amount']; ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo $e['timestamp'] ? date('d-M-Y', $e['timestamp']) : '-'; ?></td>
                <td><?php echo html_escape($e['name']); ?></td>
                <td><?php echo html_escape($e['description'] ?: '-'); ?></td>
                <td class="text-right"><?php echo number_format($e['amount'], 2); ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right">Total:</th>
            <th class="text-right"><?php echo number_format($total, 2); ?></th>
        </tr>
    </tfoot>
</table>

</body>
</html>
