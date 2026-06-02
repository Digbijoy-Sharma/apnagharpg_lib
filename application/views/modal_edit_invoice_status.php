<?php echo form_open('invoices/update_status/' . $param2, array('id' => 'edit_invoice_status', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
    <label><?php echo $this->lang->line('status'); ?> *</label>
    <div>
        <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
            <option value=""><?php echo $this->lang->line('select_status'); ?></option>
            <option <?php if ($this->db->get_where('invoice', array('invoice_id' => $param2))->row()->status == 0) echo 'selected'; ?> value="0"><?php echo $this->lang->line('due'); ?></option>
            <option <?php if ($this->db->get_where('invoice', array('invoice_id' => $param2))->row()->status == 1) echo 'selected'; ?> value="1"><?php echo $this->lang->line('paid'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <label><?php echo $this->lang->line('payment_method'); ?></label>
    <div>
        <select style="width: 100%" class="form-control default-select2" name="payment_method_id">
            <option value=""><?php echo $this->lang->line('select_payment_method'); ?></option>
            <?php
                $payment_methods = $this->db->get('payment_method')->result_array();
                foreach ($payment_methods as $payment_method):
            ?>
            <option <?php if ($payment_method['payment_method_id'] == $this->db->get_where('invoice', array('invoice_id' => $param2))->row()->payment_method_id) echo 'selected'; ?> value="<?php echo $payment_method['payment_method_id']; ?>"><?php echo $payment_method['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label><?php echo $this->lang->line('late_fee'); ?> (<?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>)</label>
    <input value="<?php echo html_escape($this->db->get_where('invoice', array('invoice_id' => $param2))->row()->late_fee); ?>" type="text" name="late_fee" placeholder="<?php echo $this->lang->line('enter_late_fee'); ?>" class="form-control" data-parsley-type="number">
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php echo form_close(); ?>

<script>
    $('#edit_invoice_status').parsley();
    FormPlugins.init();

    $('select:not(.normal)').each(function() {
        $(this).select2({
            dropdownParent: $(this).parent()
        });
    });
</script>