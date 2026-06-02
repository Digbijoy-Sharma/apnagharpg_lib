<?php echo form_open(base_url() . 'invoices/add_transaction/' . $param2, array('data-parsley-validate' => 'true', 'name' => 'add_transaction')); ?>
<div class="form-group">
    <label>Amount *</label>
    <input type="text" name="amount" placeholder="Enter amount" class="form-control" data-parsley-required="true">
</div>
<div class="form-group">
    <label>Payment Method</label>
    <select name="payment_method_id" class="form-control" data-parsley-required="true">
        <option value="">Select Payment Method</option>
        <?php
        $payment_methods = $this->db->get('payment_method')->result_array();
        foreach ($payment_methods as $method) :
        ?>
            <option value="<?php echo $method['payment_method_id']; ?>"><?php echo $method['name']; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label>Reference Number</label>
    <input type="text" name="reference_number" placeholder="Enter Reference Number (Optional)" class="form-control">
</div>

<button type="submit" class="btn btn-success">Add Transaction</button>
<?php echo form_close(); ?>