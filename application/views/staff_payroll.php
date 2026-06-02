<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('staff_payroll'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('staff_payroll_header'); ?> <?php echo $this->lang->line(strtolower(date('F'))) . ', ' .  date('Y'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-9 -->
        <div class="col-lg-9">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <table id="data-table-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="1%">#</th>
                                <th class="text-nowrap"><?php echo $this->lang->line('name'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('month'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('year'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('amount'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('status'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('created_on'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('created_by'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_on'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_by'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('options'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            $staff_payroll = $this->db->get_where('staff_salary', array('year' => date('Y'), 'month' => date('F')))->result_array();
                            foreach ($staff_payroll as $row) :
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo html_escape($this->db->get_where('staff', array('staff_id' => $row['staff_id']))->row()->name); ?></td>
                                    <td><?php echo html_escape($this->lang->line(strtolower($row['month']))); ?></td>
                                    <td><?php echo html_escape($row['year']); ?></td>
                                    <td>
                                        <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                                        <?php echo html_escape(number_format($row['amount'])); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($row['status'] == 0)
                                            echo '<span class="badge badge-warning">' . $this->lang->line('due') . '</span>';
                                        else
                                            echo '<span class="badge badge-primary">' . $this->lang->line('paid') . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo date('d M, Y', $row['created_on']); ?></td>
                                    <td>
                                        <?php
                                        $user_type =  $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->user_type;
                                        if ($user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $person_id = $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->person_id;
                                            echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('d M, Y', $row['timestamp']); ?></td>
                                    <td>
                                        <?php
                                        $user_type =  $this->db->get_where('user', array('user_id' => $row['updated_by']))->row()->user_type;
                                        if ($user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $person_id = $this->db->get_where('user', array('user_id' => $row['updated_by']))->row()->person_id;
                                            echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-white btn-xs"><?php echo $this->lang->line('action'); ?></button>
                                            <button type="button" class="btn btn-white btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_staff_payroll/<?php echo $row['staff_salary_id']; ?>');">
                                                <?php echo $this->lang->line('edit'); ?>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>staff_payroll/remove/<?php echo $row['staff_salary_id']; ?>');">
                                                <?php echo $this->lang->line('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-9 -->
        <!-- begin col-3 -->
        <div class="col-lg-3">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('year'); ?> *</label>
                        <div>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" id="year">
                                <option value=""><?php echo $this->lang->line('select_year'); ?></option>
                                <option value="<?php echo date('Y') - 4; ?>"><?php echo date('Y') - 4; ?></option>
                                <option value="<?php echo date('Y') - 3; ?>"><?php echo date('Y') - 3; ?></option>
                                <option value="<?php echo date('Y') - 2; ?>"><?php echo date('Y') - 2; ?></option>
                                <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                                <option <?php echo 'selected'; ?> value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                                <option value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
                                <option value="<?php echo date('Y') + 2; ?>"><?php echo date('Y') + 2; ?></option>
                                <option value="<?php echo date('Y') + 3; ?>"><?php echo date('Y') + 3; ?></option>
                                <option value="<?php echo date('Y') + 4; ?>"><?php echo date('Y') + 4; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('month'); ?> *</label>
                        <div>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" id="month">
                                <option value=""><?php echo $this->lang->line('select_month'); ?></option>
                                <option <?php if (date('F') == 'January') echo 'selected'; ?> value="January"><?php echo $this->lang->line('january'); ?></option>
                                <option <?php if (date('F') == 'February') echo 'selected'; ?> value="February"><?php echo $this->lang->line('february'); ?></option>
                                <option <?php if (date('F') == 'March') echo 'selected'; ?> value="March"><?php echo $this->lang->line('march'); ?></option>
                                <option <?php if (date('F') == 'April') echo 'selected'; ?> value="April"><?php echo $this->lang->line('april'); ?></option>
                                <option <?php if (date('F') == 'May') echo 'selected'; ?> value="May"><?php echo $this->lang->line('may'); ?></option>
                                <option <?php if (date('F') == 'June') echo 'selected'; ?> value="June"><?php echo $this->lang->line('june'); ?></option>
                                <option <?php if (date('F') == 'July') echo 'selected'; ?> value="July"><?php echo $this->lang->line('july'); ?></option>
                                <option <?php if (date('F') == 'August') echo 'selected'; ?> value="August"><?php echo $this->lang->line('august'); ?></option>
                                <option <?php if (date('F') == 'September') echo 'selected'; ?> value="September"><?php echo $this->lang->line('september'); ?></option>
                                <option <?php if (date('F') == 'October') echo 'selected'; ?> value="October"><?php echo $this->lang->line('october'); ?></option>
                                <option <?php if (date('F') == 'November') echo 'selected'; ?> value="November"><?php echo $this->lang->line('november'); ?></option>
                                <option <?php if (date('F') == 'December') echo 'selected'; ?> value="December"><?php echo $this->lang->line('december'); ?></option>
                            </select>
                        </div>
                    </div>

                    <button type="button" onclick="showSingleMonthPayroll()" class="mb-sm btn btn-block btn-primary"><?php echo $this->lang->line('show'); ?></button>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
            <div class="widget widget-stats bg-orange">
                <div class="stats-icon"><i class="fa fa-money-bill-alt"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('due_salary_of'); ?> <?php echo $this->lang->line(strtolower(date('F'))) . ', ' . date('Y'); ?></b></h4>
                    <p>
                        <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                        <?php
                        $this->db->select_sum('amount');
                        $this->db->from('staff_salary');
                        $this->db->where('status', 0);
                        $this->db->where('month', date('F'));
                        $this->db->where('year', date('Y'));
                        $query = $this->db->get();

                        echo $query->row()->amount > 0 ? $query->row()->amount : 0;
                        ?>
                    </p>
                </div>
            </div>
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-credit-card"></i></div>
                <div class="stats-info">
                    <h4><b><?php echo $this->lang->line('total_salary_of'); ?> <?php echo $this->lang->line(strtolower(date('F'))) . ', ' . date('Y'); ?></b></h4>
                    <p>
                        <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                        <?php
                        $this->db->select_sum('amount');
                        $this->db->from('staff_salary');
                        $this->db->where('month', date('F'));
                        $this->db->where('year', date('Y'));
                        $query = $this->db->get();

                        echo $query->row()->amount > 0 ? $query->row()->amount : 0;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <!-- end col-3 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->

<script>
    function showSingleMonthPayroll() {
        var year = $("#year").val();
        var month = $("#month").val();

        url = "<?php echo base_url(); ?>single_month_staff_payroll/" + year + "/" + month;

        window.location = url;
    }
</script>