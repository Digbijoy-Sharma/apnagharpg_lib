<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model extends CI_Model
{
	private function ensure_upload_directory($relative_path)
	{
		$upload_dir = FCPATH . trim($relative_path, '/') . '/';

		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0777, true);
		}

		@chmod($upload_dir, 0777);

		return $upload_dir;
	}

	public function ensure_library_plan_schema()
	{
		if (!$this->db->field_exists('quarterly_price', 'room')) {
			$this->db->query("ALTER TABLE room ADD COLUMN quarterly_price DECIMAL(12,2) DEFAULT 0.00 AFTER monthly_rent");
		}

		if (!$this->db->field_exists('half_yearly_price', 'room')) {
			$this->db->query("ALTER TABLE room ADD COLUMN half_yearly_price DECIMAL(12,2) DEFAULT 0.00 AFTER quarterly_price");
		}

		if (!$this->db->field_exists('yearly_price', 'room')) {
			$this->db->query("ALTER TABLE room ADD COLUMN yearly_price DECIMAL(12,2) DEFAULT 0.00 AFTER half_yearly_price");
		}

		if (!$this->db->field_exists('shift_id', 'tenant')) {
			$this->db->query("ALTER TABLE tenant ADD COLUMN shift_id INT(11) NULL AFTER room_id");
		}

		if (!$this->db->field_exists('plan_type', 'tenant')) {
			$this->db->query("ALTER TABLE tenant ADD COLUMN plan_type VARCHAR(30) NULL AFTER shift_id");
		}

		if (!$this->db->field_exists('shift_name', 'invoice')) {
			$this->db->query("ALTER TABLE invoice ADD COLUMN shift_name VARCHAR(100) NULL AFTER room_number");
		}

		if (!$this->db->field_exists('plan_type', 'invoice')) {
			$this->db->query("ALTER TABLE invoice ADD COLUMN plan_type VARCHAR(30) NULL AFTER shift_name");
		}

		if (!$this->db->field_exists('seat_label', 'invoice')) {
			$this->db->query("ALTER TABLE invoice ADD COLUMN seat_label VARCHAR(150) NULL AFTER plan_type");
		}

		if (!$this->db->field_exists('plan_type', 'tenant_rent')) {
			$this->db->query("ALTER TABLE tenant_rent ADD COLUMN plan_type VARCHAR(30) NULL AFTER tenant_id");
		}

		if (!$this->db->field_exists('shift_name', 'tenant_rent')) {
			$this->db->query("ALTER TABLE tenant_rent ADD COLUMN shift_name VARCHAR(100) NULL AFTER plan_type");
		}

		$this->db->query("CREATE TABLE IF NOT EXISTS study_shift (
			shift_id INT(11) AUTO_INCREMENT PRIMARY KEY,
			shift_name VARCHAR(100),
			timing_label VARCHAR(255),
			start_time VARCHAR(20),
			end_time VARCHAR(20),
			daily_price DECIMAL(12,2) DEFAULT 0.00,
			monthly_price DECIMAL(12,2) DEFAULT 0.00,
			quarterly_price DECIMAL(12,2) DEFAULT 0.00,
			half_yearly_price DECIMAL(12,2) DEFAULT 0.00,
			yearly_price DECIMAL(12,2) DEFAULT 0.00,
			status TINYINT(1) DEFAULT 1,
			created_on INT(11),
			created_by INT(11),
			timestamp INT(11),
			updated_by INT(11)
		)");

		$this->db->query("CREATE TABLE IF NOT EXISTS tenant_shift (
			tenant_shift_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			tenant_id INT(11) NOT NULL,
			shift_id INT(11) NOT NULL,
			created_on INT(11),
			created_by INT(11),
			timestamp INT(11),
			updated_by INT(11),
			UNIQUE KEY uniq_tenant_shift (tenant_id, shift_id),
			KEY tenant_id (tenant_id),
			KEY shift_id (shift_id)
		)");

		if (!$this->db->count_all('study_shift')) {
			$now = time();
			$default_shifts = array(
				array(
					'shift_name' => 'Shift 1',
					'timing_label' => '6:30 AM - 11:00 AM',
					'start_time' => '06:30 AM',
					'end_time' => '11:00 AM',
					'daily_price' => 33.33,
					'monthly_price' => 1000.00,
					'quarterly_price' => 3000.00,
					'half_yearly_price' => 6000.00,
					'yearly_price' => 12000.00
				),
				array(
					'shift_name' => 'Shift 2',
					'timing_label' => '11:00 AM - 5:00 PM',
					'start_time' => '11:00 AM',
					'end_time' => '05:00 PM',
					'daily_price' => 40.00,
					'monthly_price' => 1200.00,
					'quarterly_price' => 3600.00,
					'half_yearly_price' => 7200.00,
					'yearly_price' => 14400.00
				),
				array(
					'shift_name' => 'Shift 3',
					'timing_label' => '5:00 PM - 11:00 PM',
					'start_time' => '05:00 PM',
					'end_time' => '11:00 PM',
					'daily_price' => 40.00,
					'monthly_price' => 1200.00,
					'quarterly_price' => 3600.00,
					'half_yearly_price' => 7200.00,
					'yearly_price' => 14400.00
				),
				array(
					'shift_name' => 'Shift 4',
					'timing_label' => '6:30 AM - 11:00 PM',
					'start_time' => '06:30 AM',
					'end_time' => '11:00 PM',
					'daily_price' => 80.00,
					'monthly_price' => 2400.00,
					'quarterly_price' => 7200.00,
					'half_yearly_price' => 14400.00,
					'yearly_price' => 28800.00
				),
			);

			foreach ($default_shifts as $shift) {
				$shift['status'] = 1;
				$shift['created_on'] = $now;
				$shift['created_by'] = $this->session->userdata('user_id');
				$shift['timestamp'] = $now;
				$shift['updated_by'] = $this->session->userdata('user_id');
				$this->db->insert('study_shift', $shift);
			}
		}

		// Backfill: any existing tenant with a single shift_id that has no tenant_shift row yet
		$existing = $this->db->query("
			SELECT t.tenant_id, t.shift_id
			FROM tenant t
			LEFT JOIN tenant_shift ts ON ts.tenant_id = t.tenant_id
			WHERE t.shift_id > 0 AND ts.tenant_shift_id IS NULL
		")->result_array();
		foreach ($existing as $row) {
			$now = time();
			$this->db->insert('tenant_shift', array(
				'tenant_id'  => $row['tenant_id'],
				'shift_id'   => $row['shift_id'],
				'created_on' => $now,
				'created_by' => $this->session->userdata('user_id'),
				'timestamp'  => $now,
				'updated_by' => $this->session->userdata('user_id')
			));
		}
	}

	private function get_plan_label($plan_type)
	{
		$labels = array(
			'per_day' => 'Per Day Plan',
			'monthly' => 'Monthly Plan',
			'quarterly' => '3 Month Plan',
			'half_yearly' => '6 Month Plan',
			'yearly' => '12 Month Plan'
		);

		return isset($labels[$plan_type]) ? $labels[$plan_type] : 'Study Plan';
	}

	private function calculate_plan_end($start_timestamp, $plan_type)
	{
		if (!$start_timestamp) {
			return 0;
		}

		switch ($plan_type) {
			case 'per_day':
				return strtotime(date('Y-m-d 23:59:59', $start_timestamp));
			case 'monthly':
				return strtotime('-1 day', strtotime('+1 month', strtotime(date('Y-m-d', $start_timestamp))));
			case 'quarterly':
				return strtotime('-1 day', strtotime('+3 months', strtotime(date('Y-m-d', $start_timestamp))));
			case 'half_yearly':
				return strtotime('-1 day', strtotime('+6 months', strtotime(date('Y-m-d', $start_timestamp))));
			case 'yearly':
				return strtotime('-1 day', strtotime('+12 months', strtotime(date('Y-m-d', $start_timestamp))));
			default:
				return $start_timestamp;
		}
	}

	private function build_seat_label($room_id = 0)
	{
		if (!$room_id) {
			return 'Seat Not Assigned';
		}

		$room = $this->db->get_where('room', array('room_id' => $room_id))->row();
		if (!$room) {
			return 'Seat Not Assigned';
		}

		$area_name = !empty($room->roomnumber) ? $room->roomnumber . ' / ' : '';

		return trim($area_name . 'Seat ' . $room->room_number);
	}

	private function get_shift_plan_amount($shift, $plan_type)
	{
		if (!$shift) {
			return 0;
		}

		switch ($plan_type) {
			case 'per_day':
				return (float) $shift->daily_price;
			case 'monthly':
				return (float) $shift->monthly_price;
			case 'quarterly':
				return (float) $shift->quarterly_price;
			case 'half_yearly':
				return (float) $shift->half_yearly_price;
			case 'yearly':
				return (float) $shift->yearly_price;
			default:
				return 0;
		}
	}

	public function get_tenant_shift_ids($tenant_id = 0)
	{
		if (!$tenant_id) {
			return array();
		}
		$rows = $this->db
			->select('shift_id')
			->from('tenant_shift')
			->where('tenant_id', $tenant_id)
			->get()
			->result_array();
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = (int) $row['shift_id'];
		}
		return $ids;
	}

	public function get_tenant_shifts($tenant_id = 0)
	{
		if (!$tenant_id) {
			return array();
		}
		return $this->db
			->select('s.shift_id, s.shift_name, s.timing_label, s.start_time, s.end_time,
				s.daily_price, s.monthly_price, s.quarterly_price, s.half_yearly_price, s.yearly_price, s.status')
			->from('tenant_shift ts')
			->join('study_shift s', 's.shift_id = ts.shift_id', 'left')
			->where('ts.tenant_id', $tenant_id)
			->order_by('s.shift_id', 'asc')
			->get()
			->result_array();
	}

	public function set_tenant_shifts($tenant_id = 0, $shift_ids = array())
	{
		if (!$tenant_id) {
			return false;
		}
		$this->db->where('tenant_id', $tenant_id)->delete('tenant_shift');

		if (!is_array($shift_ids)) {
			$shift_ids = array($shift_ids);
		}
		$shift_ids = array_values(array_unique(array_filter(array_map('intval', $shift_ids))));

		if (empty($shift_ids)) {
			return true;
		}

		$now = time();
		$user_id = $this->session->userdata('user_id');
		$rows = array();
		foreach ($shift_ids as $shift_id) {
			$rows[] = array(
				'tenant_id'  => $tenant_id,
				'shift_id'   => $shift_id,
				'created_on' => $now,
				'created_by' => $user_id,
				'timestamp'  => $now,
				'updated_by' => $user_id
			);
		}
		return $this->db->insert_batch('tenant_shift', $rows);
	}

	private function get_tenant_shifts_plan_amount($shifts, $plan_type)
	{
		if (empty($shifts)) {
			return 0;
		}
		$total = 0;
		foreach ($shifts as $shift) {
			$total += $this->get_shift_plan_amount((object) $shift, $plan_type);
		}
		return $total;
	}

	private function build_tenant_shifts_label($shifts)
	{
		if (empty($shifts)) {
			return null;
		}
		$parts = array();
		foreach ($shifts as $shift) {
			if (!empty($shift['shift_name'])) {
				$parts[] = $shift['shift_name'] . ' (' . $shift['timing_label'] . ')';
			}
		}
		return empty($parts) ? null : implode(', ', $parts);
	}

	private function build_tenant_shifts_short_label($shifts)
	{
		if (empty($shifts)) {
			return null;
		}
		$parts = array();
		foreach ($shifts as $shift) {
			if (!empty($shift['shift_name'])) {
				$parts[] = $shift['shift_name'];
			}
		}
		return empty($parts) ? null : implode(', ', $parts);
	}

	private function get_seat_plan_amount($seat, $plan_type)
	{
		if (!$seat) {
			return 0;
		}

		switch ($plan_type) {
			case 'per_day':
				return (float) $seat->daily_rent;
			case 'monthly':
				return (float) $seat->monthly_rent;
			case 'quarterly':
				return (float) $seat->quarterly_price;
			case 'half_yearly':
				return (float) $seat->half_yearly_price;
			case 'yearly':
				return (float) $seat->yearly_price;
			default:
				return 0;
		}
	}

	private function sync_student_plan_dates(&$data)
	{
		$plan_start = $this->input->post('lease_start');
		if (!$plan_start) {
			return;
		}

		$start_timestamp = strtotime($plan_start);
		if (!$start_timestamp) {
			return;
		}

		$data['lease_start'] = $start_timestamp;
		$data['lease_end'] = $this->calculate_plan_end($start_timestamp, $this->input->post('plan_type'));
	}

	private function create_library_plan_invoice($tenant_id = '', $status = 0, $start_date = '', $due_date = '')
	{
		$this->ensure_library_plan_schema();

		$tenant = $this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row();
		if (!$tenant) {
			return false;
		}

		if (empty($tenant->room_id) || empty($tenant->plan_type)) {
			return false;
		}

		$seat = $this->db->get_where('room', array('room_id' => $tenant->room_id))->row();
		if (!$seat) {
			return false;
		}

		$shift = null;
		if (!empty($tenant->shift_id)) {
			$shift = $this->db->get_where('study_shift', array('shift_id' => $tenant->shift_id, 'status' => 1))->row();
		}
		$tenant_shifts = $this->get_tenant_shifts($tenant_id);
		if (empty($tenant_shifts) && $shift) {
			$tenant_shifts = array(
				array(
					'shift_id'      => $shift->shift_id,
					'shift_name'    => $shift->shift_name,
					'timing_label'  => $shift->timing_label,
					'start_time'    => $shift->start_time,
					'end_time'      => $shift->end_time,
					'daily_price'   => $shift->daily_price,
					'monthly_price' => $shift->monthly_price,
					'quarterly_price'   => $shift->quarterly_price,
					'half_yearly_price' => $shift->half_yearly_price,
					'yearly_price'  => $shift->yearly_price
				)
			);
		}

		$start_timestamp = $start_date ? strtotime($start_date) : 0;
		if (!$start_timestamp) {
			$start_timestamp = !empty($tenant->lease_start) ? $tenant->lease_start : time();
		}

		$due_timestamp = $due_date ? strtotime($due_date . ' 11:59:59 pm') : 0;
		if (!$due_timestamp) {
			$due_timestamp = strtotime(date('Y-m-d 23:59:59', $start_timestamp));
		}

		$end_timestamp = $this->calculate_plan_end($start_timestamp, $tenant->plan_type);
		$amount = $this->get_seat_plan_amount($seat, $tenant->plan_type);
		if ($amount <= 0) {
			$amount = $this->get_tenant_shifts_plan_amount($tenant_shifts, $tenant->plan_type);
		} else {
			$shifts_total = $this->get_tenant_shifts_plan_amount($tenant_shifts, $tenant->plan_type);
			if ($shifts_total > $amount) {
				$amount = $shifts_total;
			}
		}
		$seat_label = $this->build_seat_label($tenant->room_id);
		$shift_label = $this->build_tenant_shifts_label($tenant_shifts);
		$shift_label_short = $this->build_tenant_shifts_short_label($tenant_shifts);

		$invoice['tenant_name'] = $tenant->name;
		$invoice['status'] = $status;
		$invoice['start_date'] = $start_timestamp;
		$invoice['end_date'] = $end_timestamp;
		$invoice['due_date'] = $due_timestamp;
		$invoice['invoice_type'] = 1;
		$invoice['tenant_mobile'] = $tenant->mobile_number;
		$invoice['room_number'] = $seat_label;
		$invoice['shift_name'] = $shift_label;
		$invoice['plan_type'] = $tenant->plan_type;
		$invoice['seat_label'] = $seat_label;
		$invoice['tenant_id'] = $tenant_id;
		$invoice['late_fee'] = 0;
		$invoice['invoice_number'] = date('Ymd', $start_timestamp) . rand(100, 999) . $tenant_id;
		$invoice['created_on'] = time();
		$invoice['created_by'] = $this->session->userdata('user_id');
		$invoice['timestamp'] = time();
		$invoice['updated_by'] = $this->session->userdata('user_id');

		$this->db->insert('invoice', $invoice);
		$invoice_id = $this->db->insert_id();

		$data['month'] = date('F', $start_timestamp);
		$data['year'] = (int) date('Y', $start_timestamp);
		$data['amount'] = $amount;
		$data['invoice_id'] = $invoice_id;
		$data['tenant_id'] = $tenant_id;
		$data['plan_type'] = $tenant->plan_type;
		$data['shift_name'] = $shift_label_short;
		$data['created_on'] = time();
		$data['created_by'] = $this->session->userdata('user_id');
		$data['timestamp'] = time();
		$data['updated_by'] = $this->session->userdata('user_id');
		$data['status'] = $status;

		$this->db->insert('tenant_rent', $data);

		return true;
	}

	function update_library_plan_settings()
	{
		$this->ensure_library_plan_schema();
		$shift_ids = $this->input->post('shift_id');

		if (!is_array($shift_ids)) {
			$this->session->set_flashdata('warning', 'No shift plan data was submitted.');
			redirect(base_url() . 'library_plan_settings', 'refresh');
		}

		foreach ($shift_ids as $index => $shift_id) {
			$data['shift_name'] = $this->input->post('shift_name')[$index];
			$data['timing_label'] = $this->input->post('timing_label')[$index];
			$data['start_time'] = $this->input->post('start_time')[$index];
			$data['end_time'] = $this->input->post('end_time')[$index];
			$data['daily_price'] = $this->input->post('daily_price')[$index];
			$data['monthly_price'] = $this->input->post('monthly_price')[$index];
			$data['quarterly_price'] = $this->input->post('quarterly_price')[$index];
			$data['half_yearly_price'] = $this->input->post('half_yearly_price')[$index];
			$data['yearly_price'] = $this->input->post('yearly_price')[$index];
			$data['status'] = 1;
			$data['timestamp'] = time();
			$data['updated_by'] = $this->session->userdata('user_id');

			$this->db->where('shift_id', $shift_id);
			$this->db->update('study_shift', $data);
		}

		$this->session->set_flashdata('success', 'Study plans and shift timings updated successfully.');
		redirect(base_url() . 'library_plan_settings', 'refresh');
	}

	function add_room()
	{
		$this->ensure_library_plan_schema();
		$rooms 							= 	$this->db->get('room')->result_array();
		foreach ($rooms as $room) {
			if ($room['room_number'] == $this->input->post('room_number') && $room['floor'] == $this->input->post('floor')) {
				$this->session->set_flashdata('warning', $this->lang->line('room_already_exists'));

				redirect(base_url() . 'add_room', 'refresh');
			}
		}

		$data['roomnumber']			=	$this->input->post('roomnumber');
		$data['room_number']			=	$this->input->post('room_number');
		$data['daily_rent']				=	$this->input->post('daily_rent') ? $this->input->post('daily_rent') : 0;
		$data['monthly_rent']			=	$this->input->post('monthly_rent') ? $this->input->post('monthly_rent') : 0;
		$data['quarterly_price']		=	$this->input->post('quarterly_price') ? $this->input->post('quarterly_price') : 0;
		$data['half_yearly_price']		=	$this->input->post('half_yearly_price') ? $this->input->post('half_yearly_price') : 0;
		$data['yearly_price']			=	$this->input->post('yearly_price') ? $this->input->post('yearly_price') : 0;
		$data['status']					=	0;
		$data['floor']					=	$this->input->post('floor');
		$data['remarks']				=	$this->input->post('remarks');
		$data['created_on']				=	time();
		$data['created_by']				=	$this->session->userdata('user_id');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->insert('room', $data);

		$this->session->set_flashdata('success', $this->lang->line('room_added_successfully'));

		redirect(base_url() . 'rooms', 'refresh');
	}

	function update_room($room_id = '')
	{
		$this->ensure_library_plan_schema();
		$existing_room_number 			=	$this->db->get_where('room', array('room_id' => $room_id))->row()->room_number;
		$existing_floor_number			=	$this->db->get_where('room', array('room_id' => $room_id))->row()->floor;

		if ($existing_room_number != $this->input->post('room_number') || $existing_floor_number != $this->input->post('floor')) {
			$rooms 							= 	$this->db->get('room')->result_array();
			foreach ($rooms as $room) {
				if ($room['room_number'] == $this->input->post('room_number') && $room['floor'] == $this->input->post('floor')) {
					$this->session->set_flashdata('warning', $this->lang->line('room_already_exists'));

					redirect(base_url() . 'rooms', 'refresh');
				}
			}
		}

		$data['roomnumber']			=	$this->input->post('roomnumber');
		$data['room_number']			=	$this->input->post('room_number');
		$data['daily_rent']				=	$this->input->post('daily_rent') ? $this->input->post('daily_rent') : 0;
		$data['monthly_rent']			=	$this->input->post('monthly_rent') ? $this->input->post('monthly_rent') : 0;
		$data['quarterly_price']		=	$this->input->post('quarterly_price') ? $this->input->post('quarterly_price') : 0;
		$data['half_yearly_price']		=	$this->input->post('half_yearly_price') ? $this->input->post('half_yearly_price') : 0;
		$data['yearly_price']			=	$this->input->post('yearly_price') ? $this->input->post('yearly_price') : 0;
		$data['floor']					=	$this->input->post('floor');
		$data['remarks']				=	$this->input->post('remarks');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->where('room_id', $room_id);
		$this->db->update('room', $data);

		$this->session->set_flashdata('success', $this->lang->line('room_updated_successfully'));

		redirect(base_url() . 'rooms', 'refresh');
	}

	function remove_room($room_id = '')
	{
		$this->db->where('room_id', $room_id);
		$this->db->delete('room');

		$this->session->set_flashdata('success', $this->lang->line('room_deleted_successfully'));

		redirect(base_url() . 'rooms', 'refresh');
	}

	function assign_tenant($room_id = '')
	{
		$data['status']			=	1;
		$data['timestamp']		=	time();
		$data['updated_by']		=	$this->session->userdata('user_id');

		$this->db->where('room_id', $room_id);
		$this->db->update('room', $data);

		$data2['room_id']		=	$room_id;
		$data2['status']		=	1;
		$data2['timestamp']		=	time();
		$data2['updated_by']	=	$this->session->userdata('user_id');

		$this->db->where('tenant_id', $this->input->post('tenant_id'));
		$this->db->update('tenant', $data2);

		$array = array('user_type' => 3, 'person_id' => $this->input->post('tenant_id'));
		$this->db->where($array);
		$this->db->update('user', $data);

		$this->session->set_flashdata('success', $this->lang->line('room_assigned_successfully'));

		redirect(base_url() . 'rooms', 'refresh');
	}

	function vacant_room($room_id = '')
	{
		$data['status']			=	0;
		$data['timestamp']		=	time();
		$data['updated_by']		=	$this->session->userdata('user_id');

		$this->db->where('room_id', $room_id);
		$this->db->update('room', $data);

		$tenant_id 				=	$this->db->get_where('tenant', array('room_id' => $room_id))->row()->tenant_id;

		$data2['room_id']		=	0;
		$data2['status']		=	0;
		$data2['timestamp']		=	time();
		$data2['updated_by']	=	$this->session->userdata('user_id');

		$this->db->where('tenant_id', $tenant_id);
		$this->db->update('tenant', $data2);

		$this->session->set_flashdata('success', $this->lang->line('room_vacant_now'));

		redirect(base_url() . 'rooms', 'refresh');
	}

	function add_tenant()
	{
		$this->ensure_library_plan_schema();
		$ext 							= 	pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION);
		$ext_id_front 					= 	pathinfo($_FILES['id_front_image_link']['name'], PATHINFO_EXTENSION);
		$ext_id_back 					= 	pathinfo($_FILES['id_back_image_link']['name'], PATHINFO_EXTENSION);
		$tenant_upload_dir				= 	$this->ensure_upload_directory('uploads/tenants');

		$users = $this->db->get('user')->result_array();
		foreach ($users as $user) {
			if ($user['email'] == $this->input->post('email')) {
				$this->session->set_flashdata('warning', $this->lang->line('tenant_email_already_registered'));

				redirect(base_url() . 'add_tenant', 'refresh');
			}
		}

		if ($this->input->post('status') && !($this->input->post('room_id'))) {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_activate_assign_room'));

			redirect(base_url() . 'add_tenant', 'refresh');
		} elseif (!($this->input->post('status')) && $this->input->post('room_id')) {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_assign_room_must_activate'));

			redirect(base_url() . 'add_tenant', 'refresh');
		} else {
			if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
				$data['image_link'] 			= 	strtolower(explode(" ", $this->input->post('name'))[0]) . '_' . time() . '.' . $ext;

				$target_file = $tenant_upload_dir . $data['image_link'];
				$move_result = move_uploaded_file($_FILES['image_link']['tmp_name'], $target_file);

				if (!$move_result || !file_exists($target_file)) {
					$this->session->set_flashdata('warning', 'Tenant image upload failed. Please try again.');
					redirect(base_url() . 'add_tenant', 'refresh');
				}
			}

			if ($ext_id_front == 'jpeg' || $ext_id_front == 'jpg' || $ext_id_front == 'png' || $ext_id_front == 'JPEG' || $ext_id_front == 'JPG' || $ext_id_front == 'PNG') {
				$data['id_front_image_link'] 	= 	strtolower(explode(" ", $this->input->post('name'))[0]) . '_id_front_' . time() . '.' . $ext_id_front;

				$target_file = $tenant_upload_dir . $data['id_front_image_link'];
				$move_result = move_uploaded_file($_FILES['id_front_image_link']['tmp_name'], $target_file);

				if (!$move_result || !file_exists($target_file)) {
					$this->session->set_flashdata('warning', 'Tenant ID front image upload failed. Please try again.');
					redirect(base_url() . 'add_tenant', 'refresh');
				}
			}

			if ($ext_id_back == 'jpeg' || $ext_id_back == 'jpg' || $ext_id_back == 'png' || $ext_id_back == 'JPEG' || $ext_id_back == 'JPG' || $ext_id_back == 'PNG') {
				$data['id_back_image_link'] 	= 	strtolower(explode(" ", $this->input->post('name'))[0]) . '_id_back_' . time() . '.' . $ext_id_back;

				$target_file = $tenant_upload_dir . $data['id_back_image_link'];
				$move_result = move_uploaded_file($_FILES['id_back_image_link']['tmp_name'], $target_file);

				if (!$move_result || !file_exists($target_file)) {
					$this->session->set_flashdata('warning', 'Tenant ID back image upload failed. Please try again.');
					redirect(base_url() . 'add_tenant', 'refresh');
				}
			}

			$data['name']				=	$this->input->post('name');
			$data['mobile_number']		=	$this->input->post('mobile_number');
			$data['email']				=	$this->input->post('email');
			$data['lg_person']				=	$this->input->post('lg_person');
			$data['lg_contact']				=	$this->input->post('lg_contact');
			$data['blood_group']				=	$this->input->post('blood_group');
			$data['id_type_id']			=	$this->input->post('id_type_id');
			$data['id_number']			=	$this->input->post('id_number');
			$data['home_address']		=	$this->input->post('home_address_line_1') . '<br>' . $this->input->post('home_address_line_2');
			$data['emergency_person']	=	$this->input->post('emergency_person');
			$data['emergency_contact']	=	$this->input->post('emergency_contact');
			$data['room_id']			=	$this->input->post('room_id') ? $this->input->post('room_id') : 0;
			$shift_ids					=	$this->input->post('shift_ids');
			if (!is_array($shift_ids)) {
				$shift_ids = array();
			}
			$shift_ids					=	array_values(array_unique(array_filter(array_map('intval', $shift_ids))));
			$data['shift_id']			=	!empty($shift_ids) ? $shift_ids[0] : ($this->input->post('shift_id') ? $this->input->post('shift_id') : null);
			$data['plan_type']			=	$this->input->post('plan_type');
			$this->sync_student_plan_dates($data);

			$data['profession_id']		=	$this->input->post('profession_id');
			$data['work_address']		=	$this->input->post('work_address_line_1') . '<br>' . $this->input->post('work_address_line_2');
			$data['status']				=	$this->input->post('status');
			$data['extra_note']			=	$this->input->post('extra_note');
			$data['created_on']			=	time();
			$data['created_by']			=	$this->session->userdata('user_id');
			$data['timestamp']			=	time();
			$data['updated_by']			=	$this->session->userdata('user_id');

			$this->db->insert('tenant', $data);
			$new_tenant_id = $this->db->insert_id();
			$this->set_tenant_shifts($new_tenant_id, $shift_ids);

			if ($this->input->post('email')) {
				$data2['person_id']		=	$new_tenant_id;
				$data2['email']			=	$this->input->post('email');
				$data2['password']		=	$this->input->post('password') ? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : password_hash(123456, PASSWORD_DEFAULT);
				$data2['user_type']		=	3;
				$data2['status']		=	$this->input->post('status');
				$data2['created_on']	= 	time();
				$data2['created_by']	=	$this->session->userdata('user_id');
				$data2['timestamp']		=	time();
				$data2['updated_by']	=	$this->session->userdata('user_id');
				$data2['permissions']	=	'10,13,14';

				$this->db->insert('user', $data2);
			}

			if ($this->input->post('room_id')) {
				$data3['status']		=	1;
				$data3['timestamp']		=	time();
				$data3['updated_by']	=	$this->session->userdata('user_id');

				$this->db->where('room_id', $data['room_id']);
				$this->db->update('room', $data3);
			}
			$this->session->set_flashdata('success', $this->lang->line('tenant_added_successfully'));

			redirect(base_url() . 'tenants', 'refresh');
		}
	}

	function change_tenant_image($tenant_id = '')
	{
		$ext 							= 	pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION);

		if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
			$tenant_upload_dir			=	$this->ensure_upload_directory('uploads/tenants');
			$image_link 				= 	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->image_link;

			if (!empty($image_link) && file_exists($tenant_upload_dir . $image_link)) unlink($tenant_upload_dir . $image_link);

			$tenant_name 				=	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->name;

			$data['image_link'] 		= 	strtolower(explode(" ", $tenant_name)[0]) . '_' . time() . '.' . $ext;
			$data['timestamp']			=	time();
			$data['updated_by']			=	$this->session->userdata('user_id');

			$target_file = $tenant_upload_dir . $data['image_link'];
			$move_result = move_uploaded_file($_FILES['image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Tenant image upload failed. Please try again.');
				redirect(base_url() . 'tenants', 'refresh');
			}

			$this->db->where('tenant_id', $tenant_id);
			$this->db->update('tenant', $data);

			$this->session->set_flashdata('success', $this->lang->line('tenant_image_updated_successfully'));

			redirect(base_url() . 'tenants', 'refresh');
		} else {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));

			redirect(base_url() . 'tenants', 'refresh');
		}
	}

	function change_tenant_id_image($tenant_id = '')
	{
		$ext_id_front 					= 	pathinfo($_FILES['id_front_image_link']['name'], PATHINFO_EXTENSION);
		$ext_id_back 					= 	pathinfo($_FILES['id_back_image_link']['name'], PATHINFO_EXTENSION);
		$tenant_upload_dir				=	$this->ensure_upload_directory('uploads/tenants');

		if ($ext_id_front == 'jpeg' || $ext_id_front == 'jpg' || $ext_id_front == 'png' || $ext_id_front == 'JPEG' || $ext_id_front == 'JPG' || $ext_id_front == 'PNG') {
			$image_link 				= 	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->id_front_image_link;

			if (!empty($image_link) && file_exists($tenant_upload_dir . $image_link)) unlink($tenant_upload_dir . $image_link);

			$tenant_name 				=	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->name;

			$data['id_front_image_link'] = 	strtolower(explode(" ", $tenant_name)[0]) . '_id_front_' . time() . '.' . $ext_id_front;
			$data['timestamp']			=	time();
			$data['updated_by']			=	$this->session->userdata('user_id');

			$target_file = $tenant_upload_dir . $data['id_front_image_link'];
			$move_result = move_uploaded_file($_FILES['id_front_image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Tenant ID front image upload failed. Please try again.');
				redirect(base_url() . 'tenants', 'refresh');
			}

			$this->db->where('tenant_id', $tenant_id);
			$this->db->update('tenant', $data);

			$this->session->set_flashdata('success', $this->lang->line('tenant_image_front_success'));
		} else {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));
		}

		if ($ext_id_back == 'jpeg' || $ext_id_back == 'jpg' || $ext_id_back == 'png' || $ext_id_back == 'JPEG' || $ext_id_back == 'JPG' || $ext_id_back == 'PNG') {
			$image_link 				= 	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->id_back_image_link;

			if (!empty($image_link) && file_exists($tenant_upload_dir . $image_link)) unlink($tenant_upload_dir . $image_link);

			$tenant_name 				=	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->name;

			$data['id_back_image_link'] = 	strtolower(explode(" ", $tenant_name)[0]) . '_id_back_' . time() . '.' . $ext_id_back;
			$data['timestamp']			=	time();
			$data['updated_by']			=	$this->session->userdata('user_id');

			$target_file = $tenant_upload_dir . $data['id_back_image_link'];
			$move_result = move_uploaded_file($_FILES['id_back_image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Tenant ID back image upload failed. Please try again.');
				redirect(base_url() . 'tenants', 'refresh');
			}

			$this->db->where('tenant_id', $tenant_id);
			$this->db->update('tenant', $data);

			$this->session->set_flashdata('success', $this->lang->line('tenant_image_back_success'));
		} else {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));
		}

		redirect(base_url() . 'tenants', 'refresh');
	}

	function update_tenant($tenant_id = '')
	{
		$this->ensure_library_plan_schema();
		$existing_room_id 				=	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->room_id;

		if ($this->input->post('status') && !($this->input->post('room_id'))) {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_activate_assign_room'));

			redirect(base_url() . 'tenants', 'refresh');
		} elseif (!($this->input->post('status')) && $this->input->post('room_id')) {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_assign_room_must_activate'));

			redirect(base_url() . 'tenants', 'refresh');
		} elseif (!($this->input->post('status')) && !($this->input->post('room_id'))) {
			$data4['status']			=	0;
			$data4['timestamp']			=	time();
			$data4['updated_by']		=	$this->session->userdata('user_id');

			$this->db->where('room_id', $existing_room_id);
			$this->db->update('room', $data4);

			$data['room_id']			= 	0;
		} else {
			if ($existing_room_id != $this->input->post('room_id')) {
				if ($existing_room_id > 0) {
					$data2['status']		=	0;
					$data2['timestamp']		=	time();
					$data2['updated_by']	=	$this->session->userdata('user_id');

					$this->db->where('room_id', $existing_room_id);
					$this->db->update('room', $data2);
				}

				$data3['status']		=	1;
				$data3['timestamp']		=	time();
				$data3['updated_by']	=	$this->session->userdata('user_id');

				$this->db->where('room_id', $this->input->post('room_id'));
				$this->db->update('room', $data3);

				$data['room_id']		= 	$this->input->post('room_id');
			}
		}

		$data['name']					=	$this->input->post('name');
		$data['mobile_number']			=	$this->input->post('mobile_number');
		$data['email']					=	$this->input->post('email');
		$data['id_type_id']				=	$this->input->post('id_type_id');
		$data['id_number']				=	$this->input->post('id_number');
		$data['home_address']			=	$this->input->post('home_address_line_1') . '<br>' . $this->input->post('home_address_line_2');
		$data['emergency_person']		=	$this->input->post('emergency_person');
		$data['emergency_contact']		=	$this->input->post('emergency_contact');
		$shift_ids						=	$this->input->post('shift_ids');
		if (!is_array($shift_ids)) {
			$shift_ids = array();
		}
		$shift_ids						=	array_values(array_unique(array_filter(array_map('intval', $shift_ids))));
		$data['shift_id']				=	!empty($shift_ids) ? $shift_ids[0] : ($this->input->post('shift_id') ? $this->input->post('shift_id') : null);
		$data['plan_type']				=	$this->input->post('plan_type');
		$data['profession_id']			=	$this->input->post('profession_id');
		$data['work_address']			=	$this->input->post('work_address_line_1') . '<br>' . $this->input->post('work_address_line_2');
		$data['status']					=	$this->input->post('status');
		$data['extra_note']				=	$this->input->post('extra_note');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->sync_student_plan_dates($data);

		$this->db->where('tenant_id', $tenant_id);
		$this->db->update('tenant', $data);
		$this->set_tenant_shifts($tenant_id, $shift_ids);

		if ($this->input->post('email')) {
			if ($this->db->get_where('user', array('user_type' => 3, 'person_id' => $tenant_id))->num_rows() > 0) {
				$data2['email']					=	$this->input->post('email');
				$data2['password']				=	$this->input->post('password') ? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : password_hash(123456, PASSWORD_DEFAULT);
				$data2['status']				=	$this->input->post('status');
				$data2['timestamp']				=	time();
				$data2['updated_by']			=	$this->session->userdata('user_id');

				$array = array('user_type' => 3, 'person_id' => $tenant_id);
				$this->db->where($array);
				$this->db->update('user', $data2);
			} else {
				$data2['person_id']			=	$tenant_id;
				$data2['email']				=	$this->input->post('email');
				$data2['password']			=	$this->input->post('password') ? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : password_hash(123456, PASSWORD_DEFAULT);
				$data2['user_type']			=	3;
				$data2['status']			=	$this->input->post('status');
				$data2['created_on']		= 	time();
				$data2['created_by']		=	$this->session->userdata('user_id');
				$data2['timestamp']			=	time();
				$data2['updated_by']		=	$this->session->userdata('user_id');
				$data2['permissions']		=	'10,13,14';

				$this->db->insert('user', $data2);
			}
		}

		$this->session->set_flashdata('success', $this->lang->line('tenant_updated_successfully'));

		redirect(base_url() . 'tenants', 'refresh');
	}

	function deactivate_tenant($tenant_id = '')
	{
		$room_id 						=	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->room_id;

		$data['status']					=	0;
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		if ($room_id) {
			$this->db->where('room_id', $room_id);
			$this->db->update('room', $data);
		}

		$data2['room_id']				=	0;
		$data2['status']				=	0;
		$data2['timestamp']				=	time();
		$data2['updated_by']			=	$this->session->userdata('user_id');

		$this->db->where('tenant_id', $tenant_id);
		$this->db->update('tenant', $data2);

		if ($this->db->get_where('user', array('user_type' => 3, 'person_id' => $tenant_id))->num_rows() > 0) {
			$array = array('user_type' => 3, 'person_id' => $tenant_id);
			$this->db->where($array);
			$this->db->update('user', $data);
		}

		$this->session->set_flashdata('success', $this->lang->line('tenant_deactivated_successfully'));

		redirect(base_url() . 'tenants', 'refresh');
	}

	function remove_tenant($tenant_id = '')
	{
		$room_id 						=	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->room_id;

		$data['status']					=	0;
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		if ($room_id) {
			$this->db->where('room_id', $room_id);
			$this->db->update('room', $data);
		}

		$image_link 					= 	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->image_link;

		if (isset($image_link)) unlink('uploads/tenants/' . $image_link);

		$this->db->where('tenant_id', $tenant_id);
		$this->db->delete('tenant');

		if ($this->db->get_where('user', array('user_type' => 3, 'person_id' => $tenant_id))->num_rows() > 0) {
			$array = array('user_type' => 3, 'person_id' => $tenant_id);
			$this->db->where($array);
			$this->db->delete('user');
		}

		$this->session->set_flashdata('success', $this->lang->line('tenant_deleted_successfully'));

		redirect(base_url() . 'tenants', 'refresh');
	}

	function add_utility_bill()
	{
		$ext 								= 	pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION);

		if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
			$data['image_link'] 			= 	'utility_' . $this->input->post('year') . '_' . $this->input->post('month') . '_' . time() . '.' . $ext;
			$upload_dir = $this->ensure_upload_directory('uploads/bills');
			$target_file = $upload_dir . $data['image_link'];
			$move_result = move_uploaded_file($_FILES['image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Utility bill image upload failed. Please try again.');
				redirect(base_url() . 'utility_bills', 'refresh');
			}
		}

		$data['utility_bill_category_id']	=	$this->input->post('utility_bill_category_id');
		$data['year']						=	$this->input->post('year');
		$data['month']						=	$this->input->post('month');
		$data['amount']						=	$this->input->post('amount');
		$data['status']						=	$this->input->post('status');
		$data['created_on']					=	time();
		$data['created_by']					=	$this->session->userdata('user_id');
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->insert('utility_bill', $data);

		$this->session->set_flashdata('success', $this->lang->line('utility_bill_added_successfully'));

		redirect(base_url() . 'utility_bills', 'refresh');
	}

	function update_utility_bill($utility_bill_id = '')
	{
		$data['utility_bill_category_id']	=	$this->input->post('utility_bill_category_id');
		$data['year']						=	$this->input->post('year');
		$data['month']						=	$this->input->post('month');
		$data['amount']						=	$this->input->post('amount');
		$data['status']						=	$this->input->post('status');
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->where('utility_bill_id', $utility_bill_id);
		$this->db->update('utility_bill', $data);

		$this->session->set_flashdata('success', $this->lang->line('utility_bill_updated_successfully'));

		redirect(base_url() . 'utility_bills', 'refresh');
	}

	function change_utility_image($utility_bill_id = '')
	{
		$ext 							= 	pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION);

		if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
			$upload_dir					=	$this->ensure_upload_directory('uploads/bills');
			$image_link 				= 	$this->db->get_where('utility_bill', array('utility_bill_id' => $utility_bill_id))->row()->image_link;

			if (!empty($image_link) && file_exists($upload_dir . $image_link)) unlink($upload_dir . $image_link);

			$year 						=	$this->db->get_where('utility_bill', array('utility_bill_id' => $utility_bill_id))->row()->year;
			$month 						=	$this->db->get_where('utility_bill', array('utility_bill_id' => $utility_bill_id))->row()->month;

			$data['image_link'] 		= 	'utility_' . $year . '_' . $month . '_' . time() . '.' . $ext;
			$data['timestamp']			=	time();
			$data['updated_by']			=	$this->session->userdata('user_id');

			$target_file = $upload_dir . $data['image_link'];
			$move_result = move_uploaded_file($_FILES['image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Utility bill image upload failed. Please try again.');
				redirect(base_url() . 'utility_bills', 'refresh');
			}

			$this->db->where('utility_bill_id', $utility_bill_id);
			$this->db->update('utility_bill', $data);

			$this->session->set_flashdata('success', $this->lang->line('utility_bill_image_updated_successfully'));

			redirect(base_url() . 'utility_bills', 'refresh');
		} else {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));

			redirect(base_url() . 'utility_bills', 'refresh');
		}
	}

	function remove_utility_bill($utility_bill_id = '')
	{
		$this->db->where('utility_bill_id', $utility_bill_id);
		$this->db->delete('utility_bill');

		$this->session->set_flashdata('success', $this->lang->line('utility_bill_deleted_successfully'));

		redirect(base_url() . 'utility_bills', 'refresh');
	}

	function add_bank_statement()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS bank_statement (
			bank_statement_id INT(11) AUTO_INCREMENT PRIMARY KEY,
			month VARCHAR(20),
			year VARCHAR(10),
			file_name VARCHAR(255),
			created_on INT(11),
			created_by INT(11),
			timestamp INT(11),
			updated_by INT(11)
		)");

		if (empty($_FILES['file_name']['name'])) {
			$this->session->set_flashdata('warning', 'Please select a bank statement file to upload.');
			redirect(base_url() . 'bank_statements', 'refresh');
		}

		$ext = strtolower(pathinfo($_FILES['file_name']['name'], PATHINFO_EXTENSION));
		if (!in_array($ext, array('pdf', 'jpeg', 'jpg', 'png'))) {
			$this->session->set_flashdata('warning', 'Only PDF, JPG, JPEG, and PNG files are allowed.');
			redirect(base_url() . 'bank_statements', 'refresh');
		}

		$upload_dir = FCPATH . 'uploads/bank_statements/';
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0777, true);
		}

		$data['file_name'] = 'bank_statement_' . $this->input->post('year') . '_' . $this->input->post('month') . '_' . time() . '.' . $ext;
		$target_file = $upload_dir . $data['file_name'];
		$move_result = move_uploaded_file($_FILES['file_name']['tmp_name'], $target_file);

		if (!$move_result || !file_exists($target_file)) {
			$this->session->set_flashdata('warning', 'Bank statement upload failed. Please check folder permissions and try again.');
			redirect(base_url() . 'bank_statements', 'refresh');
		}

		$data['month'] = $this->input->post('month');
		$data['year'] = $this->input->post('year');
		$data['created_on'] = time();
		$data['created_by'] = $this->session->userdata('user_id');
		$data['timestamp'] = time();
		$data['updated_by'] = $this->session->userdata('user_id');

		$this->db->insert('bank_statement', $data);

		$this->session->set_flashdata('success', 'Bank statement added successfully');

		redirect(base_url() . 'bank_statements', 'refresh');
	}

	function remove_bank_statement($bank_statement_id = '')
	{
		$file_name = $this->db->get_where('bank_statement', array('bank_statement_id' => $bank_statement_id))->row()->file_name;
		if ($file_name && file_exists(FCPATH . 'uploads/bank_statements/' . $file_name)) {
			unlink(FCPATH . 'uploads/bank_statements/' . $file_name);
		}

		$this->db->where('bank_statement_id', $bank_statement_id);
		$this->db->delete('bank_statement');

		$this->session->set_flashdata('success', 'Bank statement deleted successfully');

		redirect(base_url() . 'bank_statements', 'refresh');
	}

	function ensure_purchase_table()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS purchase (
			purchase_id INT(11) AUTO_INCREMENT PRIMARY KEY,
			purchase_date DATE,
			purchase_head VARCHAR(100),
			vendor_name VARCHAR(100),
			amount DECIMAL(12,2) DEFAULT 0.00,
			description VARCHAR(255),
			bill_file VARCHAR(255),
			created_on INT(11),
			created_by INT(11),
			timestamp INT(11),
			updated_by INT(11)
		)");
	}

	function add_purchase()
	{
		$this->ensure_purchase_table();

		if (!$this->input->post('purchase_date') || !$this->input->post('purchase_head') || !$this->input->post('amount')) {
			$this->session->set_flashdata('warning', 'Please fill all required purchase details.');
			redirect(base_url() . 'purchases', 'refresh');
		}

		if (!empty($_FILES['bill_file']['name'])) {
			$ext = strtolower(pathinfo($_FILES['bill_file']['name'], PATHINFO_EXTENSION));
			if (!in_array($ext, array('pdf', 'jpeg', 'jpg', 'png'))) {
				$this->session->set_flashdata('warning', 'Only PDF, JPG, JPEG, and PNG files are allowed for purchase bills.');
				redirect(base_url() . 'purchases', 'refresh');
			}

			$upload_dir = FCPATH . 'uploads/purchases/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}
			@chmod($upload_dir, 0777);

			$data['bill_file'] = 'purchase_bill_' . time() . '.' . $ext;
			$target_file = $upload_dir . $data['bill_file'];
			$move_result = move_uploaded_file($_FILES['bill_file']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Purchase bill upload failed. Please try again.');
				redirect(base_url() . 'purchases', 'refresh');
			}
		}

		$data['purchase_date'] = $this->input->post('purchase_date');
		$data['purchase_head'] = $this->input->post('purchase_head');
		$data['vendor_name'] = $this->input->post('vendor_name');
		$data['amount'] = $this->input->post('amount');
		$data['description'] = $this->input->post('description');
		$data['created_on'] = time();
		$data['created_by'] = $this->session->userdata('user_id');
		$data['timestamp'] = time();
		$data['updated_by'] = $this->session->userdata('user_id');

		$this->db->insert('purchase', $data);

		$this->session->set_flashdata('success', 'Purchase added successfully');
		redirect(base_url() . 'purchases', 'refresh');
	}

	function update_purchase($purchase_id = '')
	{
		$this->ensure_purchase_table();

		if (!empty($_FILES['bill_file']['name'])) {
			$ext = strtolower(pathinfo($_FILES['bill_file']['name'], PATHINFO_EXTENSION));
			if (!in_array($ext, array('pdf', 'jpeg', 'jpg', 'png'))) {
				$this->session->set_flashdata('warning', 'Only PDF, JPG, JPEG, and PNG files are allowed for purchase bills.');
				redirect(base_url() . 'purchases', 'refresh');
			}

			$upload_dir = FCPATH . 'uploads/purchases/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}
			@chmod($upload_dir, 0777);

			$current_purchase = $this->db->get_where('purchase', array('purchase_id' => $purchase_id))->row();
			if ($current_purchase && $current_purchase->bill_file && file_exists($upload_dir . $current_purchase->bill_file)) {
				unlink($upload_dir . $current_purchase->bill_file);
			}

			$data['bill_file'] = 'purchase_bill_' . time() . '.' . $ext;
			$target_file = $upload_dir . $data['bill_file'];
			$move_result = move_uploaded_file($_FILES['bill_file']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Purchase bill upload failed. Please try again.');
				redirect(base_url() . 'purchases', 'refresh');
			}
		}

		$data['purchase_date'] = $this->input->post('purchase_date');
		$data['purchase_head'] = $this->input->post('purchase_head');
		$data['vendor_name'] = $this->input->post('vendor_name');
		$data['amount'] = $this->input->post('amount');
		$data['description'] = $this->input->post('description');
		$data['timestamp'] = time();
		$data['updated_by'] = $this->session->userdata('user_id');

		$this->db->where('purchase_id', $purchase_id);
		$this->db->update('purchase', $data);

		$this->session->set_flashdata('success', 'Purchase updated successfully');
		redirect(base_url() . 'purchases', 'refresh');
	}

	function remove_purchase($purchase_id = '')
	{
		$this->ensure_purchase_table();

		$purchase = $this->db->get_where('purchase', array('purchase_id' => $purchase_id))->row();
		if ($purchase && $purchase->bill_file && file_exists(FCPATH . 'uploads/purchases/' . $purchase->bill_file)) {
			unlink(FCPATH . 'uploads/purchases/' . $purchase->bill_file);
		}

		$this->db->where('purchase_id', $purchase_id);
		$this->db->delete('purchase');

		$this->session->set_flashdata('success', 'Purchase removed successfully');
		redirect(base_url() . 'purchases', 'refresh');
	}

	// Function related to adding utility bill category
	function add_utility_bill_category()
	{
		$data['name']					=	$this->input->post('name');
		$data['created_on']				=	time();
		$data['created_by']				=	$this->session->userdata('user_id');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->insert('utility_bill_category', $data);

		$this->session->set_flashdata('success', $this->lang->line('utility_bill_cat_added_successfully'));

		redirect(base_url() . 'utility_bill_categories', 'refresh');
	}

	// Function related to updating utility bill category
	function update_utility_bill_category($utility_bill_category_id = '')
	{
		$data['name']					=	$this->input->post('name');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->where('utility_bill_category_id', $utility_bill_category_id);
		$this->db->update('utility_bill_category', $data);

		$this->session->set_flashdata('success', $this->lang->line('utility_bill_cat_updated_successfully'));

		redirect(base_url() . 'utility_bill_categories', 'refresh');
	}

	// Function related to removing utility bill category
	function remove_utility_bill_category($utility_bill_category_id = '')
	{
		$this->db->where('utility_bill_category_id', $utility_bill_category_id);
		$this->db->delete('utility_bill_category');

		$this->session->set_flashdata('success', $this->lang->line('utility_bill_cat_deleted_successfully'));

		redirect(base_url() . 'utility_bill_categories', 'refresh');
	}


	function ensure_daily_cash_book_table()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS daily_cash_book (
			cash_book_id INT(11) NOT NULL AUTO_INCREMENT,
			entry_date DATE NOT NULL,
			entry_type VARCHAR(20) DEFAULT NULL,
			title VARCHAR(100) DEFAULT NULL,
			description VARCHAR(255) DEFAULT NULL,
			amount DECIMAL(12,2) DEFAULT 0.00,
			source_type VARCHAR(30) DEFAULT 'manual',
			source_id INT(11) DEFAULT NULL,
			created_on INT(11) DEFAULT NULL,
			created_by INT(11) DEFAULT NULL,
			timestamp INT(11) DEFAULT NULL,
			updated_by INT(11) DEFAULT NULL,
			PRIMARY KEY (cash_book_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
	}

	function add_daily_cash_book_entry()
	{
		$this->ensure_daily_cash_book_table();

		$entry_date = $this->input->post('entry_date');
		$entry_type = strtolower(trim($this->input->post('entry_type')));
		$title = trim($this->input->post('title'));
		$description = trim($this->input->post('description'));
		$amount = (float) $this->input->post('amount');

		if (!$entry_date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $entry_date) || !in_array($entry_type, array('income', 'expense')) || $title == '' || $amount <= 0) {
			$this->session->set_flashdata('warning', 'Please fill all required Daily Cash Book fields correctly.');
			redirect(base_url() . 'daily_cash_book?date=' . $entry_date . '&year=' . date('Y', strtotime($entry_date ? $entry_date : date('Y-m-d'))), 'refresh');
		}

		$data['entry_date'] = $entry_date;
		$data['entry_type'] = $entry_type;
		$data['title'] = $title;
		$data['description'] = $description;
		$data['amount'] = $amount;
		$data['source_type'] = 'manual';
		$data['created_on'] = time();
		$data['created_by'] = $this->session->userdata('user_id');
		$data['timestamp'] = time();
		$data['updated_by'] = $this->session->userdata('user_id');

		$this->db->insert('daily_cash_book', $data);

		$this->session->set_flashdata('success', 'Daily cash book entry added successfully');
		redirect(base_url() . 'daily_cash_book?date=' . $entry_date . '&year=' . date('Y', strtotime($entry_date)), 'refresh');
	}

	function remove_daily_cash_book_entry($cash_book_id = '')
	{
		$this->ensure_daily_cash_book_table();

		$this->db->where('cash_book_id', $cash_book_id);
		$this->db->where('source_type', 'manual');
		$this->db->delete('daily_cash_book');

		$this->session->set_flashdata('success', 'Daily cash book entry removed successfully');

		$selected_date = $this->input->get('date') ? $this->input->get('date') : date('Y-m-d');
		$selected_year = $this->input->get('year') ? $this->input->get('year') : date('Y', strtotime($selected_date));
		redirect(base_url() . 'daily_cash_book?date=' . $selected_date . '&year=' . $selected_year, 'refresh');
	}

	function get_daily_cash_book_totals($selected_date)
	{
		$this->ensure_daily_cash_book_table();

		$this->db->select_sum('amount');
		$this->db->from('daily_cash_book');
		$this->db->where('entry_date', $selected_date);
		$this->db->where('entry_type', 'income');
		$manual_income = (float) $this->db->get()->row()->amount;

		$this->db->select_sum('amount');
		$this->db->from('daily_cash_book');
		$this->db->where('entry_date', $selected_date);
		$this->db->where('entry_type', 'expense');
		$manual_expense = (float) $this->db->get()->row()->amount;

		$start_time = strtotime($selected_date . ' 00:00:00');
		$end_time = strtotime($selected_date . ' 23:59:59');

		$this->db->select_sum('amount');
		$this->db->from('expense');
		$this->db->where('created_on >=', $start_time);
		$this->db->where('created_on <=', $end_time);
		$linked_expense = (float) $this->db->get()->row()->amount;

		return array(
			'manual_income' => $manual_income,
			'manual_expense' => $manual_expense,
			'linked_expense' => $linked_expense,
			'total_expense' => $manual_expense + $linked_expense,
			'balance' => $manual_income - ($manual_expense + $linked_expense)
		);
	}

	function get_daily_cash_book_entries($selected_date)
	{
		$this->ensure_daily_cash_book_table();

		$entries = array();

		$this->db->order_by('created_on', 'desc');
		$manual_entries = $this->db->get_where('daily_cash_book', array('entry_date' => $selected_date))->result_array();

		foreach ($manual_entries as $row) {
			$row['source_label'] = 'Manual Entry';
			$row['source_key'] = 'manual';
			$row['can_remove'] = true;
			$entries[] = $row;
		}

		$start_time = strtotime($selected_date . ' 00:00:00');
		$end_time = strtotime($selected_date . ' 23:59:59');
		$this->db->order_by('created_on', 'desc');
		$this->db->where('created_on >=', $start_time);
		$this->db->where('created_on <=', $end_time);
		$expense_entries = $this->db->get('expense')->result_array();

		foreach ($expense_entries as $expense) {
			$entries[] = array(
				'cash_book_id' => 'expense_' . $expense['expense_id'],
				'entry_date' => $selected_date,
				'entry_type' => 'expense',
				'title' => $expense['name'],
				'description' => $expense['description'],
				'amount' => $expense['amount'],
				'source_type' => 'expense_module',
				'source_id' => $expense['expense_id'],
				'created_on' => $expense['created_on'],
				'created_by' => $expense['created_by'],
				'timestamp' => $expense['timestamp'],
				'updated_by' => $expense['updated_by'],
				'source_label' => 'Expense Module',
				'source_key' => 'expense_module',
				'can_remove' => false
			);
		}

		usort($entries, function ($a, $b) {
			return ($b['created_on'] ?? 0) <=> ($a['created_on'] ?? 0);
		});

		return $entries;
	}

	function get_daily_cash_book_monthly_summary($selected_year)
	{
		$this->ensure_daily_cash_book_table();

		$months = array(
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December'
		);

		$summary = array();

		foreach ($months as $month_number => $month_name) {
			$start_date = date('Y-m-d', strtotime($selected_year . '-' . str_pad($month_number, 2, '0', STR_PAD_LEFT) . '-01'));
			$end_date = date('Y-m-t', strtotime($start_date));

			$this->db->select_sum('amount');
			$this->db->from('daily_cash_book');
			$this->db->where('entry_date >=', $start_date);
			$this->db->where('entry_date <=', $end_date);
			$this->db->where('entry_type', 'income');
			$manual_income = (float) $this->db->get()->row()->amount;

			$this->db->select_sum('amount');
			$this->db->from('daily_cash_book');
			$this->db->where('entry_date >=', $start_date);
			$this->db->where('entry_date <=', $end_date);
			$this->db->where('entry_type', 'expense');
			$manual_expense = (float) $this->db->get()->row()->amount;

			$this->db->select_sum('amount');
			$this->db->from('expense');
			$this->db->where('year', $selected_year);
			$this->db->where('month', $month_name);
			$linked_expense = (float) $this->db->get()->row()->amount;

			$summary[] = array(
				'month_number' => $month_number,
				'month_name' => $month_name,
				'manual_income' => $manual_income,
				'manual_expense' => $manual_expense,
				'linked_expense' => $linked_expense,
				'total_expense' => $manual_expense + $linked_expense,
				'balance' => $manual_income - ($manual_expense + $linked_expense)
			);
		}

		return $summary;
	}
	function add_expense()
	{
	    $ext = pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION);

		if (!empty($_FILES['image_link']['name'])) {
			$ext = strtolower($ext);
			if (!in_array($ext, array('jpeg', 'jpg', 'png'))) {
				$this->session->set_flashdata('warning', 'Only JPG, JPEG, and PNG files are allowed for expense images.');
				redirect(base_url() . 'add_expense', 'refresh');
			}

			$upload_dir = FCPATH . 'uploads/expense/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}

			$data['image_link'] = 'expense_' . $this->input->post('year') . '_' . $this->input->post('month') . '_' . time() . '.' . $ext;
			$target_file = $upload_dir . $data['image_link'];
			$move_result = move_uploaded_file($_FILES['image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Expense image upload failed. Please try again.');
				redirect(base_url() . 'add_expense', 'refresh');
			}
		}

		$data['name']						=	$this->input->post('name');
		$data['amount']						=	$this->input->post('amount');
		$data['description']				=	$this->input->post('description');
		$data['year']						=	$this->input->post('year');
		$data['month']						=	$this->input->post('month');
		$data['created_on']					=	time();
		$data['created_by']					=	$this->session->userdata('user_id');
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->insert('expense', $data);

		$this->session->set_flashdata('success', $this->lang->line('expense_added_successfully'));

		redirect(base_url() . 'expenses', 'refresh');
	}

	function update_expense($expense_id = '')
	{
		if (!empty($_FILES['image_link']['name'])) {
			$ext = strtolower(pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION));
			if (!in_array($ext, array('jpeg', 'jpg', 'png'))) {
				$this->session->set_flashdata('warning', 'Only JPG, JPEG, and PNG files are allowed for expense images.');
				redirect(base_url() . 'expenses', 'refresh');
			}

			$upload_dir = FCPATH . 'uploads/expense/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}

			$current_expense = $this->db->get_where('expense', array('expense_id' => $expense_id))->row();
			if ($current_expense && $current_expense->image_link && file_exists($upload_dir . $current_expense->image_link)) {
				unlink($upload_dir . $current_expense->image_link);
			}

			$data['image_link'] = 'expense_' . $this->input->post('year') . '_' . $this->input->post('month') . '_' . time() . '.' . $ext;
			$target_file = $upload_dir . $data['image_link'];
			$move_result = move_uploaded_file($_FILES['image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Expense image upload failed. Please try again.');
				redirect(base_url() . 'expenses', 'refresh');
			}
		}

		$data['name']						=	$this->input->post('name');
		$data['amount']						=	$this->input->post('amount');
		$data['description']				=	$this->input->post('description');
		$data['year']						=	$this->input->post('year');
		$data['month']						=	$this->input->post('month');
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->where('expense_id', $expense_id);
		$this->db->update('expense', $data);

		$this->session->set_flashdata('success', $this->lang->line('expense_updated_successfully'));

		redirect(base_url() . 'expenses', 'refresh');
	}

	function remove_expense($expense_id = '')
	{
		$this->db->where('expense_id', $expense_id);
		$this->db->delete('expense');

		$this->session->set_flashdata('success', $this->lang->line('expense_deleted_successfully'));

		redirect(base_url() . 'expenses', 'refresh');
	}

	function add_staff()
	{
		$users = $this->db->get('user')->result_array();
		foreach ($users as $user) {
			if ($user['email'] == $this->input->post('email')) {
				$this->session->set_flashdata('warning', $this->lang->line('tenant_email_already_registered'));

				redirect(base_url() . 'add_staff', 'refresh');
			}
		}

		$data['name']					=	$this->input->post('name');
		$data['role']					=	$this->input->post('role');
		$data['mobile_number']			=	$this->input->post('mobile_number');
		$data['status']					=	$this->input->post('status');
		$data['remarks']				=	$this->input->post('remarks');
		$data['created_on']				= 	time();
		$data['created_by']				=	$this->session->userdata('user_id');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->insert('staff', $data);

		if ($this->input->post('email')) {
			$data2['person_id']				=	$this->db->insert_id();
			$data2['email']					=	$this->input->post('email');
			$data2['password']				=	$this->input->post('password') ? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : password_hash(123456, PASSWORD_DEFAULT);
			$data2['user_type']				=	2;
			$data2['status']				=	$this->input->post('status');
			$data2['created_on']			= 	time();
			$data2['created_by']			=	$this->session->userdata('user_id');
			$data2['timestamp']				=	time();
			$data2['updated_by']			=	$this->session->userdata('user_id');

			$this->db->insert('user', $data2);

			$permission 					= 	$this->input->post('permission');

			if (isset($permission)) {
				$this->update_staff_permission($data2['person_id'], $permission);
			}
		}

		$this->session->set_flashdata('success', $this->lang->line('staff_added_successfully'));

		redirect(base_url() . 'staff', 'refresh');
	}

	function update_staff($staff_id = '')
	{
		$data['name']					=	$this->input->post('name');
		$data['role']					=	$this->input->post('role');
		$data['mobile_number']			=	$this->input->post('mobile_number');
		$data['status']					=	$this->input->post('status');
		$data['remarks']				=	$this->input->post('remarks');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->where('staff_id', $staff_id);
		$this->db->update('staff', $data);

		if ($this->input->post('email')) {
			if ($this->db->get_where('user', array('user_type' => 2, 'person_id' => $staff_id))->num_rows() > 0) {
				$data2['email']					=	$this->input->post('email');
				$data2['status']				=	$this->input->post('status');
				$data2['timestamp']				=	time();
				$data2['updated_by']			=	$this->session->userdata('user_id');

				$array = array('user_type' => 2, 'person_id' => $staff_id);
				$this->db->where($array);
				$this->db->update('user', $data2);
			} else {
				$data2['person_id']				=	$staff_id;
				$data2['email']					=	$this->input->post('email');
				$data2['password']				=	$this->input->post('password') ? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : password_hash(123456, PASSWORD_DEFAULT);
				$data2['user_type']				=	2;
				$data2['status']				=	$this->input->post('status');
				$data2['created_on']			= 	time();
				$data2['created_by']			=	$this->session->userdata('user_id');
				$data2['timestamp']				=	time();
				$data2['updated_by']			=	$this->session->userdata('user_id');

				$this->db->insert('user', $data2);
			}

			$permission 						= 	$this->input->post('permission');

			if (isset($permission)) {
				$this->update_staff_permission($staff_id, $permission);
			}
		}

		$this->session->set_flashdata('success', $this->lang->line('staff_updated_successfully'));

		redirect(base_url() . 'staff', 'refresh');
	}

	function deactivate_staff($staff_id = '')
	{
		$data['status']					=	0;
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->where('staff_id', $staff_id);
		$this->db->update('staff', $data);

		if ($this->db->get_where('user', array('user_type' => 2, 'person_id' => $staff_id))->num_rows() > 0) {
			$array = array('user_type' => 2, 'person_id' => $staff_id);
			$this->db->where($array);
			$this->db->update('user', $data);
		}

		$this->session->set_flashdata('success', $this->lang->line('staff_deactivated_successfully'));

		redirect(base_url() . 'staff', 'refresh');
	}

	function remove_staff($staff_id = '')
	{
		$this->db->where('staff_id', $staff_id);
		$this->db->delete('staff');

		if ($this->db->get_where('user', array('user_type' => 2, 'person_id' => $staff_id))->num_rows() > 0) {
			$array = array('user_type' => 2, 'person_id' => $staff_id);
			$this->db->where($array);
			$this->db->delete('user');
		}

		$this->session->set_flashdata('success', $this->lang->line('staff_deleted_successfully'));

		redirect(base_url() . 'staff', 'refresh');
	}

	function update_staff_permission($staff_id = '', $permission = [])
	{
		$permissions 					=	'';

		foreach ($permission as $key => $value) {
			$permissions			.=	$value . ',';
		}

		$data['permissions']			=	substr(trim($permissions), 0, -1);
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$array = array('user_type' => 2, 'person_id' => $staff_id);
		$this->db->where($array);
		$this->db->update('user', $data);

		$this->session->set_flashdata('success', $this->lang->line('staff_permission_updated_successfully'));

		redirect(base_url() . 'staff', 'refresh');
	}

	function add_staff_salary()
	{
		$data['staff_id']				=	$this->input->post('staff_id');
		$data['year']					=	$this->input->post('year');
		$data['month']					=	$this->input->post('month');
		$data['amount']					=	$this->input->post('amount');
		$data['status']					=	$this->input->post('status');
		$data['created_on']				= 	time();
		$data['created_by']				=	$this->session->userdata('user_id');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->insert('staff_salary', $data);

		$this->session->set_flashdata('success', $this->lang->line('staff_salary_added_successfully'));

		redirect(base_url('single_month_staff_payroll' . '/' . $data['year'] . '/' . $data['month']), 'refresh');
	}

	function update_staff_salary($staff_salary_id = '')
	{
		$data['staff_id']				=	$this->input->post('staff_id');
		$data['year']					=	$this->input->post('year');
		$data['month']					=	$this->input->post('month');
		$data['amount']					=	$this->input->post('amount');
		$data['status']					=	$this->input->post('status');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->where('staff_salary_id', $staff_salary_id);
		$this->db->update('staff_salary', $data);

		$this->session->set_flashdata('success', $this->lang->line('staff_salary_updated_successfully'));

		redirect(base_url() . 'staff_payroll', 'refresh');
	}

	function remove_staff_salary($staff_salary_id = '')
	{
		$this->db->where('staff_salary_id', $staff_salary_id);
		$this->db->delete('staff_salary');

		$this->session->set_flashdata('success', $this->lang->line('staff_salary_deleted_successfully'));

		redirect(base_url() . 'staff_payroll', 'refresh');
	}

	function generate_date_range_rents()
	{
		$generated = $this->create_library_plan_invoice(
			$this->input->post('tenant_id'),
			$this->input->post('status'),
			$this->input->post('start'),
			$this->input->post('due_date')
		);

		if ($generated) {
			$this->session->set_flashdata('success', 'Study plan invoice generated successfully.');
		} else {
			$this->session->set_flashdata('warning', 'Please assign a seat, shift, and plan before generating the invoice.');
		}

		redirect(base_url() . 'invoices', 'refresh');
	}

	function generate_multiple_months_rent()
	{
		$generated = $this->create_library_plan_invoice(
			$this->input->post('tenant_id'),
			$this->input->post('status'),
			$this->input->post('start'),
			$this->input->post('due_date')
		);

		if ($generated) {
			$this->session->set_flashdata('success', 'Student plan invoice generated successfully.');
		} else {
			$this->session->set_flashdata('warning', 'Please assign a seat, shift, and plan before generating the invoice.');
		}

		redirect(base_url() . 'invoices', 'refresh');
	}

	function generate_single_months_rent()
	{
		$tenants 						=	[];
		$year 							= 	$this->input->post('year');
		$month 							= 	$this->input->post('month');

		if ($this->input->post('tenants')[0] == 'All') {
			$active_tenants = $this->db->get_where('tenant', array('status' => 1))->result_array();
			foreach ($active_tenants as $active_tenant) {
				array_push($tenants, $active_tenant['tenant_id']);
			}
		} else {
			$tenants = $this->input->post('tenants');
		}

		$generated_count = 0;
		$start_date = $this->input->post('start');

		if (!$start_date && $month && $year) {
			$start_date = date('m/d/Y', strtotime($year . '-' . date('m', strtotime($month)) . '-01'));
		}

		for ($i = 0; $i < sizeof($tenants); $i++) {
			if ($this->create_library_plan_invoice($tenants[$i], $this->input->post('status'), $start_date, $this->input->post('due_date'))) {
				$generated_count++;
			}
		}

		if ($generated_count > 0) {
			$this->session->set_flashdata('success', 'Study plan invoices generated successfully.');
		} else {
			$this->session->set_flashdata('warning', 'No invoices were generated. Please check seat assignment, shift, and plan selection for the chosen students.');
		}

		redirect(base_url() . 'invoices', 'refresh');
	}

	function send_invoice_sms($invoice_id = '')
	{
		$tenant_id 		= 	$this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->tenant_id;
		$tenant_mobile	=	$this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->mobile_number;
		$late_fee 		= 	$this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->late_fee;
		$this->db->select_sum('amount');
		$this->db->from('tenant_rent');
		$this->db->where('invoice_id', $invoice_id);
		$query = $this->db->get();
		$grand_total = $late_fee > 0 ? $query->row()->amount + $late_fee : $query->row()->amount;

        $message = $this->lang->line('sms_invoice_1') 
		. '#' 
		. $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->invoice_number 
		. $this->lang->line('sms_invoice_2') 
		. date('d M, Y', $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row()->due_date) 
		. '. '
		. $this->lang->line('sms_invoice_3') . $this->db->get_where('setting', array('name' => 'currency'))->row()->content . ' ' . number_format($grand_total) 
		. ' - '
		. $this->lang->line('from')
		. $this->db->get_where('setting', array('setting_id' => 1))->row()->content;

        if ($this->db->get_where('setting', array('name' => 'number'))->row()->content) {
			if ($tenant_mobile) {
				$from = $this->db->get_where('setting', array('name' => 'number'))->row()->content;
				$to = $tenant_mobile;    
				
				$config['account_sid']	=	$this->db->get_where('setting', array('name' => 'account_sid'))->row()->content;
				$config['auth_token']	=	$this->db->get_where('setting', array('name' => 'auth_token'))->row()->content;
				$config['number']		=	$this->db->get_where('setting', array('name' => 'number'))->row()->content;

				$this->twilio->initialize($config);

				$response = $this->twilio->sms($from, $to, $message);

				if($response->IsError) {
					echo 'Error: ' . $response->ErrorMessage;

					$this->session->set_flashdata('error', $response->ErrorMessage);

					redirect(base_url() . 'invoices', 'refresh');
				} else {
					echo 'Sent message to ' . $to;
					
					$sms['sms'] =   1;

					$this->db->where('invoice_id', $invoice_id);
					$this->db->update('invoice', $sms);

					$this->session->set_flashdata('success', $this->lang->line('sms_sent_successfully'));

        			redirect(base_url() . 'invoices', 'refresh');
				}
			} else {
				$this->session->set_flashdata('error', $this->lang->line('tenant_mobile_number_not_found'));

				redirect(base_url() . 'invoices', 'refresh');
			}
        } else {
			$this->session->set_flashdata('error', $this->lang->line('twilio_conf_not_found') . '<a href="' . base_url('website_settings') . '">' . $this->lang->line('website_settings') . '</a>');

			redirect(base_url() . 'invoices', 'refresh');
		}
	}

	function update_invoice($invoice_id = '', $invoice_type = '')
	{
		$data['status']					= 	$this->input->post('status');
		$data['month']					=	$this->input->post('month');
		$data['year']					=	$this->input->post('year');
		$data['amount']					=	$this->input->post('amount');

		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->where('tenant_rent_id', $invoice_id);
		$this->db->update('tenant_rent', $data);

		$this->session->set_flashdata('success', $this->lang->line('rent_invoice_updated_successfully'));

		redirect(base_url() . 'invoices', 'refresh');
	}

	function update_invoice_number($invoice_id = '')
	{
		$data['invoice_number'] = $this->input->post('invoice_number');
		$data['timestamp'] = time();
		$data['updated_by'] = $this->session->userdata('user_id');

		$this->db->where('invoice_id', $invoice_id);
		$this->db->update('invoice', $data);

		$this->session->set_flashdata('success', 'Invoice number updated successfully');

		redirect(base_url() . 'invoices', 'refresh');
	}

	function update_invoice_status($invoice_id = '')
	{
		$data['status']					= 	$this->input->post('status');
		$data['payment_method_id']		= 	$this->input->post('payment_method_id');
		$data['late_fee']				=	$this->input->post('late_fee') > 0 ? $this->input->post('late_fee') : 0;
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->where('invoice_id', $invoice_id);
		$this->db->update('invoice', $data);

		$tenant_rents = $this->db->get_where('tenant_rent', array('invoice_id' => $invoice_id))->result_array();
		foreach ($tenant_rents as $tenant_rent) {
			$data2['status']				=	$this->input->post('status');
			$data2['timestamp']			=	time();

			$this->db->where('tenant_rent_id', $tenant_rent['tenant_rent_id']);
			$this->db->update('tenant_rent', $data2);
		}

		$this->session->set_flashdata('success', $this->lang->line('rent_invoice_status_updated_successfully'));

		redirect(base_url() . 'invoices', 'refresh');
	}

	function add_transaction($invoice_id = '')
	{
		// Create table if not exists
		$this->db->query("CREATE TABLE IF NOT EXISTS invoice_transaction (
			transaction_id INT(11) AUTO_INCREMENT PRIMARY KEY,
			invoice_id INT(11),
			amount FLOAT,
			payment_method_id INT(11),
			reference_number VARCHAR(255),
			created_on INT(11),
			created_by INT(11)
		)");

		$data['invoice_id'] = $invoice_id;
		$data['amount'] = $this->input->post('amount');
		$data['payment_method_id'] = $this->input->post('payment_method_id');
		$data['reference_number'] = $this->input->post('reference_number');
		$data['created_on'] = time();
		$data['created_by'] = $this->session->userdata('user_id');

		$this->db->insert('invoice_transaction', $data);

		$this->session->set_flashdata('success', 'Transaction added successfully');

		redirect(base_url() . 'invoices', 'refresh');
	}

	function update_invoice_services($invoice_id = '')
	{
		$services_from_db	=	$this->db->get_where('invoice_service', array('invoice_id' => $invoice_id))->result_array();
		foreach ($services_from_db as $row) {
			$this->db->where('invoice_service_id', $row['invoice_service_id']);
			$this->db->delete('invoice_service');
		}

		$service_ids	= 	$this->input->post('service_ids');
		$years 			=	$this->input->post('years');
		$months 		= 	$this->input->post('months');

		foreach ($service_ids as $key => $value) {
            $data['service_id']	=	$value;
            $data['year']     	=   $years[$key];
            $data['month']      =   $months[$key];
            $data['invoice_id'] =   $invoice_id;
            $data['created_on']	= 	time();
			$data['created_by']	=	$this->session->userdata('user_id');
			$data['timestamp']	=	time();
			$data['updated_by']	=	$this->session->userdata('user_id');

            $this->db->insert('invoice_service', $data);
        }

		redirect(base_url('invoice/' . $invoice_id), 'refresh');
	}

	function remove_invoice($invoice_id = '')
	{
		$tenant_rents = $this->db->get_where('tenant_rent', array('invoice_id' => $invoice_id))->result_array();
		foreach ($tenant_rents as $tenant_rent) {
			$this->db->where('invoice_id', $tenant_rent['invoice_id']);
			$this->db->delete('tenant_rent');
		}

		$this->db->where('invoice_id', $invoice_id);
		$this->db->delete('invoice');

		if (file_exists('uploads/invoices/' . $invoice_id . '.pdf'))
			unlink('uploads/invoices/' . $invoice_id . '.pdf');

		$this->session->set_flashdata('success', $this->lang->line('rent_invoice_deleted_successfully'));

		redirect(base_url() . 'invoices', 'refresh');
	}

	function add_notice()
	{
	    $ext 								= 	pathinfo($_FILES['image_link']['name'], PATHINFO_EXTENSION);

		if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
			$data['image_link'] 			= 	'notice_' . time() . '.' . $ext;
			$upload_dir = $this->ensure_upload_directory('uploads/notice');
			$target_file = $upload_dir . $data['image_link'];
			$move_result = move_uploaded_file($_FILES['image_link']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Notice image upload failed. Please try again.');
				redirect(base_url() . 'add_notice', 'refresh');
			}
		}
	    
		$data['title']						=	$this->input->post('title');
		$data['notice']						=	$this->input->post('notice');
		$data['created_on']					=	time();
		$data['created_by']					=	$this->session->userdata('user_id');
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->insert('notice', $data);

		$this->session->set_flashdata('success', $this->lang->line('notice_added_successfully'));

		redirect(base_url() . 'notices', 'refresh');
	}

	function update_notice($notice_id = '')
	{
		$data['title']						=	$this->input->post('title');
		$data['notice']						=	$this->input->post('notice');
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->where('notice_id', $notice_id);
		$this->db->update('notice', $data);

		$this->session->set_flashdata('success', $this->lang->line('notice_updated_successfully'));

		redirect(base_url() . 'notices', 'refresh');
	}

	function remove_notice($notice_id = '')
	{
		$this->db->where('notice_id', $notice_id);
		$this->db->delete('notice');

		$this->session->set_flashdata('success', $this->lang->line('notice_deleted_successfully'));

		redirect(base_url() . 'notices', 'refresh');
	}

	// ===================================================================
	// Staff Training Module
	// ===================================================================

	/**
	 * Convert a YouTube URL (any form) to an embed URL suitable for <iframe src="...">.
	 * Returns null if the input is not a recognised YouTube URL.
	 */
	private function _youtube_url_to_embed($url)
	{
		if (!is_string($url) || $url === '') return null;
		$url = trim($url);

		// youtu.be/<id>
		if (preg_match('#^https?://youtu\.be/([A-Za-z0-9_\-]+)#i', $url, $m)) {
			return 'https://www.youtube.com/embed/' . $m[1];
		}
		// youtube.com/watch?v=<id>
		if (preg_match('#^https?://(www\.)?youtube\.com/watch\?.*v=([A-Za-z0-9_\-]+)#i', $url, $m)) {
			return 'https://www.youtube.com/embed/' . $m[2];
		}
		// youtube.com/embed/<id> (already embed)
		if (preg_match('#^https?://(www\.)?youtube\.com/embed/([A-Za-z0-9_\-]+)#i', $url, $m)) {
			return 'https://www.youtube.com/embed/' . $m[2];
		}
		// youtube.com/shorts/<id>
		if (preg_match('#^https?://(www\.)?youtube\.com/shorts/([A-Za-z0-9_\-]+)#i', $url, $m)) {
			return 'https://www.youtube.com/embed/' . $m[2];
		}
		return null;
	}

	function add_training_lesson()
	{
		$user_id = $this->session->userdata('user_id');
		$now     = time();

		// --- Video handling: either upload OR YouTube link (mutually exclusive) ---
		$video_type = null;
		$video_path = null;

		// 1) Uploaded MP4 (if provided)
		if (!empty($_FILES['video_upload']['name'])) {
			$ext = strtolower(pathinfo($_FILES['video_upload']['name'], PATHINFO_EXTENSION));
			if (in_array($ext, array('mp4'), true)) {
				$upload_dir = $this->ensure_upload_directory('uploads/training');
				$filename   = 'training_video_' . $now . '.' . $ext;
				$target     = $upload_dir . $filename;
				if (move_uploaded_file($_FILES['video_upload']['tmp_name'], $target)) {
					$video_type = 'upload';
					$video_path = $filename;
				}
			} else {
				$this->session->set_flashdata('warning', 'Only MP4 video files are allowed for video upload.');
			}
		}

		// 2) YouTube embed URL (overrides upload if both supplied)
		if (!empty($_POST['youtube_url'])) {
			$embed = $this->_youtube_url_to_embed($this->input->post('youtube_url'));
			if ($embed !== null) {
				// If we already had an upload, remove it
				if ($video_type === 'upload' && $video_path && file_exists(FCPATH . 'uploads/training/' . $video_path)) {
					@unlink(FCPATH . 'uploads/training/' . $video_path);
				}
				$video_type = 'youtube';
				$video_path = $embed;
			} else {
				$this->session->set_flashdata('warning', 'Invalid YouTube URL. Please paste a valid youtu.be or youtube.com link.');
			}
		}

		$data = array(
			'title'      => $this->input->post('title'),
			'video_type' => $video_type,
			'video_path' => $video_path,
			'body_html'  => $this->input->post('body_html'),
			'created_by' => $user_id,
			'created_on' => $now,
			'updated_by' => $user_id,
			'timestamp'  => $now,
		);
		$this->db->insert('training_lesson', $data);
		$lesson_id = $this->db->insert_id();

		// --- Multiple image uploads ---
		if (!empty($_FILES['images']['name'][0])) {
			$upload_dir = $this->ensure_upload_directory('uploads/training');
			$file_count = count($_FILES['images']['name']);
			for ($i = 0; $i < $file_count; $i++) {
				if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
				$ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
				if (!in_array($ext, array('jpg','jpeg','png'), true)) continue;
				$filename = 'training_img_' . $now . '_' . $i . '.' . $ext;
				$target   = $upload_dir . $filename;
				if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target)) {
					$this->db->insert('training_lesson_image', array(
						'lesson_id'  => $lesson_id,
						'image_path' => $filename,
						'timestamp'  => time(),
					));
				}
			}
		}

		$this->session->set_flashdata('success', 'Training lesson added successfully.');
		redirect(base_url() . 'staff_training', 'refresh');
	}

	function update_training_lesson($lesson_id = '')
	{
		$user_id = $this->session->userdata('user_id');
		$now     = time();
		$lesson  = $this->db->get_where('training_lesson', array('lesson_id' => (int)$lesson_id))->row();
		if (!$lesson) {
			$this->session->set_flashdata('warning', 'Training lesson not found.');
			redirect(base_url() . 'staff_training', 'refresh');
		}

		// Determine new video type
		$video_type = $lesson->video_type; // keep current by default
		$video_path = $lesson->video_path;

		// 1) If a new uploaded video is supplied, replace
		if (!empty($_FILES['video_upload']['name'])) {
			$ext = strtolower(pathinfo($_FILES['video_upload']['name'], PATHINFO_EXTENSION));
			if ($ext === 'mp4') {
				$upload_dir = $this->ensure_upload_directory('uploads/training');
				$filename   = 'training_video_' . $now . '.' . $ext;
				$target     = $upload_dir . $filename;
				if (move_uploaded_file($_FILES['video_upload']['tmp_name'], $target)) {
					// remove old upload if any
					if ($lesson->video_type === 'upload' && $lesson->video_path && file_exists(FCPATH . 'uploads/training/' . $lesson->video_path)) {
						@unlink(FCPATH . 'uploads/training/' . $lesson->video_path);
					}
					$video_type = 'upload';
					$video_path = $filename;
				}
			} else {
				$this->session->set_flashdata('warning', 'Only MP4 video files are allowed.');
			}
		}

		// 2) YouTube link (overrides upload if supplied)
		if (!empty($_POST['youtube_url'])) {
			$embed = $this->_youtube_url_to_embed($this->input->post('youtube_url'));
			if ($embed !== null) {
				if ($video_type === 'upload' && $video_path && file_exists(FCPATH . 'uploads/training/' . $video_path)) {
					@unlink(FCPATH . 'uploads/training/' . $video_path);
				}
				$video_type = 'youtube';
				$video_path = $embed;
			} else {
				$this->session->set_flashdata('warning', 'Invalid YouTube URL.');
			}
		}

		// 3) "Remove video" checkbox
		if ($this->input->post('remove_video') === '1') {
			if ($lesson->video_type === 'upload' && $lesson->video_path && file_exists(FCPATH . 'uploads/training/' . $lesson->video_path)) {
				@unlink(FCPATH . 'uploads/training/' . $lesson->video_path);
			}
			$video_type = null;
			$video_path = null;
		}

		$data = array(
			'title'      => $this->input->post('title'),
			'video_type' => $video_type,
			'video_path' => $video_path,
			'body_html'  => $this->input->post('body_html'),
			'updated_by' => $user_id,
			'timestamp'  => $now,
		);
		$this->db->where('lesson_id', (int)$lesson_id);
		$this->db->update('training_lesson', $data);

		// --- Append any newly uploaded images ---
		if (!empty($_FILES['images']['name'][0])) {
			$upload_dir = $this->ensure_upload_directory('uploads/training');
			$file_count = count($_FILES['images']['name']);
			for ($i = 0; $i < $file_count; $i++) {
				if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
				$ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
				if (!in_array($ext, array('jpg','jpeg','png'), true)) continue;
				$filename = 'training_img_' . $now . '_' . $i . '.' . $ext;
				$target   = $upload_dir . $filename;
				if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target)) {
					$this->db->insert('training_lesson_image', array(
						'lesson_id'  => (int)$lesson_id,
						'image_path' => $filename,
						'timestamp'  => $now,
					));
				}
			}
		}

		$this->session->set_flashdata('success', 'Training lesson updated successfully.');
		redirect(base_url() . 'staff_training', 'refresh');
	}

	function remove_training_lesson($lesson_id = '')
	{
		$lesson = $this->db->get_where('training_lesson', array('lesson_id' => (int)$lesson_id))->row();
		if ($lesson) {
			// Remove uploaded video file
			if ($lesson->video_type === 'upload' && $lesson->video_path && file_exists(FCPATH . 'uploads/training/' . $lesson->video_path)) {
				@unlink(FCPATH . 'uploads/training/' . $lesson->video_path);
			}
			// Remove all associated image files
			$images = $this->db->get_where('training_lesson_image', array('lesson_id' => (int)$lesson_id))->result_array();
			foreach ($images as $img) {
				$path = FCPATH . 'uploads/training/' . $img['image_path'];
				if (file_exists($path)) @unlink($path);
			}
			$this->db->where('lesson_id', (int)$lesson_id);
			$this->db->delete('training_lesson_image');
			$this->db->where('lesson_id', (int)$lesson_id);
			$this->db->delete('training_lesson');
		}
		$this->session->set_flashdata('success', 'Training lesson deleted successfully.');
		redirect(base_url() . 'staff_training', 'refresh');
	}

	function remove_training_image($image_id = '')
	{
		$img = $this->db->get_where('training_lesson_image', array('image_id' => (int)$image_id))->row();
		if ($img) {
			$path = FCPATH . 'uploads/training/' . $img->image_path;
			if (file_exists($path)) @unlink($path);
			$this->db->where('image_id', (int)$image_id);
			$this->db->delete('training_lesson_image');
		}
		$redirect_to = $this->input->get('return') ? $this->input->get('return') : ('edit_staff_training/' . (int)$img->lesson_id);
		redirect(base_url() . $redirect_to, 'refresh');
	}

	function add_complaint()
	{
		$data['complaint_number']			=	$this->random_strings(11);
		$upload_dir							=	$this->ensure_upload_directory('uploads/complaints');

		$ext1 								= 	pathinfo($_FILES['complaint_picture_1']['name'], PATHINFO_EXTENSION);
		$ext2 								= 	pathinfo($_FILES['complaint_picture_2']['name'], PATHINFO_EXTENSION);
		$ext3 								= 	pathinfo($_FILES['complaint_video']['name'], PATHINFO_EXTENSION);

		if ($ext1 == 'pdf' || $ext1 == 'PDF' || $ext1 == 'jpeg' || $ext1 == 'JPEG' || $ext1 == 'png' || $ext1 == 'PNG' || $ext1 == 'jpg' || $ext1 == 'JPG') {
			$data['complaint_picture_1']	= 	$data['complaint_number'] . '_complaint_picture_1.' . $ext1;

			$target_file = $upload_dir . $data['complaint_picture_1'];
			$move_result = move_uploaded_file($_FILES['complaint_picture_1']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Complaint picture 1 upload failed. Please try again.');
				redirect(base_url() . 'complaints', 'refresh');
			}
		}

		if ($ext2 == 'pdf' || $ext2 == 'PDF' || $ext2 == 'jpeg' || $ext2 == 'JPEG' || $ext2 == 'png' || $ext2 == 'PNG' || $ext2 == 'jpg' || $ext2 == 'JPG') {
			$data['complaint_picture_2'] 	= 	$data['complaint_number'] . '_complaint_picture_2.' . $ext2;

			$target_file = $upload_dir . $data['complaint_picture_2'];
			$move_result = move_uploaded_file($_FILES['complaint_picture_2']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Complaint picture 2 upload failed. Please try again.');
				redirect(base_url() . 'complaints', 'refresh');
			}
		}

		if ($ext3 == 'mp4' || $ext3 == 'MP4') {
			$data['complaint_video']		= 	$data['complaint_number'] . '_complaint_video.' . $ext3;

			$target_file = $upload_dir . $data['complaint_video'];
			$move_result = move_uploaded_file($_FILES['complaint_video']['tmp_name'], $target_file);

			if (!$move_result || !file_exists($target_file)) {
				$this->session->set_flashdata('warning', 'Complaint video upload failed. Please try again.');
				redirect(base_url() . 'complaints', 'refresh');
			}
		}

		$data['subject']					=	$this->input->post('subject');
		$data['status']						=	0;
		$data['tenant_id']					=	($this->session->userdata('user_type') == 3) ? $this->security->xss_clean($this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id) : $this->input->post('tenant_id');
		$data['created_on']					=	time();
		$data['created_by']					=	$this->session->userdata('user_id');
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->insert('complaint', $data);

		$data2['complaint_id']					=	$this->db->insert_id();
		$data2['content']					=	$this->input->post('content');
		$data2['created_on']				=	time();
		$data2['created_by']				=	$this->session->userdata('user_id');
		$data2['timestamp']					=	time();
		$data2['updated_by']				=	$this->session->userdata('user_id');

		$this->db->insert('complaint_details', $data2);

		$this->session->set_flashdata('success', $this->lang->line('complaint_added_successfully'));

		redirect(base_url() . 'complaints', 'refresh');
	}

	private function random_strings($length_of_string)
	{
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle($str_result), 0, $length_of_string);
	}

	function update_complaint($complaint_id = '')
	{
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->where('complaint_id', $complaint_id);
		$this->db->update('complaint', $data);

		$data2['complaint_id']					=	$complaint_id;
		$data2['content']					=	$this->input->post('content');
		$data2['created_on']				=	time();
		$data2['created_by']				=	$this->session->userdata('user_id');
		$data2['timestamp']					=	time();
		$data2['updated_by']				=	$this->session->userdata('user_id');

		$this->db->insert('complaint_details', $data2);

		$this->session->set_flashdata('success', $this->lang->line('complaint_replied_successfully'));

		redirect(base_url() . 'complaints', 'refresh');
	}

	function close_complaint($complaint_id = '')
	{
		$data['status']						=	1;
		$data['timestamp']					=	time();
		$data['updated_by']					=	$this->session->userdata('user_id');

		$this->db->where('complaint_id', $complaint_id);
		$this->db->update('complaint', $data);

		$this->session->set_flashdata('success', $this->lang->line('complaint_closed_successfully'));

		redirect(base_url() . 'complaints', 'refresh');
	}

	// Function related to adding id type
	function add_id_type()
	{
		$data['name']					=	$this->input->post('name');
		$data['created_on']				= 	time();
		$data['created_by']				= 	$this->session->userdata('user_id');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->insert('id_type', $data);

		$this->session->set_flashdata('success', $this->lang->line('id_type_added_successfully'));

		redirect(base_url() . 'id_type_settings', 'refresh');
	}

	// Function related to updating profession
	function update_id_type($id_type_id = '')
	{
		$data['name']					=	$this->input->post('name');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->where('id_type_id', $id_type_id);
		$this->db->update('id_type', $data);

		$this->session->set_flashdata('success', $this->lang->line('id_type_updated_successfully'));

		redirect(base_url() . 'id_type_settings', 'refresh');
	}

	// Function related to website settings
	function update_website_settings()
	{
		if ($this->input->post('system_name')) {
			$data1['content']			=	$this->input->post('system_name');

			$this->db->where('name', 'system_name');
			$this->db->update('setting', $data1);
		}

		if ($this->input->post('currency')) {
			$data2['content']			=	$this->input->post('currency');

			$this->db->where('name', 'currency');
			$this->db->update('setting', $data2);
		}

		if ($this->input->post('tagline')) {
			$data3['content']			=	$this->input->post('tagline');

			$this->db->where('name', 'tagline');
			$this->db->update('setting', $data3);
		}

		if ($this->input->post('language')) {
			$data4['content']			=	$this->input->post('language');

			$this->db->where('name', 'language');
			$this->db->update('setting', $data4);
		}

		if ($this->input->post('address_line_1') && $this->input->post('address_line_2')) {
			$data6['content']			=	$this->input->post('address_line_1') . '<br>' . $this->input->post('address_line_2');

			$this->db->where('name', 'address');
			$this->db->update('setting', $data6);
		}

		if ($this->input->post('copyright')) {
			$data1['content']			=	$this->input->post('copyright');

			$this->db->where('name', 'copyright');
			$this->db->update('setting', $data1);
		}

		if ($this->input->post('copyright_url')) {
			$data1['content']			=	$this->input->post('copyright_url');

			$this->db->where('name', 'copyright_url');
			$this->db->update('setting', $data1);
		}

		// Font changing switch case of the system
		if ($this->input->post('font')) {
			switch ($this->input->post('font')) {
				case 'PT Sans Narrow':
					$font['content']        =   "'PT Sans Narrow', sans-serif";
					$font_family['content'] =   "PT Sans Narrow";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=PT+Sans+Narrow:wght@400;700&display=swap";
					break;
				case 'Josefin Sans':
					$font['content']        =   "'Josefin Sans', sans-serif";
					$font_family['content'] =   "Josefin Sans";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap";
					break;
				case 'Titillium Web':
					$font['content']        =   "'Titillium Web', sans-serif";
					$font_family['content'] =   "Titillium Web";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap";
					break;
				case 'Mukta':
					$font['content']        =   "'Mukta', sans-serif";
					$font_family['content'] =   "Mukta";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Mukta:wght@200;300;400;500;600;700;800&display=swap";
					break;
				case 'PT Sans':
					$font['content']        =   "'PT Sans', sans-serif";
					$font_family['content'] =   "PT Sans";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap";
					break;
				case 'Rubik':
					$font['content']        =   "'Rubik', sans-serif";
					$font_family['content'] =   "Rubik";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap";
					break;
				case 'Oswald':
					$font['content']        =   "'Oswald', sans-serif";
					$font_family['content'] =   "Oswald";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&display=swap";
					break;
				case 'Poppins':
					$font['content']        =   "'Poppins', sans-serif";
					$font_family['content'] =   "Poppins";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap";
					break;
				case 'Open Sans':
					$font['content']        =   "'Open Sans', sans-serif";
					$font_family['content'] =   "Open Sans";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap";
					break;
				case 'Cantarell':
					$font['content']        =   "'Cantarell', sans-serif";
					$font_family['content'] =   "Cantarell";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Cantarell:ital,wght@0,400;0,700;1,400;1,700&display=swap";
					break;
				case 'Ubuntu':
					$font['content']        =   "'Ubuntu', sans-serif";
					$font_family['content'] =   "Ubuntu";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap";
					break;
				default:
					$font['content']        =   "'PT Sans Narrow', sans-serif";
					$font_family['content'] =   "PT Sans Narrow";
					$font_src['content']    =   "https://fonts.googleapis.com/css2?family=PT+Sans+Narrow:wght@400;700&display=swap";
			}
	
			$this->db->where('name', 'font');
			$this->db->update('setting', $font);
			$this->db->where('name', 'font_family');
			$this->db->update('setting', $font_family);
			$this->db->where('name', 'font_src');
			$this->db->update('setting', $font_src);
		}

		$this->session->set_flashdata('success', $this->lang->line('website_settings_updated_successfully'));

		redirect(base_url() . 'website_settings', 'refresh');
	}

	// Function realted to website favicon update
	function update_website_favicon()
	{
		$ext 							= 	pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);

		if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
			$favicon 					= 	$this->db->get_where('setting', array('name' => 'favicon'))->row()->content;

			if (isset($favicon) && file_exists(FCPATH . 'uploads/website/' . $favicon)) {
				unlink(FCPATH . 'uploads/website/' . $favicon);
			}

			$data['content'] 			= 	$_FILES['favicon']['name'];
			$data['timestamp']			=	time();
			$data['updated_by']			=	$this->session->userdata('user_id');

			move_uploaded_file($_FILES['favicon']['tmp_name'], FCPATH . 'uploads/website/' . $data['content']);

			$this->db->where('name', 'favicon');
			$this->db->update('setting', $data);

			$this->session->set_flashdata('success', $this->lang->line('website_favicon_updated_successfully'));

			redirect(base_url() . 'website_settings', 'refresh');
		} else {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));

			redirect(base_url() . 'website_settings', 'refresh');
		}
	}

	// Function realted to website login background update
	function update_website_login_bg()
	{
		$ext 							= 	pathinfo($_FILES['login_bg']['name'], PATHINFO_EXTENSION);

		if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
			$login_bg 					= 	$this->db->get_where('setting', array('name' => 'login_bg'))->row()->content;

			if (isset($login_bg) && file_exists(FCPATH . 'uploads/website/' . $login_bg)) {
				unlink(FCPATH . 'uploads/website/' . $login_bg);
			}

			$data['content'] 			= 	$_FILES['login_bg']['name'];
			$data['timestamp']			=	time();
			$data['updated_by']			=	$this->session->userdata('user_id');

			move_uploaded_file($_FILES['login_bg']['tmp_name'], FCPATH . 'uploads/website/' . $data['content']);

			$this->db->where('name', 'login_bg');
			$this->db->update('setting', $data);

			$this->session->set_flashdata('success', $this->lang->line('website_login_background_updated_successfully'));

			redirect(base_url() . 'website_settings', 'refresh');
		} else {
			$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));

			redirect(base_url() . 'website_settings', 'refresh');
		}
	}

		function update_owner_agreement_settings()
	{
		if (!empty($_FILES['rent_agreement']['name'])) {
			$config['upload_path']   = FCPATH . 'uploads/website/';
			$config['allowed_types'] = 'pdf|jpg|jpeg|png';
			$config['file_name']     = 'rent_agreement_' . time();
			$config['max_size']      = 20480; // 20MB
			$config['overwrite']     = TRUE;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($this->upload->do_upload('rent_agreement')) {
				$upload_data = $this->upload->data();

				$rent_agreement = $this->db->get_where('setting', array('name' => 'rent_agreement'))->row();

				if ($rent_agreement && $rent_agreement->content != '' && file_exists(FCPATH . 'uploads/website/' . $rent_agreement->content)) {
					unlink(FCPATH . 'uploads/website/' . $rent_agreement->content);
				}

				$data['content'] = $upload_data['file_name'];
				$data['timestamp'] = time();
				$data['updated_by'] = $this->session->userdata('user_id');

				if ($rent_agreement) {
					$this->db->where('name', 'rent_agreement');
					$this->db->update('setting', $data);
				} else {
					$data['created_on'] = time();
					$data['created_by'] = $this->session->userdata('user_id');
					$data['name'] = 'rent_agreement';
					$this->db->insert('setting', $data);
				}

				$this->session->set_flashdata('success', 'Rent Agreement updated successfully');
			} else {
				$this->session->set_flashdata('warning', strip_tags($this->upload->display_errors()));
			}
		} else {
			$this->session->set_flashdata('warning', 'Please select a file to upload.');
		}

		redirect(base_url() . 'owner_agreement_settings', 'refresh');
	}

	function update_website_trade_licence()
	{
		if (!empty($_FILES['trade_licence']['name'])) {
			$config['upload_path']   = FCPATH . 'uploads/website/';
			$config['allowed_types'] = 'pdf|jpg|jpeg|png';
			$config['file_name']     = 'trade_licence_' . time();
			$config['max_size']      = 20480;
			$config['overwrite']     = TRUE;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($this->upload->do_upload('trade_licence')) {
				$upload_data = $this->upload->data();
				$trade_licence = $this->db->get_where('setting', array('name' => 'trade_licence'))->row();

				if ($trade_licence && $trade_licence->content != '' && file_exists(FCPATH . 'uploads/website/' . $trade_licence->content)) {
					unlink(FCPATH . 'uploads/website/' . $trade_licence->content);
				}

				$data['content'] = $upload_data['file_name'];
				$data['timestamp'] = time();
				$data['updated_by'] = $this->session->userdata('user_id');

				if ($trade_licence) {
					$this->db->where('name', 'trade_licence');
					$this->db->update('setting', $data);
				} else {
					$data['name'] = 'trade_licence';
					$data['created_on'] = time();
					$data['created_by'] = $this->session->userdata('user_id');
					$this->db->insert('setting', $data);
				}

				$this->session->set_flashdata('success', 'Trade licence updated successfully');
			} else {
				$this->session->set_flashdata('warning', strip_tags($this->upload->display_errors()));
			}
		} else {
			$this->session->set_flashdata('warning', 'Please select a trade licence to upload.');
		}

		redirect(base_url() . 'website_settings', 'refresh');
	}

	function update_website_gst_certificate()
	{
		$user_id = $this->session->userdata('user_id');

		// --- Save GST Number and GST Enabled flag (always processed, even if no file uploaded) ---
		$gst_number  = trim((string)$this->input->post('gst_number'));
		$gst_enabled = ($this->input->post('gst_enabled') === '1') ? '1' : '0';

		$this->_save_setting('gst_number', $gst_number, $user_id);
		$this->_save_setting('gst_enabled', $gst_enabled, $user_id);

		// --- Save GST Certificate file (optional) ---
		if (!empty($_FILES['gst_certificate']['name'])) {
			$config['upload_path']   = FCPATH . 'uploads/website/';
			$config['allowed_types'] = 'pdf|jpg|jpeg|png';
			$config['file_name']     = 'gst_certificate_' . time();
			$config['max_size']      = 20480;
			$config['overwrite']     = TRUE;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($this->upload->do_upload('gst_certificate')) {
				$upload_data = $this->upload->data();
				$gst_certificate = $this->db->get_where('setting', array('name' => 'gst_certificate'))->row();

				if ($gst_certificate && $gst_certificate->content != '' && file_exists(FCPATH . 'uploads/website/' . $gst_certificate->content)) {
					unlink(FCPATH . 'uploads/website/' . $gst_certificate->content);
				}

				$data['content'] = $upload_data['file_name'];
				$data['timestamp'] = time();
				$data['updated_by'] = $user_id;

				if ($gst_certificate) {
					$this->db->where('name', 'gst_certificate');
					$this->db->update('setting', $data);
				} else {
					$data['name'] = 'gst_certificate';
					$data['created_on'] = time();
					$data['created_by'] = $user_id;
					$this->db->insert('setting', $data);
				}

				$this->session->set_flashdata('success', 'GST settings updated successfully');
			} else {
				$this->session->set_flashdata('warning', strip_tags($this->upload->display_errors()));
				redirect(base_url() . 'website_settings', 'refresh');
				return;
			}
		} else {
			// Only GST number / enabled flag was submitted
			$this->session->set_flashdata('success', 'GST settings updated successfully');
		}

		redirect(base_url() . 'website_settings', 'refresh');
	}

	// Helper: insert or update a row in the `setting` table by name
	private function _save_setting($name, $content, $user_id)
	{
		$row = $this->db->get_where('setting', array('name' => $name))->row();
		$data = array(
			'content'   => (string)$content,
			'timestamp' => time(),
			'updated_by'=> $user_id,
		);

		if ($row) {
			$this->db->where('name', $name);
			$this->db->update('setting', $data);
		} else {
			$data['name']       = $name;
			$data['created_on'] = time();
			$data['created_by'] = $user_id;
			$this->db->insert('setting', $data);
		}
	}

	function update_tenant_agreement_settings()
	{
		$data['content'] = $this->input->post('tenant_agreement_template');
		$data['timestamp'] = time();
		$data['updated_by'] = $this->session->userdata('user_id');

		$template_setting = $this->db->get_where('setting', array('name' => 'tenant_agreement_template'))->row();

		if ($template_setting) {
			$this->db->where('name', 'tenant_agreement_template');
			$this->db->update('setting', $data);
		} else {
			$data['name'] = 'tenant_agreement_template';
			$data['created_on'] = time();
			$data['created_by'] = $this->session->userdata('user_id');
			$this->db->insert('setting', $data);
		}

		$this->session->set_flashdata('success', 'Tenant Agreement Template updated successfully');

		redirect(base_url() . 'tenant_agreement_settings', 'refresh');
	}

	function upload_signed_agreement($tenant_id)
	{
		if (!empty($_FILES['signed_agreement']['name'])) {
			$config['upload_path']   = FCPATH . 'uploads/tenants/';
			$config['allowed_types'] = 'pdf|jpg|jpeg|png';
			$config['file_name']     = 'signed_agreement_' . $tenant_id . '_' . time();
			$config['max_size']      = 20480; // 20MB
			$config['overwrite']     = TRUE;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($this->upload->do_upload('signed_agreement')) {
				$upload_data = $this->upload->data();
				
				$tenant = $this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row();
				
				if ($tenant && $tenant->signed_agreement != '' && file_exists(FCPATH . 'uploads/tenants/' . $tenant->signed_agreement)) {
					unlink(FCPATH . 'uploads/tenants/' . $tenant->signed_agreement);
				}

				$data['signed_agreement'] = $upload_data['file_name'];
				$data['timestamp'] = time();
				$data['updated_by'] = $this->session->userdata('user_id');

				$this->db->where('tenant_id', $tenant_id);
				$this->db->update('tenant', $data);

				$this->session->set_flashdata('success', 'Signed Agreement uploaded successfully');
			} else {
				$this->session->set_flashdata('warning', strip_tags($this->upload->display_errors()));
			}
		} else {
			$this->session->set_flashdata('warning', 'Please select a file to upload.');
		}
		
		redirect(base_url() . 'tenants', 'refresh');
	}

	// Function related to website smtp
	function update_website_smtp()
	{
		if ($this->input->post('smtp_user')) {
			$data1['content']			=	$this->input->post('smtp_user');

			$this->db->where('name', 'smtp_user');
			$this->db->update('setting', $data1);
		}

		if ($this->input->post('smtp_pass')) {
			$data2['content']			=	$this->input->post('smtp_pass');

			$this->db->where('name', 'smtp_pass');
			$this->db->update('setting', $data2);
		}

		$this->session->set_flashdata('success', $this->lang->line('website_smtp_updated_successfully'));

		redirect(base_url() . 'website_settings', 'refresh');
	}

	// Function related to website twilio
    function delete_website_smtp()
    {
        $data['content']			=	'';

        $this->db->where('name', 'smtp_user');
        $this->db->update('setting', $data);

        $this->db->where('name', 'smtp_pass');
        $this->db->update('setting', $data);

        $this->session->set_flashdata('success', $this->lang->line('website_smtp_deleted_successfully'));

		redirect(base_url() . 'website_settings', 'refresh');
    }

	// Function related to website twilio
	function update_website_twilio()
	{
		if ($this->input->post('account_sid')) {
			$data1['content']			=	$this->input->post('account_sid');

			$this->db->where('name', 'account_sid');
			$this->db->update('setting', $data1);
		}

		if ($this->input->post('auth_token')) {
			$data2['content']			=	$this->input->post('auth_token');

			$this->db->where('name', 'auth_token');
			$this->db->update('setting', $data2);
		}

        if ($this->input->post('number')) {
			$data3['content']			=	$this->input->post('number');

			$this->db->where('name', 'number');
			$this->db->update('setting', $data3);
		}

		$this->session->set_flashdata('success', $this->lang->line('website_twilio_updated_successfully'));

		redirect(base_url() . 'website_settings', 'refresh');
	}

    // Function related to website twilio
    function delete_website_twilio()
    {
        $data['content']			=	'';

        $this->db->where('name', 'account_sid');
        $this->db->update('setting', $data);

        $this->db->where('name', 'auth_token');
        $this->db->update('setting', $data);

        $this->db->where('name', 'number');
        $this->db->update('setting', $data);

        $this->session->set_flashdata('success', $this->lang->line('website_twilio_deleted_successfully'));

		redirect(base_url() . 'website_settings', 'refresh');
    }

	// Function related to adding profession
	function add_profession()
	{
		$data['name']					=	$this->input->post('name');
		$data['created_on']				= 	time();
		$data['created_by']				= 	$this->session->userdata('user_id');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->insert('profession', $data);

		$this->session->set_flashdata('success', $this->lang->line('profession_added_successfully'));

		redirect(base_url() . 'profession_settings', 'refresh');
	}

	// Function related to updating profession
	function update_profession($profession_id = '')
	{
		$data['name']					=	$this->input->post('name');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->where('profession_id', $profession_id);
		$this->db->update('profession', $data);

		$this->session->set_flashdata('success', $this->lang->line('profession_updated_successfully'));

		redirect(base_url() . 'profession_settings', 'refresh');
	}

	// Function related to adding service
	function add_service()
	{
		$data['name']					=	$this->input->post('name');
		$data['cost']					=	$this->input->post('cost');
		$data['created_on']				= 	time();
		$data['created_by']				= 	$this->session->userdata('user_id');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->insert('service', $data);

		$this->session->set_flashdata('success', $this->lang->line('service_added_successfully'));

		redirect(base_url() . 'service_settings', 'refresh');
	}

	// Function related to updating service
	function update_service($service_id = '')
	{
		$data['name']					=	$this->input->post('name');
		$data['cost']					=	$this->input->post('cost');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->where('service_id', $service_id);
		$this->db->update('service', $data);

		$this->session->set_flashdata('success', $this->lang->line('service_updated_successfully'));

		redirect(base_url() . 'service_settings', 'refresh');
	}

	// Function related to adding payment method
	function add_payment_method()
	{
		$data['name']					=	$this->input->post('name');
		$data['created_on']				= 	time();
		$data['created_by']				= 	$this->session->userdata('user_id');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->insert('payment_method', $data);

		$this->session->set_flashdata('success', $this->lang->line('payment_method_added_successfully'));

		redirect(base_url() . 'payment_method_settings', 'refresh');
	}

	// Function related to updating payment method
	function update_payment_method($payment_method_id = '')
	{
		$data['name']					=	$this->input->post('name');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->where('payment_method_id', $payment_method_id);
		$this->db->update('payment_method', $data);

		$this->session->set_flashdata('success', $this->lang->line('payment_method_updated_successfully'));

		redirect(base_url() . 'payment_method_settings', 'refresh');
	}

	// Function related to adding board member
	function add_board_member()
	{
		if ($_FILES['image']['name']) {
			$ext 						= 	pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

			if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
				$data['image']			=	'board_member_' . time() . '.' . $ext;
				$upload_dir = $this->ensure_upload_directory('uploads/board_members');
				$target_file = $upload_dir . $data['image'];
				$move_result = move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

				if (!$move_result || !file_exists($target_file)) {
					$this->session->set_flashdata('warning', 'Board member image upload failed. Please try again.');
					redirect(base_url() . 'board_member_settings', 'refresh');
				}
			} else {
				$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));

				redirect(base_url() . 'website_settings', 'refresh');
			}
		}		

		$data['name']					=	$this->input->post('name');
		$data['position']				=	$this->input->post('position');
		$data['serial']					=	$this->input->post('serial');
		$data['image']					=	isset($data['image']) ? $data['image'] : '';
		$data['created_on']				= 	time();
		$data['created_by']				= 	$this->session->userdata('user_id');
		$data['timestamp']				=	time();
		$data['updated_by']				=	$this->session->userdata('user_id');

		$this->db->insert('board_member', $data);

		$this->session->set_flashdata('success', $this->lang->line('board_member_added_successfully'));

		redirect(base_url() . 'board_member_settings', 'refresh');
	}

	// Function related to updating board member
	function update_board_member($board_member_id = '')
	{
		if ($_FILES['image']['name']) {
			$ext 						= 	pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

			if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'JPEG' || $ext == 'JPG' || $ext == 'PNG') {
				$upload_dir				=	$this->ensure_upload_directory('uploads/board_members');
				$image 					= 	$this->db->get_where('board_member', array('board_member_id' => $board_member_id))->row()->image;
				
				if (!empty($image) && file_exists($upload_dir . $image)) unlink($upload_dir . $image);

				$data['image']			=	'board_member_' . time() . '.' . $ext;
				$target_file = $upload_dir . $data['image'];
				$move_result = move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

				if (!$move_result || !file_exists($target_file)) {
					$this->session->set_flashdata('warning', 'Board member image upload failed. Please try again.');
					redirect(base_url() . 'board_member_settings', 'refresh');
				}
			} else {
				$this->session->set_flashdata('warning', $this->lang->line('tenant_image_supported_type'));

				redirect(base_url() . 'board_member_settings', 'refresh');
			}
		}

		$data['name']					=	$this->input->post('name');
		$data['position']				=	$this->input->post('position');
		$data['serial']					=	$this->input->post('serial');
		$data['timestamp']				= 	time();
		$data['updated_by']				= 	$this->session->userdata('user_id');

		$this->db->where('board_member_id', $board_member_id);
		$this->db->update('board_member', $data);

		$this->session->set_flashdata('success', $this->lang->line('board_member_updated_successfully'));

		redirect(base_url() . 'board_member_settings', 'refresh');
	}

	function update_profile_settings($user_id = '')
	{
		$db_password 					=	$this->db->get_where('user', array('user_id' => $user_id))->row()->password;
		$given_password 				=	$this->input->post('old_password');

		$existing_email 				= 	$this->db->get_where('user', array('user_id' => $user_id))->row()->email;

		if (password_verify($given_password, $db_password)) {
			if ($existing_email != $this->input->post('email')) {
				$users = $this->db->get('user')->result_array();
				foreach ($users as $user) {
					if ($user['email'] == $this->input->post('email')) {
						$this->session->set_flashdata('warning', $this->lang->line('tenant_email_already_registered'));

						redirect(base_url() . 'profile_settings', 'refresh');
					}
				}
			}

			$data['email']				=	$this->input->post('email');
			if ($this->input->post('new_password') && ($this->input->post('new_password') == $this->input->post('confirm_password'))) {
				$data['password']		=	password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);
			} else {
				$this->session->set_flashdata('warning', $this->lang->line('new_passwords_do_not_match'));

				redirect(base_url() . 'profile_settings', 'refresh');
			}

			$this->db->where('user_id', $user_id);
			$this->db->update('user', $data);

			$this->session->set_flashdata('success', $this->lang->line('profile_updated_successfully'));

			redirect(base_url() . 'profile_settings', 'refresh');
		} else {
			$this->session->set_flashdata('warning', $this->lang->line('passwords_do_not_match'));

			redirect(base_url() . 'profile_settings', 'refresh');
		}
	}
}
