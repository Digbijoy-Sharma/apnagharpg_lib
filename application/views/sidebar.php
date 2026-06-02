<?php $page_name = isset($page_name) ? $page_name : ''; ?>
<?php $header_tenant_image = isset($header_tenant_image) ? $header_tenant_image : ''; ?>
<!-- begin #sidebar -->
<div id="sidebar" class="sidebar" data-disable-slide-animation="true">
    <!-- begin sidebar scrollbar -->
    <div data-scrollbar="true" data-height="100%">
        <!-- begin sidebar user -->
        <ul class="nav">
            <li class="nav-profile">
                <div class="text-center">
                    <div class="cover with-shadow"></div>
                    <div class="image">
                        <?php
                        $sidebar_user_type =  $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->user_type;
                        if ($sidebar_user_type == 1) {
                            echo '<img src="' . base_url() . 'uploads/website/' . $this->db->get_where('setting', array('name' => 'favicon'))->row()->content . '" alt="Mars Room Management System"' . '/>';
                        } else if ($sidebar_user_type == 2) {
                            echo '<img src="' . base_url() . 'uploads/website/' . $this->db->get_where('setting', array('name' => 'favicon'))->row()->content . '" alt="Mars Room Management System"' . '/>';
                        } else {
                            $sidebar_person_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                            $sidebar_tenant_image = $this->db->get_where('tenant', array('tenant_id' => $sidebar_person_id))->row()->image_link;

                            if ($header_tenant_image)
                                echo '<img src="' . base_url() . 'uploads/tenants/' . $header_tenant_image . '" alt="Mars Room Management System"' . '/>';
                            else
                                echo '<img src="' . base_url() . 'uploads/website/' . $this->db->get_where('setting', array('name' => 'favicon'))->row()->content . '" alt="Mars Room Management System"' . '/>';
                        }

                        ?>
                    </div>
                    <div class="info">
                        <?php
                        if ($sidebar_user_type == 1) {
                            echo $this->lang->line('admin');
                        } else if ($sidebar_user_type == 2) {
                            $sidebar_person_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                            echo html_escape($this->db->get_where('staff', array('staff_id' => $sidebar_person_id))->row()->name);
                        } else {
                            $sidebar_person_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                            echo html_escape($this->db->get_where('tenant', array('tenant_id' => $sidebar_person_id))->row()->name);
                        }
                        ?>
                        <small>
                            <?php
                            if ($sidebar_user_type == 1) {
                                echo $this->lang->line('admin');
                            } else if ($sidebar_user_type == 2) {
                                $sidebar_person_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                                if ($this->db->get_where('staff', array('staff_id' => $sidebar_person_id))->row()->role)
                                    echo html_escape($this->db->get_where('staff', array('staff_id' => $sidebar_person_id))->row()->role);
                                else
                                echo $this->lang->line('staff');
                            } else {
                                $sidebar_person_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                                $profession_id = $this->db->get_where('tenant', array('tenant_id' => $sidebar_person_id))->row()->profession_id;
                                if ($profession_id)
                                    echo html_escape($this->db->get_where('profession', array('profession_id' => $profession_id))->row()->name);
                                else
                                echo 'Student';
                            }
                            ?>
                        </small>
                    </div>
                </div>
            </li>
        </ul>
        <!-- end sidebar user -->
        <!-- begin sidebar nav -->
        <ul class="nav">
            <li class="nav-header"><?php echo $this->lang->line('navigation'); ?></li>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'dashboard'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="<?php if ($page_name == 'dashboard') echo 'active'; ?>">
                    <a href="<?php echo base_url(); ?>">
                        <i class="fa fa-home"></i>
                        <span><?php echo $this->lang->line('dashboard'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php
            if (!in_array($this->db->get_where('module', array(
                'module_name' => 'generate_invoice'
            ))->row()->module_id, $this->session->userdata('permissions')) && !in_array($this->db->get_where('module', array(
                'module_name' => 'Invoices'
            ))->row()->module_id, $this->session->userdata('permissions'))) :
            ?>

            <?php else : ?>
                <li class="has-sub <?php if ($page_name == 'generate_invoice' || $page_name == 'monthly_invoices' || $page_name == 'single_month_invoices' || $page_name == 'tenant_invoices' || $page_name == 'single_tenant_invoices' || $page_name == 'invoices' || $page_name == 'paid_invoices' || $page_name == 'unpaid_invoices' || $page_name == 'invoice') echo 'active'; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="far fa-credit-card"></i>
                        <span><?php echo $this->lang->line('invoices'); ?></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (in_array($this->db->get_where('module', array('module_name' => 'generate_invoice'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                            <li class="<?php if ($page_name == 'generate_invoice') echo 'active'; ?>">
                                <a href="<?php echo base_url(); ?>generate_invoice"><?php echo $this->lang->line('generate_invoice'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (in_array($this->db->get_where('module', array('module_name' => 'invoices'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                            <li class="<?php if ($page_name == 'monthly_invoices' || $page_name == 'single_month_invoices') echo 'active'; ?>">
                                <a href="<?php echo base_url(); ?>monthly_invoices"><?php echo $this->lang->line('monthly_invoices'); ?></a>
                            </li>
                            <?php if ($this->session->userdata('user_type') != 3 && (count($this->db->get('tenant')->result_array()) > 0)) : ?>
                                <li class="<?php if ($page_name == 'tenant_invoices' || $page_name == 'single_tenant_invoices') echo 'active'; ?>">
                                    <a href="<?php echo base_url(); ?>tenant_invoices">Student Invoices</a>
                                </li>
                            <?php endif; ?>
                            <li class="<?php if ($page_name == 'invoices' || $page_name == 'paid_invoices' || $page_name == 'unpaid_invoices' || $page_name == 'invoice') echo 'active'; ?>">
                                <a href="<?php echo base_url(); ?>invoices"><?php echo $this->lang->line('all_invoices'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'rooms'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="has-sub <?php if ($page_name == 'add_room' || $page_name == 'rooms' || $page_name == 'occupied_rooms' || $page_name == 'unoccupied_rooms' || $page_name == 'seat_occupancy_chart') echo 'active'; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-building"></i>
                        <span>Seats</span>
                    </a>
                    <ul class="sub-menu">
                        <li class="<?php if ($page_name == 'rooms' || $page_name == 'add_room') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>rooms">All Seats</a>
                        </li>
                        <li class="<?php if ($page_name == 'occupied_rooms') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>occupied_rooms">Occupied Seats</a>
                        </li>
                        <li class="<?php if ($page_name == 'unoccupied_rooms') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>unoccupied_rooms">Available Seats</a>
                        </li>
                        <li class="<?php if ($page_name == 'seat_occupancy_chart') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>seat_occupancy_chart">Occupancy Chart</a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'tenants'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="<?php if ($page_name == 'add_tenant' || $page_name == 'tenants' || $page_name == 'active_tenants' || $page_name == 'inactive_tenants') echo 'active'; ?>">
                    <a href="<?php echo base_url(); ?>tenants">
                        <i class="fa fa-users"></i>
                        <span>Students</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'utility_bills'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="has-sub <?php if ($page_name == 'add_utility_bill' || $page_name == 'utility_bills' || $page_name == 'utility_bill_categories') echo 'active'; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-money-bill-alt"></i>
                        <span><?php echo $this->lang->line('utility_bills'); ?></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="<?php if ($page_name == 'add_utility_bill' || $page_name == 'utility_bills') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>utility_bills"><?php echo $this->lang->line('utility_bill'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'utility_bill_categories') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>utility_bill_categories"><?php echo $this->lang->line('utility_bill_category'); ?></a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'complaints'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="<?php if ($page_name == 'complaints' || $page_name == 'add_complaint' || $page_name == 'closed_complaints' || $page_name == 'open_complaints') echo 'active'; ?>">
                    <a href="<?php echo base_url(); ?>complaints">
                        <i class="fa fa-life-ring"></i>
                        <span><?php echo $this->lang->line('complaints'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'notices'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="<?php if ($page_name == 'notices' || $page_name == 'add_notice') echo 'active'; ?>">
                    <a href="<?php echo base_url(); ?>notices">
                        <i class="fa fa-podcast"></i>
                        <span><?php echo $this->lang->line('notices'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'reports'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="has-sub <?php if ($page_name == 'rents_report' || $page_name == 'single_year_rents_report' || $page_name == 'expenses_report' || $page_name == 'single_year_expenses_report' || $page_name == 'utilities_report' || $page_name == 'single_year_utilities_report') echo 'active'; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-file-excel"></i>
                        <span><?php echo $this->lang->line('reports'); ?></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="<?php if ($page_name == 'rents_report' || $page_name == 'single_year_rents_report') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>rents_report">Plan Revenue</a>
                        </li>
                        <li class="<?php if ($page_name == 'expenses_report' || $page_name == 'single_year_expenses_report') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>expenses_report"><?php echo $this->lang->line('expenses'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'utilities_report' || $page_name == 'single_year_utilities_report') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>utilities_report"><?php echo $this->lang->line('utilities'); ?></a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'account'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="has-sub <?php if ($page_name == 'account' || $page_name == 'single_year_account' || $page_name == 'bank_statements' || $page_name == 'daily_cash_book' || $page_name == 'purchases') echo 'active'; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-list-ol"></i>
                        <span><?php echo $this->lang->line('account'); ?></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="<?php if ($page_name == 'account' || $page_name == 'single_year_account') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>account"><?php echo $this->lang->line('account'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'bank_statements') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>bank_statements">Bank Statement</a>
                        </li>
                        <li class="<?php if ($page_name == 'daily_cash_book') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>daily_cash_book">Daily Cash Book</a>
                        </li>
                        <li class="<?php if ($page_name == 'purchases') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>purchases">Purchases</a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'expenses'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="<?php if ($page_name == 'add_expense' || $page_name == 'expenses') echo 'active'; ?>">
                    <a href="<?php echo base_url(); ?>expenses">
                        <i class="fas fa-credit-card"></i>
                        <span><?php echo $this->lang->line('expenses'); ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php
            if (!in_array($this->db->get_where('module', array(
                'module_name' => 'staff'
            ))->row()->module_id, $this->session->userdata('permissions')) && !in_array($this->db->get_where('module', array(
                'module_name' => 'staff_payroll'
            ))->row()->module_id, $this->session->userdata('permissions'))) :
            ?>

            <?php else : ?>
                <li class="has-sub <?php if ($page_name == 'add_staff' || $page_name == 'staff' || $page_name == 'add_staff_payroll' || $page_name == 'staff_payroll' || $page_name == 'single_month_staff_payroll') echo 'active'; ?>">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-user"></i>
                        <span><?php echo $this->lang->line('staff'); ?></span>
                    </a>
                    <ul class="sub-menu">
                        <?php if (in_array($this->db->get_where('module', array('module_name' => 'staff'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                            <li class="<?php if ($page_name == 'add_staff' || $page_name == 'staff') echo 'active'; ?>">
                                <a href="<?php echo base_url(); ?>staff"><?php echo $this->lang->line('staff'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (in_array($this->db->get_where('module', array('module_name' => 'staff_payroll'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                            <li class="<?php if ($page_name == 'add_staff_payroll') echo 'active'; ?>">
                                <a href="<?php echo base_url(); ?>add_staff_payroll"><?php echo $this->lang->line('add_staff_payroll'); ?></a>
                            </li>
                            <li class="<?php if ($page_name == 'staff_payroll' || $page_name == 'single_month_staff_payroll') echo 'active'; ?>">
                                <a href="<?php echo base_url(); ?>staff_payroll"><?php echo $this->lang->line('staff_payroll'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <li class="<?php if ($page_name == 'board_members') echo 'active'; ?>">
                <a href="<?php echo base_url(); ?>board_members">
                    <i class="fa fa-user-circle"></i>
                    <span><?php echo $this->lang->line('board_members'); ?></span>
                </a>
            </li>
            <?php if (in_array($this->db->get_where('module', array('module_name' => 'lease_monitor'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                <li class="<?php if ($page_name == 'lease_monitor') echo 'active'; ?>">
                    <a href="<?php echo base_url(); ?>lease_monitor">
                        <i class="fa fa-desktop"></i>
                        <span>Plan Expiry Monitor</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="has-sub <?php if ($page_name == 'board_member_settings' || $page_name == 'payment_method_settings' || $page_name == 'service_settings' || $page_name == 'website_settings' || $page_name == 'profession_settings' || $page_name == 'id_type_settings' || $page_name == 'profile_settings' || $page_name == 'owner_agreement_settings' || $page_name == 'tenant_agreement_settings' || $page_name == 'library_plan_settings') echo 'active'; ?>">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-cog"></i>
                    <span><?php echo $this->lang->line('settings'); ?></span>
                </a>
                <ul class="sub-menu">
                    <?php if (in_array($this->db->get_where('module', array('module_name' => 'settings'))->row()->module_id, $this->session->userdata('permissions'))) : ?>
                            <li class="<?php if ($page_name == 'library_plan_settings') echo 'active'; ?>">
                                <a href="<?php echo base_url(); ?>library_plan_settings">Library Plans</a>
                            </li>
                        <li class="<?php if ($page_name == 'website_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>website_settings"><?php echo $this->lang->line('website'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'profession_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>profession_settings"><?php echo $this->lang->line('profession'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'service_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>service_settings"><?php echo $this->lang->line('service'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'id_type_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>id_type_settings"><?php echo $this->lang->line('id_type'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'payment_method_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>payment_method_settings"><?php echo $this->lang->line('payment_method'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'board_member_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>board_member_settings"><?php echo $this->lang->line('board_member'); ?></a>
                        </li>
                        <li class="<?php if ($page_name == 'owner_agreement_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>owner_agreement_settings">Owner Agreement</a>
                        </li>
                        <li class="<?php if ($page_name == 'tenant_agreement_settings') echo 'active'; ?>">
                            <a href="<?php echo base_url(); ?>tenant_agreement_settings">Student Agreement</a>
                        </li>
                    <?php endif; ?>
                    <li class="<?php if ($page_name == 'profile_settings') echo 'active'; ?>">
                        <a href="<?php echo base_url(); ?>profile_settings"><?php echo $this->lang->line('profile'); ?></a>
                    </li>
                </ul>
            </li>
            <li class="">
                <a href="https://gitcservices.com" target="_blank">
                    <i class="fa fa-question-circle"></i>
                    <span><?php echo $this->lang->line('support'); ?></span>
                </a>
            </li>
        </ul>
        <!-- end sidebar nav -->
    </div>
    <!-- end sidebar scrollbar -->
</div>
<div class="sidebar-bg"></div>
<!-- end #sidebar -->
