<?php echo form_open('service_settings/update/' . $param2, array('id' => 'edit_service', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
	<label><?php echo $this->lang->line('service_name'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('service', array('service_id' => $param2))->row()->name); ?>" type="text" name="name" placeholder="<?php echo $this->lang->line('service_name_placeholder'); ?>" class="form-control" data-parsley-required="true">
</div>
<div class="form-group">
	<label><?php echo $this->lang->line('cost'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('service', array('service_id' => $param2))->row()->cost); ?>" type="text" name="cost" data-parsley-type="number" placeholder="<?php echo $this->lang->line('service_cost_placeholder'); ?>" class="form-control" data-parsley-required="true" min="0">
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#edit_service').parsley();
</script>