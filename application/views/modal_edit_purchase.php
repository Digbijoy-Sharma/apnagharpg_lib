<?php $param2 = isset($param2) ? $param2 : ''; ?>
<?php echo form_open_multipart('purchases/update/' . $param2, array('id' => 'edit_purchase', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<?php
$purchase_details = $this->db->get_where('purchase', array('purchase_id' => $param2))->result_array();
foreach ($purchase_details as $row) :
?>
    <div class="form-group">
        <label>Purchase Date *</label>
        <input type="date" name="purchase_date" class="form-control" value="<?php echo html_escape($row['purchase_date']); ?>" data-parsley-required="true">
    </div>
    <div class="form-group">
        <label>Purchase Head *</label>
        <input type="text" name="purchase_head" class="form-control" value="<?php echo html_escape($row['purchase_head']); ?>" data-parsley-required="true">
    </div>
    <div class="form-group">
        <label>Vendor / Shop Name</label>
        <input type="text" name="vendor_name" class="form-control" value="<?php echo html_escape($row['vendor_name']); ?>">
    </div>
    <div class="form-group">
        <label>Amount (<?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>) *</label>
        <input type="number" name="amount" step="0.01" min="0.01" class="form-control" value="<?php echo html_escape($row['amount']); ?>" data-parsley-required="true">
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="4" style="resize:none;"><?php echo html_escape($row['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label>Replace Purchase Bill</label>
        <input type="file" name="bill_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    </div>

    <button type="submit" class="btn btn-primary">Update Purchase</button>
<?php endforeach; ?>
<?php echo form_close(); ?>

<script>
    $('#edit_purchase').parsley();
    FormPlugins.init();
</script>
