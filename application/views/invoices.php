<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('invoices'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('invoices_header'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <table id="data-table-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="1%">#</th>
                                <th class="text-nowrap"><?php echo $this->lang->line('invoice'); ?></th>
                                <th class="text-nowrap">Student Name</th>
                                <th class="text-nowrap">Student Mobile</th>
                                <th class="text-nowrap"><?php echo $this->lang->line('status'); ?></th>
                                <th class="text-nowrap">Seat</th>
                                <th class="text-nowrap">Shift</th>
                                <th class="text-nowrap">Plan</th>
                                <th class="text-nowrap"><?php echo $this->lang->line('amount'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('due_date'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('payment_method'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('sms'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('email'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('late_fee'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_on'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_by'); ?></th>
                                <?php if ($this->session->userdata('user_type') != 3) : ?>
                                    <th class="text-nowrap"><?php echo $this->lang->line('options'); ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            if ($this->session->userdata('user_type') == 3) {
                                $tenant_id = $this->db->get_where('user', array('user_id' => $this->session->userdata('user_id')))->row()->person_id;
                                $bill_info = $this->db->get_where('invoice', array('tenant_id' => $tenant_id))->result_array();
                            } else {
                                $bill_info = $this->db->get('invoice')->result_array();
                            }
                            foreach ($bill_info as $row) :
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td>
                                        <a href="<?php echo base_url(); ?>invoice/<?php echo $row['invoice_id']; ?>">
                                            #<?php echo html_escape($row['invoice_number']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo html_escape($row['tenant_name']); ?></td>
                                    <td><?php echo html_escape($row['tenant_mobile']); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 0) : ?>
                                            <span class="badge badge-warning"><?php echo $this->lang->line('due'); ?></span>
                                        <?php endif; ?>
                                        <?php if ($row['status'] == 1) : ?>
                                            <span class="badge badge-primary"><?php echo $this->lang->line('paid'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape(!empty($row['seat_label']) ? $row['seat_label'] : $row['room_number']); ?></td>
                                    <td><?php echo !empty($row['shift_name']) ? html_escape($row['shift_name']) : 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        $plan_labels = array(
                                            'per_day' => 'Per Day Plan',
                                            'monthly' => 'Monthly Plan',
                                            'quarterly' => '3 Month Plan',
                                            'half_yearly' => '6 Month Plan',
                                            'yearly' => '12 Month Plan'
                                        );
                                        echo !empty($row['plan_type']) && isset($plan_labels[$row['plan_type']]) ? $plan_labels[$row['plan_type']] : 'N/A';
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                                        <?php
                                        $grand_total    =   0;
                                        $rent_total     =   0;
                                        $service_total  =   0;

                                        $this->db->select_sum('amount');
                                        $this->db->from('tenant_rent');
                                        $this->db->where('invoice_id', $row['invoice_id']);
                                        $query = $this->db->get();

                                        $rent_total = $query->row()->amount;

                                        $service_costs = $this->db->get_where('invoice_service', array('invoice_id' => $row['invoice_id']))->result_array();
                                        foreach ($service_costs as $service_cost) {
                                            $service_total += $this->db->get_where('service', array('service_id' => $service_cost['service_id']))->row()->cost;
                                        }

                                        $grand_total = $rent_total + $service_total;

                                        echo number_format($grand_total);
                                        ?>
                                    </td>
                                    <td><?php echo date('Y-m-d', $row['due_date']); ?></td>
                                    
                                    
                      
                                    
                                    
                                    
                                    
                                    
                                    
                                    <td>
                                        <?php
                                            if ($row['payment_method_id']) {
                                                $payment_method_query  =   $this->db->get_where('payment_method', array('payment_method_id' => $row['payment_method_id']));
                                                if ($payment_method_query->num_rows() > 0) {
                                                    echo $payment_method_query->row()->name;
                                                } else {
                                                    echo 'N/A';
                                                }
                                            } else {
                                                echo 'N/A';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $row['sms'] ? $this->lang->line('sent') : $this->lang->line('not_sent'); ?></td>
                                <?php    
                                
                                            if ($row['email'] != "1") {
                                                
                                            $proo = $this->db->get_where('invoice', array('tenant_id' => 'NULL'))->result_array();
                                           
                                            foreach ($proo as $pro) {
                                                // Skipping synchronous HTTP request to prevent page hang
                                                // file_get_contents("https://pg1.apnagharpg.in/email_invoice/" . $pro['invoice_id']);
                                            }
                                        }
                                    
                                    ?>
                                    <td><?php echo $row['email'] ? $this->lang->line('sent') : $this->lang->line('not_sent'); ?></td>
                                    <td>
                                        
                                        
                                        <?php 
                                        
                                    $currdate = date_create(date("Y-m-d"));
                                    $duedate = date_create(date('Y-m-d', $row['due_date']));
                                    $diff = date_diff($currdate, $duedate);
                                    $days = (int)$diff->format("%a");
                                        
                                  if ($row['status'] == 0){
                                       echo $days * 50;
                                  } else {
                                      echo "Not Applicable";
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
                                    <?php if ($this->session->userdata('user_type') != 3) : ?>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-white btn-xs"><?php echo $this->lang->line('action'); ?></button>
                                                <button type="button" class="btn btn-white btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="javascript:;" onclick="showInvoiceModal(<?php echo $row['invoice_id']; ?>)">
                                                        <?php echo $this->lang->line('show_invoice_pdf'); ?>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_invoice_sms/<?php echo $row['invoice_id']; ?>/<?php echo $grand_total; ?>')">
                                                        <?php echo $this->lang->line('send_sms'); ?>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_invoice_services/<?php echo $row['invoice_id']; ?>')">
                                                        <?php echo $this->lang->line('update_services'); ?>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_invoice_number/<?php echo $row['invoice_id']; ?>');">
                                                        Edit Invoice Number
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_invoice_status/<?php echo $row['invoice_id']; ?>');">
                                                        <?php echo $this->lang->line('update_status'); ?>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_add_transaction/<?php echo $row['invoice_id']; ?>');">
                                                        Add Transaction
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_view_transactions/<?php echo $row['invoice_id']; ?>');">
                                                        View Transactions
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>invoices/remove/<?php echo $row['invoice_id']; ?>');">
                                                        <?php echo $this->lang->line('remove'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                
                                

                                
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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




                                    

<script>
function printPageArea(areaID){
    var printContent = document.getElementById(areaID).innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
}
</script>



<script>
    function showInvoiceModal(invoice_id) {
        $.ajax({
            url: "<?php echo base_url(); ?>generate_invoice_pdf/" + invoice_id,
            success: function(result) {
                // console.log(result);
            }
        });

        showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_invoice_pdf/' + invoice_id);
    }
</script>
