<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Student Agreement Template</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Student Agreement Template</h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                    </div>
                    <h4 class="panel-title">Student Agreement Body</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open('tenant_agreement_settings/update', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="form-group">
                        <label>Format the agreement terms here. The student details (photo, name, mobile, seat) will be automatically prepended when downloading the agreement for a specific student.</label>
                        <br>
                        <?php
                        $template_row = $this->db->get_where('setting', array('name' => 'tenant_agreement_template'))->row();
                        $template = $template_row ? $template_row->content : '';
                        ?>
                        <textarea class="ckeditor" id="ckeditor1" name="tenant_agreement_template"><?php echo $template; ?></textarea>
                    </div>

                    <button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <!-- end panel -->
        </div>
    </div>
</div>
<!-- end #content -->

<script>
    $(document).ready(function() {
        CKEDITOR.replace('ckeditor1');
    });
</script>
