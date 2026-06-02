<!-- begin #header -->
<div id="header" class="header navbar-default hidden-print">
    <!-- begin navbar-header -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed navbar-toggle-left" data-click="sidebar-minify">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="<?php echo base_url(); ?>" class="navbar-brand">
            <?php echo strtoupper($this->db->get_where('setting', array('name' => 'system_name'))->row()->content); ?>
        </a>
    </div>
    <!-- end navbar-header -->
    























    <!-- begin header-nav -->
    <ul class="navbar-nav navbar-right">
        <li>    <a href="#" type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#exampleModal">Payment Details</a></li>
    <li class="dropdown">
            <?php
                $expired_leases = [];

                if ($this->session->userdata('user_type') != 3) {
                    $expired_tenants = $this->db->get_where('tenant')->result_array();
                    foreach ($expired_tenants as $expired_tenant) {
                        if ($expired_tenant['lease_end']) {
                            $time_differences = $expired_tenant['lease_end'] - time();
                
                            $i = $time_differences / (24 * 3600);

                            if ($i <= 1)
                                array_push($expired_leases, $expired_tenant);
                        }
                    }
                }

                $open_complaints = [];

                if ($this->session->userdata('user_type') != 3) {
                    $open_complaints = $this->db->get_where('complaint', array('status' => 0))->result_array();
                } else {
                    $tenant_id = $this->security->xss_clean($this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id);
                    $open_complaints = $this->db->get_where('complaint', array('status' => 0, 'tenant_id' => $tenant_id))->result_array();
                }
            ?>
            <a href="#" data-toggle="dropdown" class="dropdown-toggle icon">
                <i class="material-icons">inbox</i>
                <span class="label"><?php echo count($expired_leases) + count($open_complaints); ?></span>
            </a>

            <div class="dropdown-menu media-list dropdown-menu-right" style="max-height: 350px; overflow-y: auto;">
                <?php if ($this->session->userdata('user_type') != 3): ?>
                <div class="dropdown-header"><?php echo $this->lang->line('expired_leases_notification'); ?> (<?php echo count($expired_leases); ?>)</div>
                <?php foreach ($expired_leases as $expired_lease): ?>
                <a href="<?php echo base_url('lease_monitor'); ?>" class="dropdown-item media" style="white-space: unset">
                    <div class="media-body">
                        <p style="min-width: 270px">
                            <?php echo $expired_lease['name'] . ' - ' . date('d M, Y', $expired_lease['lease_end']); ?>
                        </p>
                    </div>
                </a>
                <?php 
                    endforeach; 
                endif;
                ?>

                <div class="dropdown-header"><?php echo $this->lang->line('open_complaints'); ?> (<?php echo count($open_complaints); ?>)</div>
                <?php foreach ($open_complaints as $open_complaint): ?>
                <a href="<?php echo base_url('open_complaints'); ?>" class="dropdown-item media" style="white-space: unset">
                    <div class="media-body">
                        <p style="min-width: 270px">
                            <?php echo $open_complaint['complaint_number'] . ' - ' . $open_complaint['subject']; ?>
                        </p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </li>
        <li class="dropdown navbar-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                <?php
                echo '<span class="d-md-inline">';
                echo $this->lang->line('hi') . ', ';
                $header_user_type =  $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->user_type;
                if ($header_user_type == 1) {
                    echo $this->lang->line('admin');
                    echo '<img src="' . base_url() . 'uploads/website/' . $this->db->get_where('setting', array('name' => 'favicon'))->row()->content . '" alt="Mars Room Management System"' . '/>';
                } else if ($header_user_type == 2) {
                    $header_person_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                    echo html_escape($this->db->get_where('staff', array('staff_id' => $header_person_id))->row()->name);
                    echo '<img src="' . base_url() . 'uploads/website/' . $this->db->get_where('setting', array('name' => 'favicon'))->row()->content . '" alt="Mars Room Management System"' . '/>';
                } else {
                    $header_person_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                    $header_tenant_image = $this->db->get_where('tenant', array('tenant_id' => $header_person_id))->row()->image_link;
                    echo html_escape($this->db->get_where('tenant', array('tenant_id' => $header_person_id))->row()->name);
                    if ($header_tenant_image)
                        echo '<img src="' . base_url() . 'uploads/tenants/' . $header_tenant_image . '" alt="Mars Room Management System"' . '/>';
                    else
                        echo '<img src="' . base_url() . 'uploads/website/' . $this->db->get_where('setting', array('name' => 'favicon'))->row()->content . '" alt="Mars Room Management System"' . '/>';
                }
                echo '</span>';
                ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?php echo base_url(); ?>profile_settings" class="dropdown-item"><?php echo $this->lang->line('profile_settings'); ?></a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo base_url(); ?>auth/logout" class="dropdown-item"><?php echo $this->lang->line('log_out'); ?></a>
            </div>
        </li>
    </ul>
    <!-- end header navigation right -->

    <div class="search-form">
        <button class="search-btn" type="submit"><i class="material-icons">search</i></button>
        <input type="text" class="form-control" placeholder="Search Something..." />
        <a href="#" class="close" data-dismiss="navbar-search"><i class="material-icons">close</i></a>
    </div>
</div>






<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Payment Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <h3>A/C Holder's Name: <strong>APNA GHAR PAYING GUEST</strong></h3>
       <h3>A/C Type: <strong> Current Account</strong></h3>
       <h3>A/C: <strong>634305500631</strong></h3>
       <h3>IFSC: <strong>ICIC0006343</strong></h3>
       <h3>Branch: <strong>Bhangagarh, Guwahati,Assam</strong></h3>
       <h3>Mobile: <strong>9756056848</strong></h3>
       <h3>UPI: <strong>APNAGHARPAYINGGUEST.ibz@icici</strong></h3>
       <br><br>
       <form><script src="https://checkout.razorpay.com/v1/payment-button.js" data-payment_button_id="pl_L5xuspUMQZq5rn" async> </script> </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>


<!-- end #header -->