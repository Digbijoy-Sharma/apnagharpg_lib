<?php echo form_open_multipart('board_member_settings/update/' . $param2, array('id' => 'edit_board_member', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
	<label><?php echo $this->lang->line('board_member_name'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('board_member', array('board_member_id' => $param2))->row()->name); ?>" type="text" name="name" placeholder="<?php echo $this->lang->line('board_member_name_placeholder'); ?>" class="form-control" data-parsley-required="true">
</div>
<div class="form-group">
	<label><?php echo $this->lang->line('board_member_image'); ?></label>
	<br>
	<?php if ($this->db->get_where('board_member', array('board_member_id' => $param2))->row()->image): ?>
		<img id="image-preview" width="90px" src="<?php echo base_url(); ?>uploads/board_members/<?php echo html_escape($this->db->get_where('board_member', array('board_member_id' => $param2))->row()->image); ?>" class="media-object" />
	<?php else: ?>
		<img id="image-preview" width="90px" src="<?php echo base_url('assets/img/tenant.png'); ?>" class="media-object" />
	<?php endif; ?>
	<br>
	<br>
	<span class="btn btn-primary fileinput-button">
		<i class="fa fa-plus"></i>
		<span><?php echo $this->lang->line('add_file'); ?></span>
		<input class="form-control" type="file" name="image">
	</span>
</div>
<div class="form-group">
	<label><?php echo $this->lang->line('board_member_position'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('board_member', array('board_member_id' => $param2))->row()->position); ?>" type="text" name="position" placeholder="<?php echo $this->lang->line('board_member_position_placeholder'); ?>" class="form-control" data-parsley-required="true">
</div>
<div class="form-group">
	<label><?php echo $this->lang->line('board_member_serial'); ?> *</label>
	<input value="<?php echo html_escape($this->db->get_where('board_member', array('board_member_id' => $param2))->row()->serial); ?>" type="text" data-parsley-type="number" name="serial" placeholder="<?php echo $this->lang->line('board_member_serial_placeholder'); ?>" class="form-control" data-parsley-required="true" min="0">
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#edit_board_member').parsley();
</script>

<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>