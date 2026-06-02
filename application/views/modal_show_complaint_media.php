<div class="form-group">
	<label><?php echo $this->lang->line('pictures_related_to_complaint'); ?></label>
	<br>
	<?php if ($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->complaint_picture_1) : ?>
		<embed src="<?php echo base_url(); ?>uploads/complaints/<?php echo html_escape($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->complaint_picture_1); ?>" width="100%" height="640px">
	<?php else : ?>
		<p><?php echo $this->lang->line('no_preview_available'); ?></p>
	<?php endif; ?>
	<?php if ($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->complaint_picture_2) : ?>
		<embed src="<?php echo base_url(); ?>uploads/complaints/<?php echo html_escape($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->complaint_picture_2); ?>" width="100%" height="640px">
	<?php else : ?>
		<p><?php echo $this->lang->line('no_preview_available'); ?></p>
	<?php endif; ?>
</div>

<div class="form-group">
	<label><?php echo $this->lang->line('video_related_to_complaint'); ?></label>
	<br>
	<?php if ($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->complaint_video) : ?>
		<embed src="<?php echo base_url(); ?>uploads/complaints/<?php echo html_escape($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->complaint_video); ?>" width="100%" height="640px">
	<?php else : ?>
		<p><?php echo $this->lang->line('no_preview_available'); ?></p>
	<?php endif; ?>
</div>

<script>
	$('.modal-dialog').css('max-width', '720px');
</script>