<?php echo form_open('notices/update/' . $param2, array('id' => 'update_notice_details', 'class' => 'form-horizontal', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<?php
$notice_details = $this->security->xss_clean($this->db->get_where('notice', array('notice_id' => $param2))->result_array());
foreach ($notice_details as $row) :
?>
	<div class="form-group">
		<label><?php echo $this->lang->line('title'); ?> *</label>
		<input value="<?php echo $row['title']; ?>" name="title" type="text" data-parsley-required="true" class="form-control" placeholder="<?php echo $this->lang->line('add_notice_title_placeholder'); ?>" />
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('notice'); ?> *</label>
		<textarea class="ckeditor" id="ckeditor1" name="notice" data-parsley-required="true"><?php echo $row['notice']; ?></textarea>
	</div>

	<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php endforeach; ?>
<?php echo form_close(); ?>

<script>
	"use strict";

	$('#update_notice_details').parsley();
	CKEDITOR.replace('ckeditor1');
</script>