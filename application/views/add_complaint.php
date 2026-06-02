<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('add_complaint'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('add_complaint_header'); ?>
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
                    <?php echo form_open_multipart('complaints/add', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <?php if ($this->session->userdata('user_type') != 3) : ?>
                        <div class="form-group">
                            <label><?php echo $this->lang->line('tenant'); ?> *</label>
                            <div>
                                <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="tenant_id">
                                    <option value=""><?php echo $this->lang->line('select_tenant'); ?></option>
                                    <?php
                                    $tenants = $this->db->get_where('tenant', array('status' => 1))->result_array();
                                    foreach ($tenants as $tenant) :
                                    ?>
                                        <option value="<?php echo html_escape($tenant['tenant_id']); ?>"><?php echo html_escape($tenant['name'] . ' - ' . $this->db->get_where('room', array('room_id' => $tenant['room_id']))->row()->room_number); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('subject'); ?> *</label>
                        <input type="text" name="subject" placeholder="<?php echo $this->lang->line('add_complaint_subject_placeholder'); ?>" class="form-control" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('pictures_related_to_complaint'); ?></label>
                        <br>
                        <span class="btn btn-primary fileinput-button">
                            <i class="fa fa-plus"></i>
                            <span><?php echo $this->lang->line('add_picture_1'); ?></span>
                            <input class="form-control" type="file" name="complaint_picture_1">
                        </span>
                        <br>
                        <br>
                        <span class="btn btn-primary fileinput-button">
                            <i class="fa fa-plus"></i>
                            <span><?php echo $this->lang->line('add_picture_2'); ?></span>
                            <input class="form-control" type="file" name="complaint_picture_2">
                        </span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('video_related_to_complaint'); ?></label>
                        <br>
                        <span class="btn btn-primary fileinput-button">
                            <i class="fa fa-plus"></i>
                            <span><?php echo $this->lang->line('add_video'); ?></span>
                            <input class="form-control" type="file" name="complaint_video">
                        </span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('details'); ?></label>
                        <textarea rows="10" style="resize: none" type="text" name="content" placeholder="<?php echo $this->lang->line('add_complaint_details_placeholder'); ?>" class="form-control"></textarea>
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