<?php echo form_open_multipart('tenants/change_id_image/' . $param2, array('id' => 'change_tenant_id_image', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
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

<div class="form-group">
	<label><?php echo $this->lang->line('for_new_image'); ?></label>
	<br>
	<img id="id-front-preview" width="90px" src="<?php echo base_url('assets/img/tenant.png'); ?>" class="media-object" />
	<img id="id-back-preview" width="90px" src="<?php echo base_url('assets/img/tenant.png'); ?>" class="media-object" />
	<br>
	<br>
	<span class="btn btn-primary fileinput-button">
		<i class="fa fa-plus"></i>
		<span><?php echo $this->lang->line('add_file_front'); ?></span>
		<input onchange="readIdFrontURL(this);" class="form-control" type="file" name="id_front_image_link">
	</span>
	<span class="btn btn-primary fileinput-button">
		<i class="fa fa-plus"></i>
		<span><?php echo $this->lang->line('add_file_back'); ?></span>
		<input onchange="readIdBackURL(this);" class="form-control" type="file" name="id_back_image_link">
	</span>
</div>

<hr>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('change'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#change_tenant_id_image').parsley();

	function readIdFrontURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#id-front-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function readIdBackURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#id-back-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>