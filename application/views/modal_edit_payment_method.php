<?php echo form_open('payment_method_settings/update/' . $param2, array('id' => 'edit_payment_method', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
	<label><?php echo $this->lang->line('payment_method_name'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('payment_method', array('payment_method_id' => $param2))->row()->name); ?>" type="text" name="name" placeholder="<?php echo $this->lang->line('payment_method_name_placeholder'); ?>" class="form-control" data-parsley-required="true">
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#edit_payment_method').parsley();
</script>