<?php
$invoice_id = $param2;
$transactions = [];
if ($this->db->table_exists('invoice_transaction')) {
    $this->db->where('invoice_id', $invoice_id);
    $this->db->order_by('created_on', 'DESC');
    $transactions = $this->db->get('invoice_transaction')->result_array();
}
?>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Ref Number</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
            <tr>
                <td colspan="5" class="text-center">No transactions found for this invoice.</td>
            </tr>
            <?php else: ?>
                <?php $count = 1; foreach($transactions as $row): 
                    $method_name = 'N/A';
                    if ($row['payment_method_id']) {
                        $method_query = $this->db->get_where('payment_method', array('payment_method_id' => $row['payment_method_id']));
                        if ($method_query->num_rows() > 0) {
                            $method_name = $method_query->row()->name;
                        }
                    }
                ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo date('d M Y, h:i A', $row['created_on']); ?></td>
                    <td><?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo html_escape($method_name); ?></td>
                    <td><?php echo html_escape($row['reference_number'] ? $row['reference_number'] : 'N/A'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>