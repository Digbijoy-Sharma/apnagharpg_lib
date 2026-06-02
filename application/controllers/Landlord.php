<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Landlord extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->output->set_header("X-Frame-Options: sameorigin");
		$this->output->set_header("X-XSS-Protection: 1; mode=block");
		$this->output->set_header("X-Content-Type-Options: nosniff");
		$this->output->set_header("Strict-Transport-Security: max-age=31536000");

		$this->lang->load('vasha', $this->db->get_where('setting', array('name' => 'language'))->row()->content);
	}

	public function index()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'dashboard'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Dashboard';
			$page_data['page_name']		=	'dashboard';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function login()
	{
		$this->load->view('login');
	}

	function add_room()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'rooms'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			$page_data['page_title']	=	'Add Seat';
			$page_data['page_name'] 	= 	'add_room';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function rooms($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'rooms'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			if ($param1 == 'add') $this->model->add_room();
			elseif ($param1 == 'update') $this->model->update_room($param2);
			elseif ($param1 == 'remove') $this->model->remove_room($param2);
			elseif ($param1 == 'assign_tenant') $this->model->assign_tenant($param2);
			elseif ($param1 == 'vacant') $this->model->vacant_room($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Seats';
			$page_data['page_name'] 	= 	'rooms';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function occupied_rooms()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'rooms'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Occupied Seats';
			$page_data['page_name'] 	= 	'occupied_rooms';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function unoccupied_rooms()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'rooms'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Available Seats';
			$page_data['page_name'] 	= 	'unoccupied_rooms';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function seat_occupancy_chart()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'rooms'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Seat Occupancy Chart';
			$page_data['page_name'] 	= 	'seat_occupancy_chart';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function add_tenant()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'tenants'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			$page_data['page_title']	=	'Add Student';
			$page_data['page_name'] 	= 	'add_tenant';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function tenants($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'tenants'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			if ($param1 == 'add') $this->model->add_tenant();
			elseif ($param1 == 'change_image') $this->model->change_tenant_image($param2);
			elseif ($param1 == 'change_id_image') $this->model->change_tenant_id_image($param2);
			elseif ($param1 == 'update') $this->model->update_tenant($param2);
			elseif ($param1 == 'deactivate') $this->model->deactivate_tenant($param2);
			elseif ($param1 == 'remove') $this->model->remove_tenant($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Students';
			$page_data['page_name'] 	= 	'tenants';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function tanent_details()
	{
	    
	    if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');
			
			if (in_array($this->db->get_where('module', array('module_name' => 'tenants'))->row()->module_id, $this->session->userdata('permissions'))) {
		$page_data['page_title']	=	'Student Details';
			$page_data['page_name'] 	= 	'tanent_details';
			$this->load->view('index', $page_data);
			}else{
			    	$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
			}
	}

	function active_tenants()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'tenants'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Active Students';
			$page_data['page_name'] 	= 	'active_tenants';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function inactive_tenants()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'tenants'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Inactive Students';
			$page_data['page_name'] 	= 	'inactive_tenants';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function add_utility_bill()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'utility_bills'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Add Utility Bill';
			$page_data['page_name'] 	= 	'add_utility_bill';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function utility_bills($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'utility_bills'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_utility_bill();
			elseif ($param1 == 'update') $this->model->update_utility_bill($param2);
			elseif ($param1 == 'remove') $this->model->remove_utility_bill($param2);
			elseif ($param1 == 'change_image') $this->model->change_utility_image($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Utility Bills';
			$page_data['page_name'] 	= 	'utility_bills';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function utility_bill_categories($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'utility_bills'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_utility_bill_category();
			elseif ($param1 == 'update') $this->model->update_utility_bill_category($param2);
			elseif ($param1 == 'remove') $this->model->remove_utility_bill_category($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Utility Bill Categories';
			$page_data['page_name'] 	= 	'utility_bill_categories';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function add_expense()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'expenses'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Add Expense';
			$page_data['page_name'] 	= 	'add_expense';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function expenses($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'expenses'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_expense();
			elseif ($param1 == 'update') $this->model->update_expense($param2);
			elseif ($param1 == 'remove') $this->model->remove_expense($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Expenses';
			$page_data['page_name'] 	= 	'expenses';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function add_staff()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'staff'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Add Staff';
			$page_data['page_name'] 	= 	'add_staff';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function staff($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'staff'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_staff();
			elseif ($param1 == 'update') $this->model->update_staff($param2);
			elseif ($param1 == 'deactivate') $this->model->deactivate_staff($param2);
			elseif ($param1 == 'remove') $this->model->remove_staff($param2);
			elseif ($param1 == 'permission') $this->model->update_staff_permission($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Staff';
			$page_data['page_name'] 	= 	'staff';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function add_staff_payroll()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'staff_payroll'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Add Staff Payroll';
			$page_data['page_name'] 	= 	'add_staff_payroll';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function staff_payroll($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'staff_payroll'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_staff_salary();
			elseif ($param1 == 'update') $this->model->update_staff_salary($param2);
			elseif ($param1 == 'remove') $this->model->remove_staff_salary($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Staff Payroll';
			$page_data['page_name'] 	= 	'staff_payroll';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function single_month_staff_payroll($year = '', $month = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'staff_payroll'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['year']			=	$year;
			$page_data['month']			=	$month;

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Single Month Staff Payroll';
			$page_data['page_name'] 	= 	'single_month_staff_payroll';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function generate_invoice($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'generate_invoice'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param == 'range') $this->model->generate_date_range_rents();
			elseif ($param == 'multiple') $this->model->generate_multiple_months_rent();
			elseif ($param == 'single') $this->model->generate_single_months_rent();

			$page_data['page_title']	=	'Generate Study Plan Invoice';
			$page_data['page_name'] 	=	'generate_invoice';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function monthly_invoices()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Monthly Plan Invoices';
			$page_data['page_name'] 	=	'monthly_invoices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function single_month_invoices($year = '', $month = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['year']			=	$year;
			$page_data['month']			=	$month;

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Single Month Plan Invoices';
			$page_data['page_name'] 	=	'single_month_invoices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function tenant_invoices()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions')) && (count($this->db->get('tenant')->result_array()) > 0)) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Student Invoices';
			$page_data['page_name'] 	=	'tenant_invoices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function single_tenant_invoices($tenant_id = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions')) && (count($this->db->get('tenant')->result_array()) > 0)) {
			$page_data['tenant_id']		=	$tenant_id;

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Single Tenant Invoices';
			$page_data['page_name'] 	=	'single_tenant_invoices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function invoices($param1 = '', $param2 = '', $param3 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'update') $this->model->update_invoice($param2, $param3);
			if ($param1 == 'update_status') $this->model->update_invoice_status($param2);
			if ($param1 == 'update_invoice_number') $this->model->update_invoice_number($param2);
			if ($param1 == 'add_transaction') $this->model->add_transaction($param2);
			elseif ($param1 == 'remove') $this->model->remove_invoice($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'All Student Invoices';
			$page_data['page_name'] 	=	'invoices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function paid_invoices()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Paid Student Invoices';
			$page_data['page_name'] 	=	'paid_invoices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function unpaid_invoices()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Due Student Invoices';
			$page_data['page_name'] 	=	'unpaid_invoices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function generate_invoice_pdf($param = '')
	{
		$page_data['invoice_id'] = $param;
		$this->load->view('generate_invoice_pdf', $page_data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$this->pdf->loadHtml($html);
		$this->pdf->setPaper('A4', 'potrait');
		$this->pdf->render();

		// $this->pdf->stream("mypdf.pdf", array("Attachment" => 0));
		// exit(0);

		$pdf = $this->pdf->output();
		$file_location = $_SERVER['DOCUMENT_ROOT'] . '/uploads/invoices/' . $this->db->get_where('invoice', array('invoice_id' => $param))->row()->invoice_number . '.pdf';
		file_put_contents($file_location, $pdf);
	}

	function email_invoice($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		$this->email_model->send_invoice($param);
	}

	function sms_invoice($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		$this->model->send_invoice_sms($param);
	}

	function invoice($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) {

			$page_data['invoice_id']	=	$param;
			$page_data['page_title']	=	'Invoice';
			$page_data['page_name'] 	=	'invoice';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function invoice_services($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->update_invoice_services($param);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function add_notice()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'notices'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Add Notice';
			$page_data['page_name'] 	= 	'add_notice';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function notices($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'notices'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add' && $this->session->userdata('user_type') != 3) $this->model->add_notice();
			elseif ($param1 == 'update' && $this->session->userdata('user_type') != 3) $this->model->update_notice($param2);
			elseif ($param1 == 'remove' && $this->session->userdata('user_type') != 3) $this->model->remove_notice($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Notices';
			$page_data['page_name'] 	= 	'notices';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function add_complaint()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'complaints'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Add Complaint';
			$page_data['page_name'] 	= 	'add_complaint';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function complaints($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'complaints'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_complaint();
			elseif ($param1 == 'update') $this->model->update_complaint($param2);
			elseif ($param1 == 'close') $this->model->close_complaint($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Complaints';
			$page_data['page_name'] 	= 	'complaints';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function open_complaints()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'complaints'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Open Complaints';
			$page_data['page_name'] 	= 	'open_complaints';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function closed_complaints()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'complaints'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Closed Complaints';
			$page_data['page_name'] 	= 	'closed_complaints';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function account($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'account'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Account';
			$page_data['page_name'] 	=	'account';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function bank_statements($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'account'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_bank_statement();
			elseif ($param1 == 'remove') $this->model->remove_bank_statement($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Bank Statements';
			$page_data['page_name'] 	=	'bank_statements';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function purchases($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'account'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_purchase_table();

			if ($param1 == 'add') $this->model->add_purchase();
			elseif ($param1 == 'update') $this->model->update_purchase($param2);
			elseif ($param1 == 'remove') $this->model->remove_purchase($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Purchases';
			$page_data['page_name'] 	=	'purchases';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function daily_cash_book($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'account'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_daily_cash_book_table();

			if ($param1 == 'add') {
				$this->model->add_daily_cash_book_entry();
			} elseif ($param1 == 'remove') {
				$this->model->remove_daily_cash_book_entry($param2);
			}

			$selected_date = $this->input->get('date');
			if (!$selected_date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
				$selected_date = date('Y-m-d');
			}

			$selected_year = (int) $this->input->get('year');
			if (!$selected_year) {
				$selected_year = (int) date('Y', strtotime($selected_date));
			}

			$page_data['selected_date'] = $selected_date;
			$page_data['selected_year'] = $selected_year;
			$page_data['cash_book_daily_totals'] = $this->model->get_daily_cash_book_totals($selected_date);
			$page_data['cash_book_entries'] = $this->model->get_daily_cash_book_entries($selected_date);
			$page_data['cash_book_monthly_summary'] = $this->model->get_daily_cash_book_monthly_summary($selected_year);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Daily Cash Book';
			$page_data['page_name'] 	=	'daily_cash_book';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function single_year_account($year = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'account'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['year']			=	$year;

			$page_data['page_title']	=	'Single Year Account';
			$page_data['page_name'] 	=	'single_year_account';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function lease_monitor()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'lease_monitor'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['page_title']	=	'Plan Expiry Monitor';
			$page_data['page_name'] 	=	'lease_monitor';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function board_members()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		$page_data['page_title']	=	'Board Members';
		$page_data['page_name'] 	=	'board_members';
		$this->load->view('index', $page_data);
	}

	function website_settings($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
		if ($param == 'update') $this->model->update_website_settings();
		if ($param == 'update_favicon') $this->model->update_website_favicon();
		if ($param == 'login_bg') $this->model->update_website_login_bg();
		if ($param == 'update_smtp') $this->model->update_website_smtp();
		if ($param == 'delete_smtp') $this->model->delete_website_smtp();
		if ($param == 'update_twilio') $this->model->update_website_twilio();
		if ($param == 'delete_twilio') $this->model->delete_website_twilio();
		if ($param == 'update_gst_certificate') $this->model->update_website_gst_certificate();

		$page_data['page_title']	=	'Website Settings';
		$page_data['page_name'] 	=	'website_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function library_plan_settings($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			$this->model->ensure_library_plan_schema();
			if ($param == 'update') $this->model->update_library_plan_settings();

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Library Plan Settings';
			$page_data['page_name'] 	=	'library_plan_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function owner_agreement_settings($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param == 'update') $this->model->update_owner_agreement_settings();

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Owner Agreement Settings';
			$page_data['page_name'] 	=	'owner_agreement_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function tenant_agreement_settings($param = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param == 'update') $this->model->update_tenant_agreement_settings();

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Student Agreement Template';
			$page_data['page_name'] 	=	'tenant_agreement_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function download_tenant_agreement($tenant_id = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		$page_data['tenant_id'] = $tenant_id;
		$this->load->view('generate_tenant_agreement_print', $page_data);
	}

	function upload_signed_agreement($tenant_id = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		$this->model->upload_signed_agreement($tenant_id);
	}

	function profession_settings($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_profession();
			elseif ($param1 == 'update') $this->model->update_profession($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Profession Settings';
			$page_data['page_name'] 	=	'profession_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function service_settings($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_service();
			elseif ($param1 == 'update') $this->model->update_service($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Service Settings';
			$page_data['page_name'] 	=	'service_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function id_type_settings($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_id_type();
			elseif ($param1 == 'update') $this->model->update_id_type($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'ID Type Settings';
			$page_data['page_name'] 	=	'id_type_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function payment_method_settings($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_payment_method();
			elseif ($param1 == 'update') $this->model->update_payment_method($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Payment Method Settings';
			$page_data['page_name'] 	=	'payment_method_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function board_member_settings($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) {
			if ($param1 == 'add') $this->model->add_board_member();
			elseif ($param1 == 'update') $this->model->update_board_member($param2);

			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Board Member Settings';
			$page_data['page_name'] 	=	'board_member_settings';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function profile_settings($param1 = '', $param2 = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if ($param1 == 'update') $this->model->update_profile_settings($param2);

		$page_data['page_title']	=	'Profile Settings';
		$page_data['page_name'] 	=	'profile_settings';
		$this->load->view('index', $page_data);
	}

	function rents_report()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'reports'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Plan Revenue Report';
			$page_data['page_name'] 	=	'rents_report';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function single_year_rents_report($year = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'reports'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['year']			=	$year;

			$page_data['page_title']	=	'Single Year Plan Revenue Report';
			$page_data['page_name'] 	=	'single_year_rents_report';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function download_rents_report($year = '')
	{
		$page_data['year']	=	$year;
		$this->load->view('rents_report_to_excel', $page_data);
	}

	function expenses_report()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'reports'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Expenses Report';
			$page_data['page_name'] 	=	'expenses_report';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function single_year_expenses_report($year = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'reports'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['year']			=	$year;

			$page_data['page_title']	=	'Single Year Expenses Report';
			$page_data['page_name'] 	=	'single_year_expenses_report';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function download_expenses_report($year = '')
	{
		$page_data['year']	=	$year;
		$this->load->view('expenses_report_to_excel', $page_data);
	}

	function utilities_report()
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'reports'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['navbar_status']	=	'aside-collapsed';
			$page_data['page_title']	=	'Utilities Report';
			$page_data['page_name'] 	=	'utilities_report';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function single_year_utilities_report($year = '')
	{
		if (!$this->session->userdata('user_type'))
			redirect(base_url() . 'login', 'refresh');

		if (in_array($this->db->get_where('module', array('module_name' => 'reports'))->row()->module_id, $this->session->userdata('permissions'))) {
			$page_data['year']			=	$year;

			$page_data['page_title']	=	'Single Year Utilities Report';
			$page_data['page_name'] 	=	'single_year_utilities_report';
			$this->load->view('index', $page_data);
		} else {
			$page_data['page_title']	=	'Permission Denied';
			$page_data['page_name'] 	= 	'permission_denied';
			$this->load->view('index', $page_data);
		}
	}

	function download_utilities_report($year = '')
	{
		$page_data['year']	=	$year;
		$this->load->view('utilities_report_to_excel', $page_data);
	}
}
