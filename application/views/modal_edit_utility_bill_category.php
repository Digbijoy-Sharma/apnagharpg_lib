<?php echo form_open('utility_bill_categories/update/' . $param2, array('id' => 'edit_utility_bill_category', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
	<label><?php echo $this->lang->line('name'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('utility_bill_category', array('utility_bill_category_id' => $param2))->row()->name); ?>" type="text" name="name" placeholder="<?php echo $this->lang->line('utility_bill_category_name_placeholder'); ?>" class="form-control" data-parsley-required="true">
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#edit_utility_bill_category').parsley();
	FormPlugins.init();
</script>