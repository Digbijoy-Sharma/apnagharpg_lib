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

    <!-- begin row -->
    <div class="row">
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-building"></i></div>
                <div class="stats-info">
                    <h4><b>Total Seats</b></h4>
                    <p><?php echo html_escape($this->db->get('room')->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>rooms"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
        <!-- begin col-3 -->
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-building"></i></div>
                <div class="stats-info">
                    <h4><b>Available Seats</b></h4>
                    <p><?php echo html_escape($this->db->get_where('room', array('status' => 0))->num_rows()); ?></p>
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
                <div class="stats-icon"><i class="fa fa-building"></i></div>
                <div class="stats-info">
                    <h4><b>Occupied Seats</b></h4>
                    <p><?php echo html_escape($this->db->get_where('room', array('status' => 1))->num_rows()); ?></p>
                </div>
                <div class="stats-link">
                    <a href="<?php echo base_url(); ?>occupied_rooms"><?php echo $this->lang->line('view_details'); ?> <i class="fa fa-arrow-alt-circle-right"></i></a>
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
</div>
<!-- end #content -->
