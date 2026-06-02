<?php
$invoice = $this->db->get_where('invoice', array('invoice_id' => $param2))->row();
?>
<?php echo form_open(base_url() . 'invoices/update_invoice_number/' . $param2, array('data-parsley-validate' => 'true', 'name' => 'edit_invoice_number')); ?>
<div class="form-group">
    <label>Invoice Number *</label>
    <input type="text" name="invoice_number" value="<?php echo html_escape($invoice->invoice_number); ?>" class="form-control" data-parsley-required="true">
</div>

<button type="submit" class="btn btn-success">Update Invoice Number</button>
<?php echo form_close(); ?>