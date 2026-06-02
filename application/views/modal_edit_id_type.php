<?php echo form_open('id_type_settings/update/' . $param2, array('id' => 'edit_id_type', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
	<label><?php echo $this->lang->line('id_type_name'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('id_type', array('id_type_id' => $param2))->row()->name); ?>" type="text" name="name" placeholder="<?php echo $this->lang->line('id_type_name_placeholder'); ?>" class="form-control" data-parsley-required="true">
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#edit_id_type').parsley();
</script>