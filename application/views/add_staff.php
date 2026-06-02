<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('add_staff'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('add_staff_header'); ?>
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
                    <?php echo form_open('staff/add', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('name'); ?> *</label>
                        <input type="text" name="name" placeholder="<?php echo $this->lang->line('enter_name'); ?>" class="form-control" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('role'); ?></label>
                        <input type="text" name="role" placeholder="<?php echo $this->lang->line('add_staff_role_placeholder'); ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('email'); ?> (<?php echo $this->lang->line('for_staff_login'); ?>)</label>
                        <input type="email" name="email" placeholder="<?php echo $this->lang->line('enter_email'); ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('password'); ?> (<?php echo $this->lang->line('for_staff_login'); ?>)</label>
                        <input type="text" name="password" id="password-indicator-visible" class="form-control m-b-5">
                        <div id="passwordStrengthDiv2" class="is0 m-t-5"></div>
                    </div>
                    <div class="note note-yellow m-b-15">
                        <span><?php echo $this->lang->line('default_password'); ?></span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('mobile_number'); ?> *</label>
                        <input type="text" name="mobile_number" placeholder="<?php echo $this->lang->line('enter_mobile_number'); ?>" class="form-control" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('permission'); ?> *</label>
                        <div class="row">
                            <?php 
                                $modules = $this->db->get('module')->result_array();
                                foreach ($modules as $module) :
                            ?>
                            <div class="col-md-4">
                                <div class="checkbox checkbox-css">
                                    <input type="checkbox" id="<?php echo html_escape($module['module_id']); ?>" value="<?php echo html_escape($module['module_id']); ?>" name="permission[]" />
                                    <label for="<?php echo html_escape($module['module_id']); ?>"><?php echo $this->lang->line(strtolower($module['module_name'])); ?></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('status'); ?> *</label>
                        <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
                            <option value=""><?php echo $this->lang->line('select_status'); ?></option>
                            <option value="0"><?php echo $this->lang->line('inactive'); ?></option>
                            <option value="1"><?php echo $this->lang->line('active'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('remarks'); ?></label>
                        <textarea style="resize: none" type="text" name="remarks" placeholder="<?php echo $this->lang->line('enter_remarks'); ?>" class="form-control"></textarea>
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