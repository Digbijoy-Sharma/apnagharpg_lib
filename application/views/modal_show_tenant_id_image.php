<div class="form-group">
	<label><?php echo $this->lang->line('existing_front_image'); ?></label>
	<br>
	<?php if (html_escape($this->db->get_where('tenant', array('tenant_id' => $param2))->row()->id_front_image_link)) : ?>
		<img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($this->db->get_where('tenant', array('tenant_id' => $param2))->row()->id_front_image_link); ?>" alt="Image" class="img-thumbnail thumb128">
	<?php else : ?>
		<p><?php echo $this->lang->line('no_preview_available'); ?>.</p>
	<?php endif; ?>
</div>
<div class="form-group">
	<label><?php echo $this->lang->line('existing_back_image'); ?></label>
	<br>
	<?php if (html_escape($this->db->get_where('tenant', array('tenant_id' => $param2))->row()->id_back_image_link)) : ?>
		<img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($this->db->get_where('tenant', array('tenant_id' => $param2))->row()->id_back_image_link); ?>" alt="Image" class="img-thumbnail thumb128">
	<?php else : ?>
		<p><?php echo $this->lang->line('no_preview_available'); ?>.</p>
	<?php endif; ?>
</div>