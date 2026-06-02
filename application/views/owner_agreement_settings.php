<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Owner Agreement</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Owner Agreement Settings</h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <div class="col-lg-6">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                    </div>
                    <h4 class="panel-title">Owner Agreement</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open_multipart('owner_agreement_settings/update', array('method' => 'post')); ?>
                    <div class="form-group">
                        <label>Current Agreement</label>
                        <br>
                        <?php 
                        $rent_agreement = $this->db->get_where('setting', array('name' => 'rent_agreement'))->row();
                        if ($rent_agreement && $rent_agreement->content != ''): 
                        ?>
                            <a href="<?php echo base_url(); ?>uploads/website/<?php echo $rent_agreement->content; ?>" target="_blank" class="btn btn-info">
                                <i class="fa fa-eye"></i> View Current Agreement
                            </a>
                        <?php else: ?>
                            <span class="text-danger">No agreement uploaded yet.</span>
                        <?php endif; ?>
                    </div>
                    <div class="note note-yellow m-b-15">
                        <span>Upload a new rent agreement document (PDF, JPG, PNG). This will replace the existing one.</span>
                    </div>
                    <div class="form-group">
                        <label for="rent_agreement">Select Document</label>
                        <input type="file" class="form-control" id="rent_agreement" name="rent_agreement" accept=".pdf,.jpg,.jpeg,.png">
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
