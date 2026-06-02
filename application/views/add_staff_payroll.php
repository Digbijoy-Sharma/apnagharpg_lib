<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('staff_salary'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('staff_salary'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-6 offset-lg-3">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <?php echo form_open('staff_payroll/add', array('method' => 'post', 'data-parsley-validate' => 'ture')); ?>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('name'); ?> *</label>
                        <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="staff_id">
                            <option value=""><?php echo $this->lang->line('select_staff'); ?></option>
                            <?php
                                $staff = $this->db->get_where('staff', array('status' => 1))->result_array();
                                foreach($staff as $row):
                            ?>
                                <option value="<?php echo html_escape($row['staff_id']); ?>"><?php echo html_escape($row['name']); ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('year'); ?> *</label>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="year">
                                <option value=""><?php echo $this->lang->line('select_year'); ?></option>
                                <option value="<?php echo date('Y') - 4; ?>"><?php echo date('Y') - 4; ?></option>
                                <option value="<?php echo date('Y') - 3; ?>"><?php echo date('Y') - 3; ?></option>
                                <option value="<?php echo date('Y') - 2; ?>"><?php echo date('Y') - 2; ?></option>
                                <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                                <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                                <option value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
                                <option value="<?php echo date('Y') + 2; ?>"><?php echo date('Y') + 2; ?></option>
                                <option value="<?php echo date('Y') + 3; ?>"><?php echo date('Y') + 3; ?></option>
                                <option value="<?php echo date('Y') + 4; ?>"><?php echo date('Y') + 4; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('month'); ?> *</label>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="month">
                                <option value=""><?php echo $this->lang->line('select_month'); ?></option>
                                <option value="January"><?php echo $this->lang->line('january'); ?></option>
                                <option value="February"><?php echo $this->lang->line('february'); ?></option>
                                <option value="March"><?php echo $this->lang->line('march'); ?></option>
                                <option value="April"><?php echo $this->lang->line('april'); ?></option>
                                <option value="May"><?php echo $this->lang->line('may'); ?></option>
                                <option value="June"><?php echo $this->lang->line('june'); ?></option>
                                <option value="July"><?php echo $this->lang->line('july'); ?></option>
                                <option value="August"><?php echo $this->lang->line('august'); ?></option>
                                <option value="September"><?php echo $this->lang->line('september'); ?></option>
                                <option value="October"><?php echo $this->lang->line('october'); ?></option>
                                <option value="November"><?php echo $this->lang->line('november'); ?></option>
                                <option value="December"><?php echo $this->lang->line('december'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('amount'); ?> (<?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>) *</label>
                            <input type="text" name="amount" placeholder="<?php echo $this->lang->line('enter_amount'); ?>" class="form-control" data-parsley-required="true" data-parsley-type="number" min="0">
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('status'); ?> *</label>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
                                <option value=""><?php echo $this->lang->line('select_status'); ?></option>
                                <option value="0"><?php echo $this->lang->line('due'); ?></option>
                                <option value="1"><?php echo $this->lang->line('paid'); ?></option>
                            </select>
                        </div>

                        <button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('submit'); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->
