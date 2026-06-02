<?php echo form_open('staff/update/' . $param2, array('id' => 'edit_staff', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<?php
$staff_details = $this->db->get_where('staff', array('staff_id' => $param2))->result_array();
foreach ($staff_details as $row) :
?>
	<div class="form-group">
		<label><?php echo $this->lang->line('name'); ?> *</label>
		<input value="<?php echo html_escape($row['name']); ?>" type="text" name="name" placeholder="<?php echo $this->lang->line('enter_name'); ?>" class="form-control" data-parsley-required="true">
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('role'); ?></label>
		<input value="<?php echo html_escape($row['role']); ?>" type="text" name="role" placeholder="<?php echo $this->lang->line('add_staff_role_placeholder'); ?>" class="form-control">
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('mobile_number'); ?> *</label>
		<input value="<?php echo html_escape($row['mobile_number']); ?>" type="text" name="mobile_number" placeholder="<?php echo $this->lang->line('enter_mobile_number'); ?>" class="form-control" data-parsley-required="true">
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('email'); ?> (<?php echo $this->lang->line('for_staff_login'); ?>)</label>
		<?php
		if ($this->db->get_where('user', array('user_type' => 2, 'person_id' => $row['staff_id']))->num_rows() > 0) {
			$staff_email = $this->db->get_where('user', array('user_type' => 2, 'person_id' => $row['staff_id']))->row()->email;
		} else {
			$staff_email = '';
		}
		?>
		<input value="<?php echo $staff_email ? html_escape($staff_email) : ''; ?>" type="email" name="email" placeholder="<?php echo $this->lang->line('enter_email'); ?>" class="form-control">
	</div>
	<?php if (!$staff_email) : ?>
		<div class="form-group">
			<label><?php echo $this->lang->line('password'); ?> (<?php echo $this->lang->line('for_staff_login'); ?>)</label>
			<input type="text" name="password" id="password-indicator-visible" class="form-control m-b-5">
			<div id="passwordStrengthDiv2" class="is0 m-t-5"></div>
		</div>
		<div class="note note-secondary note-purple m-b-15">
			<span><?php echo $this->lang->line('default_password'); ?></span>
		</div>
	<?php endif; ?>
	<div class="form-group">
		<label><?php echo $this->lang->line('permission'); ?> *</label>
		<div class="row">
			<?php
				$permissions = $this->db->get_where('user', array('person_id' => $row['staff_id'], 'user_type' => 2))->row()->permissions;
				if (isset($permissions)) $permissions_no_comma = explode(",", $permissions);
				$modules = $this->db->get('module')->result_array();
				foreach ($modules as $module) :
			?>
			<div class="col-md-4">
				<div class="checkbox checkbox-css">
					<input type="checkbox" id="<?php echo html_escape($module['module_id']); ?>" value="<?php echo html_escape($module['module_id']); ?>" name="permission[]" <?php if (isset($permissions_no_comma) && in_array($module['module_id'], $permissions_no_comma)) echo 'checked'; ?> />
					<label for="<?php echo html_escape($module['module_id']); ?>"><?php echo $this->lang->line(strtolower($module['module_name'])); ?></label>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('status'); ?> *</label>
		<div>
			<select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
				<option value=""><?php echo $this->lang->line('select_status'); ?></option>
				<option <?php if ($row['status'] == 0) echo 'selected'; ?> value="0"><?php echo $this->lang->line('inactive'); ?></option>
				<option <?php if ($row['status'] == 1) echo 'selected'; ?> value="1"><?php echo $this->lang->line('active'); ?></option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('remarks'); ?></label>
		<textarea style="resize: none" type="text" name="remarks" placeholder="<?php echo $this->lang->line('enter_remarks'); ?>" class="form-control"><?php echo html_escape($row['remarks']); ?></textarea>
	</div>

	<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php endforeach; ?>
<?php echo form_close(); ?>

<script>
	$('#edit_staff').parsley();
	FormPlugins.init();

	$('select:not(.normal)').each(function() {
		$(this).select2({
			dropdownParent: $(this).parent()
		});
	});
</script>