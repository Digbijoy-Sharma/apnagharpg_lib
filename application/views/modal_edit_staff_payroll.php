<?php echo form_open('staff_payroll/update/' . $param2, array('id' => 'edit_staff_salary', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<?php
$staff_salary_details = $this->db->get_where('staff_salary', array('staff_salary_id' => $param2))->result_array();
foreach ($staff_salary_details as $row) :
?>
	<div class="form-group">
		<label><?php echo $this->lang->line('name'); ?> *</label>
		<select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="staff_id">
			<option value=""><?php echo $this->lang->line('select_staff'); ?></option>
			<?php
			$staff = $this->db->get('staff')->result_array();
			foreach ($staff as $row2) :
			?>
				<option <?php if ($row2['staff_id'] == $row['staff_id']) echo 'selected'; ?> value="<?php echo html_escape($row2['staff_id']); ?>"><?php echo html_escape($row2['name']); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('year'); ?> *</label>
		<select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="year">
			<option value=""><?php echo $this->lang->line('select_year'); ?></option>
			<option <?php if ($row['year'] == date('Y') - 4) echo 'selected'; ?> value="<?php echo date('Y') - 4; ?>"><?php echo date('Y') - 4; ?></option>
			<option <?php if ($row['year'] == date('Y') - 3) echo 'selected'; ?> value="<?php echo date('Y') - 3; ?>"><?php echo date('Y') - 3; ?></option>
			<option <?php if ($row['year'] == date('Y') - 2) echo 'selected'; ?> value="<?php echo date('Y') - 2; ?>"><?php echo date('Y') - 2; ?></option>
			<option <?php if ($row['year'] == date('Y') - 1) echo 'selected'; ?> value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
			<option <?php if ($row['year'] == date('Y')) echo 'selected'; ?> value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
			<option <?php if ($row['year'] == date('Y') + 1) echo 'selected'; ?> value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
			<option <?php if ($row['year'] == date('Y') + 2) echo 'selected'; ?> value="<?php echo date('Y') + 2; ?>"><?php echo date('Y') + 2; ?></option>
			<option <?php if ($row['year'] == date('Y') + 3) echo 'selected'; ?> value="<?php echo date('Y') + 3; ?>"><?php echo date('Y') + 3; ?></option>
			<option <?php if ($row['year'] == date('Y') + 4) echo 'selected'; ?> value="<?php echo date('Y') + 4; ?>"><?php echo date('Y') + 4; ?></option>
		</select>
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('month'); ?> *</label>
		<select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="month">
			<option value=""><?php echo $this->lang->line('select_month'); ?></option>
			<option <?php if ($row['month'] == 'January') echo 'selected'; ?> value="January"><?php echo $this->lang->line('january'); ?></option>
			<option <?php if ($row['month'] == 'February') echo 'selected'; ?> value="February"><?php echo $this->lang->line('february'); ?></option>
			<option <?php if ($row['month'] == 'March') echo 'selected'; ?> value="March"><?php echo $this->lang->line('march'); ?></option>
			<option <?php if ($row['month'] == 'April') echo 'selected'; ?> value="April"><?php echo $this->lang->line('april'); ?></option>
			<option <?php if ($row['month'] == 'May') echo 'selected'; ?> value="May"><?php echo $this->lang->line('may'); ?></option>
			<option <?php if ($row['month'] == 'June') echo 'selected'; ?> value="June"><?php echo $this->lang->line('june'); ?></option>
			<option <?php if ($row['month'] == 'July') echo 'selected'; ?> value="July"><?php echo $this->lang->line('july'); ?></option>
			<option <?php if ($row['month'] == 'August') echo 'selected'; ?> value="August"><?php echo $this->lang->line('august'); ?></option>
			<option <?php if ($row['month'] == 'September') echo 'selected'; ?> value="September"><?php echo $this->lang->line('september'); ?></option>
			<option <?php if ($row['month'] == 'October') echo 'selected'; ?> value="October"><?php echo $this->lang->line('october'); ?></option>
			<option <?php if ($row['month'] == 'November') echo 'selected'; ?> value="November"><?php echo $this->lang->line('november'); ?></option>
			<option <?php if ($row['month'] == 'December') echo 'selected'; ?> value="December"><?php echo $this->lang->line('december'); ?></option>
		</select>
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('amount'); ?> (<?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>) *</label>
		<input value="<?php echo html_escape($row['amount']); ?>" type="text" name="amount" placeholder="<?php echo $this->lang->line('enter_amount'); ?>" class="form-control" data-parsley-required="true" data-parsley-type="number" min="0">
	</div>
	<div class="form-group">
		<label><?php echo $this->lang->line('status'); ?> *</label>
		<select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
			<option value=""><?php echo $this->lang->line('select_status'); ?></option>
			<option <?php if ($row['status'] == 0) echo 'selected' ?> value="0"><?php echo $this->lang->line('due'); ?></option>
			<option <?php if ($row['status'] == 1) echo 'selected' ?> value="1"><?php echo $this->lang->line('paid'); ?></option>
		</select>
	</div>

	<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php endforeach; ?>
<?php echo form_close(); ?>

<script>
	$('#edit_staff_salary').parsley();
	FormPlugins.init();

	$('select:not(.normal)').each(function() {
		$(this).select2({
			dropdownParent: $(this).parent()
		});
	});
</script>