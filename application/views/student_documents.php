<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Documents</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Documents</h1>
    <!-- end page-header -->
    <p class="text-muted m-b-20">Official documents shared with you by the library. Click "View" to open a document in a new tab.</p>

    <?php
        // --- Load all document settings up front (single source of truth for this business) ---
        $gst_certificate_row = $this->db->get_where('setting', array('name' => 'gst_certificate'))->row();
        $gst_enabled_row     = $this->db->get_where('setting', array('name' => 'gst_enabled'))->row();
        $gst_number_row      = $this->db->get_where('setting', array('name' => 'gst_number'))->row();
        $trade_licence_row   = $this->db->get_where('setting', array('name' => 'trade_licence'))->row();

        $gst_enabled    = ($gst_enabled_row && $gst_enabled_row->content == '1');
        $gst_number     = ($gst_number_row && $gst_number_row->content !== '') ? $gst_number_row->content : '';

        $show_gst       = $gst_enabled
                          && $gst_certificate_row
                          && $gst_certificate_row->content !== ''
                          && file_exists(FCPATH . 'uploads/website/' . $gst_certificate_row->content);

        $show_trade     = $trade_licence_row
                          && $trade_licence_row->content !== ''
                          && file_exists(FCPATH . 'uploads/website/' . $trade_licence_row->content);
    ?>

    <!-- begin row -->
    <div class="row">
        <!-- begin col-6 -->
        <div class="col-lg-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">GST Certificate</h4>
                </div>
                <div class="panel-body">
                    <?php if ($show_gst): ?>
                        <div class="form-group">
                            <label>Status</label><br>
                            <span class="label label-success label-lg m-b-10"><i class="fa fa-check-circle"></i> Available</span>
                        </div>
                        <?php if ($gst_number !== ''): ?>
                            <div class="form-group">
                                <label>GST Number</label>
                                <input type="text" class="form-control" value="<?php echo html_escape($gst_number); ?>" readonly>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo base_url(); ?>uploads/website/<?php echo $gst_certificate_row->content; ?>" target="_blank" class="btn btn-info">
                            <i class="fa fa-eye"></i> View GST Certificate
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info m-b-0">
                            <i class="fa fa-info-circle"></i>
                            <?php if (!$gst_enabled): ?>
                                GST is not currently enabled for this business. The certificate will appear here once the administrator enables GST and uploads the certificate.
                            <?php else: ?>
                                The GST certificate has not been uploaded yet. Please contact the administrator.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- end col-6 -->

        <!-- begin col-6 -->
        <div class="col-lg-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Trade Licence</h4>
                </div>
                <div class="panel-body">
                    <?php if ($show_trade): ?>
                        <div class="form-group">
                            <label>Status</label><br>
                            <span class="label label-success label-lg m-b-10"><i class="fa fa-check-circle"></i> Available</span>
                        </div>
                        <a href="<?php echo base_url(); ?>uploads/website/<?php echo $trade_licence_row->content; ?>" target="_blank" class="btn btn-info">
                            <i class="fa fa-eye"></i> View Trade Licence
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info m-b-0">
                            <i class="fa fa-info-circle"></i> The Trade Licence has not been uploaded yet. Please contact the administrator.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- end col-6 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->
