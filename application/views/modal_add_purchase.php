<?php echo form_open_multipart('purchases/add', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
    <label>Purchase Date *</label>
    <input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" data-parsley-required="true">
</div>
<div class="form-group">
    <label>Purchase Head *</label>
    <input type="text" name="purchase_head" class="form-control" placeholder="Example: Grocery Stock / Furniture / Cleaning Material" data-parsley-required="true">
</div>
<div class="form-group">
    <label>Vendor / Shop Name</label>
    <input type="text" name="vendor_name" class="form-control" placeholder="Enter vendor or supplier name">
</div>
<div class="form-group">
    <label>Amount (<?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>) *</label>
    <input type="number" name="amount" step="0.01" min="0.01" class="form-control" placeholder="Enter purchase amount" data-parsley-required="true">
</div>
<div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="4" style="resize:none;" placeholder="Add purchase details, materials, notes, or bill references"></textarea>
</div>
<div class="form-group">
    <label>Upload Purchase Bill (Optional)</label>
    <input type="file" name="bill_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
</div>

<button type="submit" class="btn btn-success">Save Purchase</button>
<?php echo form_close(); ?>
