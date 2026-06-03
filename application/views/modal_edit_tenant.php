<?php $param2 = isset($param2) ? $param2 : ''; ?>
<?php echo form_open('tenants/update/' . $param2, array('id' => 'edit_tenant', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<?php
$tenant_info = $this->db->get_where('tenant', array('tenant_id' => $param2))->result_array();
foreach ($tenant_info as $tenant) :
?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label><?php echo $this->lang->line('name'); ?> *</label>
				<input value="<?php echo html_escape($tenant['name']); ?>" type="text" name="name" placeholder="<?php echo $this->lang->line('enter_name'); ?>" class="form-control" data-parsley-required="true">
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('mobile'); ?> *</label>
				<input value="<?php echo html_escape($tenant['mobile_number']); ?>" type="text" name="mobile_number" placeholder="<?php echo $this->lang->line('enter_mobile_number'); ?>" class="form-control" data-parsley-required="true">
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('email'); ?> (For student login)</label>
				<?php
				if ($this->db->get_where('user', array('user_type' => 3, 'person_id' => $tenant['tenant_id']))->num_rows() > 0) {
					$tenant_email = $this->db->get_where('user', array('user_type' => 3, 'person_id' => $tenant['tenant_id']))->row()->email;
				} else {
					$tenant_email = '';
				}
				?>
				<input value="<?php echo html_escape($tenant['email']); ?>" type="email" name="email" placeholder="<?php echo $this->lang->line('enter_email'); ?>" class="form-control">
			</div>
			<?php if (!$tenant_email) : ?>
				<div class="form-group">
					<label><?php echo $this->lang->line('password'); ?> (For student login)</label>
					<input type="text" name="password" id="password-indicator-visible" class="form-control m-b-5">
					<div id="passwordStrengthDiv2" class="is0 m-t-5"></div>
				</div>
				<div class="note note-yellow m-b-15">
					<span><?php echo $this->lang->line('default_password'); ?></span>
				</div>
			<?php endif; ?>
			<div class="form-group">
				<label><?php echo $this->lang->line('id_type'); ?></label>
				<div>
					<select style="width: 100%" class="form-control default-select2" name="id_type_id">
						<option value=""><?php echo $this->lang->line('select_id_type'); ?></option>
						<?php
						$id_types = $this->db->get('id_type')->result_array();
						foreach ($id_types as $id_type) :
						?>
							<option <?php if ($id_type['id_type_id'] == $tenant['id_type_id']) echo 'selected'; ?> value="<?php echo html_escape($id_type['id_type_id']); ?>"><?php echo html_escape($id_type['name']); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('id_number'); ?></label>
				<input value="<?php echo html_escape($tenant['id_number']); ?>" name="id_number" type="text" placeholder="<?php echo $this->lang->line('enter_id_number'); ?>" class="form-control">
			</div>
			<div class="form-group">
				<label>Plan Duration</label>
				<div class="input-group input-daterange">
					<input type="text" class="form-control" value="<?php echo $tenant['lease_start'] ? date('m/d/Y', $tenant['lease_start']) : ''; ?>" name="lease_start" placeholder="Plan start date" />
					<span class="input-group-addon">to</span>
					<input type="text" class="form-control" value="<?php echo $tenant['lease_end'] ? date('m/d/Y', $tenant['lease_end']) : ''; ?>" name="lease_end" placeholder="Plan end date (auto)" readonly />
				</div>
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('home_address'); ?></label>
				<input value="<?php echo html_escape(explode('<br>', $tenant['home_address'])[0]); ?>" name="home_address_line_1" type="text" placeholder="<?php echo $this->lang->line('enter_home_address_line_1'); ?>" class="form-control">
			</div>
			<div class="form-group">
				<input value="<?php echo html_escape(explode('<br>', $tenant['home_address'])[1]); ?>" name="home_address_line_2" type="text" placeholder="<?php echo $this->lang->line('enter_home_address_line_2'); ?>" class="form-control">
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('emergency_person'); ?></label>
				<input value="<?php echo html_escape($tenant['emergency_person']); ?>" type="text" name="emergency_person" placeholder="<?php echo $this->lang->line('enter_emergency_person_name'); ?>" class="form-control">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label><?php echo $this->lang->line('emergency_contact'); ?></label>
				<input value="<?php echo html_escape($tenant['emergency_contact']); ?>" type="text" name="emergency_contact" placeholder="<?php echo $this->lang->line('enter_emergency_person_mobile_number'); ?>" class="form-control">
			</div>
			<div class="form-group">
				<label>Seat</label>
				<div>
					<select style="width: 100%" class="form-control default-select2" name="room_id">
						<option value="">Select seat</option>
						<?php if ($tenant['room_id'] > 0) : ?>
							<?php $selected_seat = $this->db->get_where('room', array('room_id' => $tenant['room_id']))->row(); ?>
							<option value="<?php echo html_escape($tenant['room_id']); ?>"
								data-daily="<?php echo html_escape($selected_seat->daily_rent); ?>"
								data-monthly="<?php echo html_escape($selected_seat->monthly_rent); ?>"
								data-quarterly="<?php echo html_escape(isset($selected_seat->quarterly_price) ? $selected_seat->quarterly_price : 0); ?>"
								data-half-yearly="<?php echo html_escape(isset($selected_seat->half_yearly_price) ? $selected_seat->half_yearly_price : 0); ?>"
								data-yearly="<?php echo html_escape(isset($selected_seat->yearly_price) ? $selected_seat->yearly_price : 0); ?>"
								selected><?php echo html_escape($selected_seat->roomnumber . ' -> Seat ' . $selected_seat->room_number); ?></option>
						<?php
						endif;
						$rooms = $this->db->get_where('room', array('status' => 0))->result_array();
						foreach ($rooms as $room) :
						?>
							<option value="<?php echo html_escape($room['room_id']); ?>"
								data-daily="<?php echo html_escape($room['daily_rent']); ?>"
								data-monthly="<?php echo html_escape($room['monthly_rent']); ?>"
								data-quarterly="<?php echo html_escape(isset($room['quarterly_price']) ? $room['quarterly_price'] : 0); ?>"
								data-half-yearly="<?php echo html_escape(isset($room['half_yearly_price']) ? $room['half_yearly_price'] : 0); ?>"
								data-yearly="<?php echo html_escape(isset($room['yearly_price']) ? $room['yearly_price'] : 0); ?>">
								<?php echo html_escape($room['roomnumber'] . ' -> Seat ' . $room['room_number']); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="note note-yellow m-b-15">
				<span>To assign a seat, you must activate the student.</span>
			</div>
			<div class="form-group">
			<label>Shifts *</label>
			<div>
				<?php
				$current_tenant_id = isset($param2) ? $param2 : 0;
				$preselected_shifts = $current_tenant_id
					? $this->model->get_tenant_shift_ids($current_tenant_id)
					: array();
				if (empty($preselected_shifts) && !empty($tenant['shift_id'])) {
					$preselected_shifts = array((int) $tenant['shift_id']);
				}
				?>
				<select style="width: 100%" class="form-control default-select2" name="shift_ids[]" id="shift_ids_edit" multiple="multiple" data-parsley-required="true" data-placeholder="Select one or more shifts">
					<?php foreach ($this->db->order_by('shift_id', 'asc')->get('study_shift')->result_array() as $shift) : ?>
						<option <?php if (in_array((int) $shift['shift_id'], $preselected_shifts, true)) echo 'selected'; ?> value="<?php echo html_escape($shift['shift_id']); ?>"><?php echo html_escape($shift['shift_name'] . ' - ' . $shift['timing_label']); ?></option>
					<?php endforeach; ?>
				</select>
				<small class="text-muted">Hold Ctrl/Cmd to select multiple shifts. A student's plan price = sum of selected shifts.</small>
			</div>
		</div>
			<div class="form-group">
				<label>Plan *</label>
				<div>
					<select style="width: 100%" class="form-control default-select2" name="plan_type" data-parsley-required="true">
						<option value="">Select plan</option>
						<option <?php if ($tenant['plan_type'] == 'per_day') echo 'selected'; ?> value="per_day">Per Day Plan</option>
						<option <?php if ($tenant['plan_type'] == 'monthly') echo 'selected'; ?> value="monthly">Monthly Plan</option>
						<option <?php if ($tenant['plan_type'] == 'quarterly') echo 'selected'; ?> value="quarterly">3 Month Plan</option>
						<option <?php if ($tenant['plan_type'] == 'half_yearly') echo 'selected'; ?> value="half_yearly">6 Month Plan</option>
						<option <?php if ($tenant['plan_type'] == 'yearly') echo 'selected'; ?> value="yearly">12 Month Plan</option>
					</select>
				</div>
			</div>
			<div class="note note-info m-b-15">
				<span id="seat-plan-price-info">Select a seat and plan to see the seat-wise plan price.</span>
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('profession'); ?></label>
				<div>
					<select style="width: 100%" class="form-control default-select2" name="profession_id">
						<option value=""><?php echo $this->lang->line('select_profession'); ?></option>
						<?php
						$professions = $this->db->get('profession')->result_array();
						foreach ($professions as $profession) :
						?>
							<option <?php if ($profession['profession_id'] == $tenant['profession_id']) echo 'selected'; ?> value="<?php echo html_escape($profession['profession_id']); ?>"><?php echo html_escape($profession['name']); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('work_address'); ?></label>
				<input value="<?php echo html_escape(explode('<br>', $tenant['work_address'])[0]); ?>" name="work_address_line_1" type="text" placeholder="<?php echo $this->lang->line('enter_work_address_line_1'); ?>" class="form-control">
			</div>
			<div class="form-group">
				<input value="<?php echo html_escape(explode('<br>', $tenant['work_address'])[1]); ?>" name="work_address_line_2" type="text" placeholder="<?php echo $this->lang->line('enter_work_address_line_2'); ?>" class="form-control">
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('status'); ?> *</label>
				<div>
					<select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
						<option value=""><?php echo $this->lang->line('select_status'); ?></option>
						<option <?php if ($tenant['status'] == 1) echo 'selected'; ?> value="1"><?php echo $this->lang->line('active'); ?></option>
						<option <?php if ($tenant['status'] == 0) echo 'selected'; ?> value="0"><?php echo $this->lang->line('inactive'); ?></option>
					</select>
				</div>
			</div>
			<div class="note note-yellow m-b-15">
				<span>To activate a student, you must assign a seat.</span>
			</div>
			<div class="form-group">
				<label><?php echo $this->lang->line('extra_note'); ?></label>
				<textarea style="resize: none" type="text" name="extra_note" placeholder="<?php echo $this->lang->line('enter_extra_note'); ?>" class="form-control"><?php echo html_escape($tenant['extra_note']); ?></textarea>
			</div>
		</div>
	</div>

	<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php endforeach; ?>
<?php echo form_close(); ?>

<script>
	$('#edit_tenant').parsley();
	FormPlugins.init();

	$('.modal-dialog').css('max-width', '1080px');

	$('select:not(.normal)').each(function() {
		$(this).select2({
			dropdownParent: $(this).parent()
		});
	});

	function updatePlanEndDate() {
		var startDate = $('input[name="lease_start"]').val();
		var planType = $('select[name="plan_type"]').val();

		if (!startDate || !planType) {
			return;
		}

		var parts = startDate.split('/');
		if (parts.length !== 3) {
			return;
		}

		var start = new Date(parts[2], parts[0] - 1, parts[1]);
		if (Number.isNaN(start.getTime())) {
			return;
		}

		if (planType === 'per_day') {
			$('input[name="lease_end"]').val(startDate);
			return;
		}

		var end = new Date(start);
		var monthsToAdd = 1;
		if (planType === 'quarterly') monthsToAdd = 3;
		if (planType === 'half_yearly') monthsToAdd = 6;
		if (planType === 'yearly') monthsToAdd = 12;

		end.setMonth(end.getMonth() + monthsToAdd);
		end.setDate(end.getDate() - 1);

		var month = String(end.getMonth() + 1).padStart(2, '0');
		var day = String(end.getDate()).padStart(2, '0');
		var year = end.getFullYear();
		$('input[name="lease_end"]').val(month + '/' + day + '/' + year);
	}

	$('input[name="lease_start"]').on('change', updatePlanEndDate);
	$('select[name="plan_type"]').on('change', updatePlanEndDate);

	function updateSeatPlanPriceInfo() {
		var planType = $('select[name="plan_type"]').val();
		var selectedSeat = $('select[name="room_id"] option:selected');
		var price = '';
		var label = '';

		if (!selectedSeat.val() || !planType) {
			$('#seat-plan-price-info').text('Select a seat and plan to see the seat-wise plan price.');
			return;
		}

		if (planType === 'per_day') {
			price = selectedSeat.data('daily');
			label = 'Per Day Plan';
		} else if (planType === 'monthly') {
			price = selectedSeat.data('monthly');
			label = 'Monthly Plan';
		} else if (planType === 'quarterly') {
			price = selectedSeat.data('quarterly');
			label = '3 Month Plan';
		} else if (planType === 'half_yearly') {
			price = selectedSeat.data('half-yearly');
			label = '6 Month Plan';
		} else if (planType === 'yearly') {
			price = selectedSeat.data('yearly');
			label = '12 Month Plan';
		}

		$('#seat-plan-price-info').text(label + ' price for selected seat: Rs. ' + (parseFloat(price || 0).toFixed(2)));
	}

	$('select[name="room_id"]').on('change', updateSeatPlanPriceInfo);
	$('select[name="plan_type"]').on('change', updateSeatPlanPriceInfo);
	updateSeatPlanPriceInfo();
</script>
