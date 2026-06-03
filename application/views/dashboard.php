<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item active"><?php echo $this->lang->line('dashboard'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><?php echo $this->lang->line('welcome_to'); ?> <?php echo $this->db->get_where('setting', array('name' => 'system_name'))->row()->content; ?> <small><?php echo date('d') . ' ' . $this->lang->line(strtolower(date('F'))) . ', ' . date('Y'); ?></small></h1>
    <!-- end page-header -->

    <?php if (in_array($this->db->get_where('module', array('module_name' => 'tenants'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
    <!-- begin admission-cta -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body text-center" style="padding: 30px 20px;">
                    <h2 class="m-t-0 m-b-15" style="font-weight: 600;">
                        <i class="fa fa-user-plus"></i> New Admission
                    </h2>
                    <p class="m-b-20" style="font-size: 15px; opacity: 0.85;">
                        Onboard a new student, assign a seat, shift, and study plan in a single step.
                    </p>
                    <a href="<?php echo base_url(); ?>add_tenant" class="btn btn-success btn-lg" style="font-size: 18px; padding: 12px 40px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        <i class="fa fa-plus-circle"></i> Add Admission
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- end admission-cta -->
    <?php endif; ?>

    <!-- begin row -->
    <div class="row">
        <?php
            // --- Seat KPIs (computed once, used by all 3 cards) ---
            $total_seats = (int)$this->db->get('room')->num_rows();
            // "Occupied" = distinct rooms that have at least one active tenant (status=1)
            $this->db->select('COUNT(DISTINCT room_id) AS occupied');
            $this->db->from('tenant');
            $this->db->where('status', 1);
            $this->db->where('room_id IS NOT NULL', null, false);
            $occupied_seats = (int)($this->db->get()->row()->occupied ?? 0);
            // Clamp to total in case of stale data
            if ($occupied_seats > $total_seats) $occupied_seats = $total_seats;
            $available_seats = $total_seats - $occupied_seats;
        ?>
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-building"></i></div>
                <div class="stats-info">
                    <h4><b>Total Seats</b></h4>
                    <p><?php echo html_escape($total_seats); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>rooms"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-orange">
                <div class="stats-icon"><i class="fa fa-building"></i></div>
                <div class="stats-info">
                    <h4><b>Occupied Seats</b></h4>
                    <p><?php echo html_escape($occupied_seats); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>occupied_rooms"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-green">
                <div class="stats-icon"><i class="fa fa-building"></i></div>
                <div class="stats-info">
                    <h4><b>Available Seats</b></h4>
                    <p><?php echo html_escape($available_seats); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>unoccupied_rooms"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-user"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('total_staff'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get('staff')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>staff"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-users"></i></div>
                <div class="stats-info">
                    <h4><b>Total Students</b></h4>
                    <p><?php echo html_escape($this->db->get('tenant')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>tenants"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-users"></i></div>
                <div class="stats-info">
                    <h4><b>Inactive Students</b></h4>
                    <p><?php echo html_escape($this->db->get_where('tenant', array('status' => 0))->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>inactive_tenants"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-users"></i></div>
                <div class="stats-info">
                    <h4><b>Active Students</b></h4>
                    <p><?php echo html_escape($this->db->get_where('tenant', array('status' => 1))->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>active_tenants"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-podcast"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('total_notices'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get('notice')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>notices"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="far fa-credit-card"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('total_invoices'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get('invoice')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>invoices"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="far fa-credit-card"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('unpaid_invoices'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get_where('invoice', array('status' => 0))->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>unpaid_invoices"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="far fa-credit-card"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('paid_invoices'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get_where('invoice', array('status' => 1))->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>paid_invoices"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-money-bill-alt"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('total_utility_bills'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get('utility_bill')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>utility_bills"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-life-ring"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('total_complaints'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get('complaint')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>complaints"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-life-ring"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('open_complaints'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get_where('complaint', array('status' => 0))->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>open_complaints"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-life-ring"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('closed_complaints'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get_where('complaint', array('status' => 1))->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>closed_complaints"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fas fa-credit-card"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('total_expenses'); ?></b></h4>
                    <p><?php echo html_escape($this->db->get('expense')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>expenses"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->

        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b>Due Plan Revenue Of <?php echo $this->lang->line(strtolower(date('F'))) . ', ' . date('Y'); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('tenant_rent');
                    $this->db->where('status', 0);
                    $this->db->where('month', date('F'));
                    $this->db->where('year', date('Y'));
                    $query = $this->db->get();

                    $due_amount = $query->row()->amount;

                    echo number_format(round($due_amount > 0 ? $due_amount : 0));
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b><?php echo $this->lang->line('total_rents_of'); ?> <?php echo $this->lang->line(strtolower(date('F'))) . ', ' . date('Y'); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('tenant_rent');
                    $this->db->where('month', date('F'));
                    $this->db->where('year', date('Y'));
                    $query = $this->db->get();

                    $total_amount = $query->row()->amount;

                    echo number_format(round($total_amount > 0 ? $total_amount : 0));
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b><?php echo $this->lang->line('due_rents_of'); ?> <?php echo $this->lang->line(strtolower(date('F', strtotime("-1 months")))) . ', ' . date('Y', strtotime("-1 months")); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('tenant_rent');
                    $this->db->where('status', 0);
                    $this->db->where('month', date('F', strtotime("-1 months")));
                    $this->db->where('year', date('Y'));
                    $query = $this->db->get();

                    $last_due_amount = $query->row()->amount;

                    echo number_format(round($last_due_amount > 0 ? $last_due_amount : 0));
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b><?php echo $this->lang->line('total_rents_of'); ?> <?php echo $this->lang->line(strtolower(date('F', strtotime("-1 months")))) . ', ' . date('Y', strtotime("-1 months")); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('tenant_rent');
                    $this->db->where('month', date('F', strtotime("-1 months")));
                    $this->db->where('year', date('Y'));
                    $query = $this->db->get();

                    $last_total_amount = $query->row()->amount;

                    echo number_format(round($last_total_amount > 0 ? $last_total_amount : 0));
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b><?php echo $this->lang->line('total_utility_bills_overall'); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('utility_bill');
                    $query = $this->db->get();

                    $overall_utility_bill = $query->row()->amount;

                    if ($overall_utility_bill > 1000000) {
                        echo number_format(round($overall_utility_bill / 1000000)) . ' M';
                    } else {
                        echo number_format(round($overall_utility_bill  > 0 ? $overall_utility_bill : 0));
                    }
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b><?php echo $this->lang->line('total_expenses_overall'); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('expense');
                    $query = $this->db->get();

                    $overall_expense = $query->row()->amount;

                    if ($overall_expense > 1000000) {
                        echo number_format(round($overall_expense / 1000000)) . ' M';
                    } else {
                        echo number_format(round($overall_expense > 0 ? $overall_expense : 0));
                    }
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b><?php echo $this->lang->line('total_due_rents_overall'); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('tenant_rent');
                    $this->db->where('status', 0);
                    $query = $this->db->get();

                    $overall_due_amount = $query->row()->amount;

                    echo number_format(round($overall_due_amount > 0 ? $overall_due_amount : 0));
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="note note-light m-b-15">
                <h5><b><?php echo $this->lang->line('total_rents_overall'); ?></b></h5>
                <p>
                    <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                    <?php
                    $this->db->select_sum('amount');
                    $this->db->from('tenant_rent');
                    $query = $this->db->get();

                    $overall_amount = $query->row()->amount;

                    echo number_format(round($overall_amount > 0 ? $overall_amount : 0));
                    ?>
                </p>
            </div>
        </div>
        <!-- end col-3 -->
    </div>
    <!-- end row -->
    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-heading -->
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-users"></i> Active Students
                        <span class="badge badge-primary" style="margin-left: 8px; background: #5c6bc0;">
                            <?php
                            $active_tenants_count = $this->db->get_where('tenant', array('status' => 1))->num_rows();
                            echo (int) $active_tenants_count;
                            ?>
                        </span>
                    </h4>
                </div>
                <!-- end panel-heading -->
                <!-- begin panel-body -->
                <div class="panel-body">
                    <div class="row" style="margin-bottom: 14px;">
                        <div class="col-md-6">
                            <div class="input-group" style="max-width: 480px;">
                                <span class="input-group-addon" style="background: #f3f4f6; border-color: #d1d5db;">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="text" id="dashboard-active-search" class="form-control"
                                       placeholder="Search by name, mobile, or seat number"
                                       autocomplete="off"
                                       style="border-left: 0; box-shadow: none;" />
                                <span class="input-group-addon" id="dashboard-active-search-clear"
                                      style="background: #f3f4f6; border-color: #d1d5db; cursor: pointer; color: #6b7280;"
                                      title="Clear search">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <small class="text-muted" id="dashboard-active-search-status" style="display: block; margin-top: 6px;">
                                Tip: type a name, phone number, or seat like "A1" to filter.
                            </small>
                        </div>
                    </div>
                    <?php
                    $dashboard_active_tenants = $this->db
                        ->order_by('timestamp', 'desc')
                        ->get_where('tenant', array('status' => 1))
                        ->result_array();
                    if (empty($dashboard_active_tenants)):
                    ?>
                        <div class="alert alert-info text-center m-b-0">No active students yet. Use <strong>Add Admission</strong> to enroll a new student.</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table id="dashboard-active-students-tbl" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="1%">#</th>
                                    <th width="60">Photo</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Seat</th>
                                    <th>Shifts</th>
                                    <th>Plan</th>
                                    <th>Lease End</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $d_count = 1;
                                $d_plan_labels = array(
                                    'per_day'     => 'Per Day',
                                    'monthly'     => 'Monthly',
                                    'quarterly'   => '3 Month',
                                    'half_yearly' => '6 Month',
                                    'yearly'      => '12 Month'
                                );
                                foreach ($dashboard_active_tenants as $d_tenant):
                                    $d_room = $d_tenant['room_id'] ? $this->db->get_where('room', array('room_id' => $d_tenant['room_id']))->row() : null;
                                    $d_shifts = $this->model->get_tenant_shifts($d_tenant['tenant_id']);
                                    if (empty($d_shifts) && !empty($d_tenant['shift_id'])) {
                                        $legacy = $this->db->get_where('study_shift', array('shift_id' => $d_tenant['shift_id']))->row_array();
                                        if ($legacy) {
                                            $d_shifts = array($legacy);
                                        }
                                    }
                                    $d_shift_names = array();
                                    foreach ($d_shifts as $d_sh) {
                                        $d_shift_names[] = $d_sh['shift_name'];
                                    }
                                    $d_lease_end = $d_tenant['lease_end'] ? (int) $d_tenant['lease_end'] : 0;
                                    $d_lease_class = '';
                                    if ($d_lease_end > 0 && $d_lease_end < time()) {
                                        $d_lease_class = 'text-danger';
                                    } elseif ($d_lease_end > 0 && $d_lease_end < strtotime('+7 days')) {
                                        $d_lease_class = 'text-warning';
                                    }
                                ?>
                                    <tr class="dashboard-student-row">
                                        <td><?php echo $d_count++; ?></td>
                                        <td>
                                            <?php if (!empty($d_tenant['image_link'])): ?>
                                                <img class="img-rounded height-30" src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($d_tenant['image_link']); ?>" alt="<?php echo html_escape($d_tenant['name']); ?>" onerror="this.onerror=null;this.src='<?php echo base_url(); ?>assets/img/tenant.png';" />
                                            <?php else: ?>
                                                <img class="img-rounded height-30" src="<?php echo base_url(); ?>assets/img/tenant.png" alt="No photo" />
                                            <?php endif; ?>
                                        </td>
                                        <td class="ds-name"><strong><?php echo html_escape($d_tenant['name']); ?></strong></td>
                                        <td class="ds-mobile"><?php echo $d_tenant['mobile_number'] ? html_escape($d_tenant['mobile_number']) : ''; ?></td>
                                        <td class="ds-seat">
                                            <?php if ($d_room): ?>
                                                <?php echo html_escape($d_room->roomnumber . ' / ' . $d_room->room_number); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($d_shift_names)): ?>
                                                <?php foreach ($d_shift_names as $d_sn): ?>
                                                    <span class="badge badge-info" style="background: #5c6bc0; margin-right: 3px;"><?php echo html_escape($d_sn); ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo !empty($d_tenant['plan_type']) && isset($d_plan_labels[$d_tenant['plan_type']])
                                                ? html_escape($d_plan_labels[$d_tenant['plan_type']])
                                                : ''; ?>
                                        </td>
                                        <td class="<?php echo $d_lease_class; ?>">
                                            <?php echo $d_lease_end ? date('d M, Y', $d_lease_end) : ''; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-xs" onclick="showStudentProfile(<?php echo (int) $d_tenant['tenant_id']; ?>);">
                                                <i class="fa fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div id="dashboard-active-students-empty" class="alert alert-warning text-center" style="display: none; margin-top: 10px;">
                            <i class="fa fa-search"></i> No students match "<span id="dashboard-active-students-empty-term"></span>".
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row -->

    <!-- Student profile modal (wide) -->
    <div class="modal fade" id="modal_student_profile" tabindex="-1" role="dialog" aria-labelledby="modal_student_profile_title">
        <div class="modal-dialog modal-lg" role="document" style="width: 92%; max-width: 920px;">
            <div class="modal-content" id="modal_student_profile_content">
                <div class="modal-body" id="modal_student_profile_body" style="padding: 0;">
                    <div class="text-center" style="padding: 60px 20px;">
                        <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showStudentProfile(tenantId) {
            var $modal = jQuery('#modal_student_profile');
            $modal.modal('show', { backdrop: true });
            jQuery('#modal_student_profile_body').html(
                '<div class="text-center" style="padding: 80px 20px;"><i class="fa fa-spinner fa-spin fa-3x text-muted"></i><div style="margin-top: 12px; color: #6b7280;">Loading profile...</div></div>'
            );
            jQuery.ajax({
                url: '<?php echo base_url(); ?>modal/popup/modal_student_profile/' + tenantId,
                success: function (response) {
                    jQuery('#modal_student_profile_body').html(response);
                },
                error: function () {
                    jQuery('#modal_student_profile_body').html(
                        '<div class="alert alert-danger" style="margin: 22px;">Failed to load profile. Please try again.</div>'
                    );
                }
            });
        }
    </script>

    <script>
        (function () {
            var input  = document.getElementById('dashboard-active-search');
            var table  = document.getElementById('dashboard-active-students-tbl');
            var status = document.getElementById('dashboard-active-search-status');
            var empty  = document.getElementById('dashboard-active-students-empty');
            var term   = document.getElementById('dashboard-active-students-empty-term');
            var clear  = document.getElementById('dashboard-active-search-clear');

            if (!input || !table) { return; }

            var rows = table.querySelectorAll('tbody tr.dashboard-student-row');
            if (rows.length === 0) { return; }
            var totalRows = rows.length;

            function escHtml(s) {
                return String(s).replace(/[&<>"']/g, function (c) {
                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
                });
            }

            function getCellText(row, cls) {
                var cell = row.querySelector('.' + cls);
                return cell ? cell.textContent.toLowerCase() : '';
            }

            function applyFilter() {
                var raw = input.value || '';
                var q = raw.toLowerCase().trim();
                var visible = 0;

                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    var name   = getCellText(row, 'ds-name');
                    var mobile = getCellText(row, 'ds-mobile');
                    var seat   = getCellText(row, 'ds-seat');

                    var match = !q ||
                                name.indexOf(q)   !== -1 ||
                                mobile.indexOf(q) !== -1 ||
                                seat.indexOf(q)   !== -1;

                    if (match) {
                        row.style.display = '';
                        visible++;
                    } else {
                        row.style.display = 'none';
                    }
                }

                if (q) {
                    if (visible === 0) {
                        empty.style.display = 'block';
                        term.innerHTML = escHtml(raw);
                        status.innerHTML = 'No matches for &quot;<strong>' + escHtml(raw) + '</strong>&quot;';
                    } else {
                        empty.style.display = 'none';
                        status.innerHTML = 'Showing <strong>' + visible + '</strong> of <strong>' + totalRows +
                            '</strong> student' + (totalRows === 1 ? '' : 's') +
                            ' matching &quot;<strong>' + escHtml(raw) + '</strong>&quot;';
                    }
                } else {
                    empty.style.display = 'none';
                    status.innerHTML = 'Tip: type a name, phone number, or seat like &quot;A1&quot; to filter.';
                }
            }

            input.addEventListener('keyup', applyFilter);
            input.addEventListener('input', applyFilter);
            input.addEventListener('change', applyFilter);

            if (clear) {
                clear.addEventListener('click', function () {
                    input.value = '';
                    input.focus();
                    applyFilter();
                });
            }
        })();
    </script>
</div>
<!-- end #content -->
