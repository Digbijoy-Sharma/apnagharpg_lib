<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('profile_settings'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('profile_settings_header'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-4 offset-lg-4">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <?php echo form_open('profile_settings/update/' . $this->session->userdata('user_id'), array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <?php
                    $user_info = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->result_array();
                    foreach ($user_info as $row) :
                        ?>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('email'); ?> *</label>
                            <input value="<?php echo html_escape($row['email']); ?>" type="email" name="email" placeholder="<?php echo $this->lang->line('email_placeholder'); ?>" class="form-control" data-parsley-required="true">
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('current_password'); ?> *</label>
                            <input type="password" name="old_password" placeholder="<?php echo $this->lang->line('current_password_placeholder'); ?>" class="form-control" data-parsley-required="true">
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('new_password'); ?></label>
                            <input type="password" name="new_password" placeholder="<?php echo $this->lang->line('new_password_placeholder'); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('confirm_password'); ?></label>
                            <input type="password" name="confirm_password" placeholder="<?php echo $this->lang->line('confirm_password_placeholder'); ?>" class="form-control">
                        </div>

                        <button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
                    <?php endforeach; ?>
                    <?php echo form_close(); ?>
                    <!-- end panel-body -->
                </div>
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->