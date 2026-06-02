<?php echo form_open('complaints/update/' . $param2, array('id' => 'reply_to_complaint', 'class' => 'form-horizontal', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
	<label><?php echo $this->lang->line('details'); ?></label>
	<textarea rows="10" style="resize: none" type="text" name="content" placeholder="<?php echo $this->lang->line('add_complaint_details_placeholder'); ?>" class="form-control"></textarea>
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('reply'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#reply_to_complaint').parsley();
</script>