<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('tenant_invoices'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <?php $tenant_id = isset($tenant_id) ? $tenant_id : 0; ?>
    <?php
    $tenants        =   $this->db->get('tenant')->result_array();
    $tenant_name    =   $this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row()->name;
    ?>
    <h1 class="page-header">
    <?php echo $this->lang->line('tenant_invoices_header'); ?> <?php echo $tenant_name; ?>
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
                                <th class="text-nowrap"><?php echo $this->lang->line('invoice'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('tenant_name'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('tenant_mobile'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('status'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('room'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('amount'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('due_date'); ?></th>
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
                            $invoices = $this->db->get_where('invoice', array('tenant_id' => $tenant_id))->result_array();
                            foreach ($invoices as $row) :
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
                                    <td><?php echo date('d M, Y', $row['due_date']); ?></td>
                                    <td><?php echo $row['sms'] ? $this->lang->line('sent') : $this->lang->line('not_sent'); ?></td>
                                    <td><?php echo $row['email'] ? $this->lang->line('sent') : $this->lang->line('not_sent'); ?></td>
                                    <td><?php echo html_escape($this->db->get_where('setting', array('name' => 'currency'))->row()->content . ' ' . number_format($row['late_fee'])); ?></td>
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
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_invoice_status/<?php echo $row['invoice_id']; ?>');">
                                                    <?php echo $this->lang->line('update_status'); ?>
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
        <!-- end col-9 -->
        <!-- begin col-3 -->
        <div class="col-lg-3">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('tenant'); ?> *</label>
                        <div>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" id="tenant_id">
                                <option value=""><?php echo $this->lang->line('select_tenant'); ?></option>
                                <?php foreach ($tenants as $tenant) : ?>
                                    <option <?php if ($tenant['tenant_id'] == $tenant_id) echo 'selected'; ?> value="<?php echo $tenant['tenant_id']; ?>"><?php echo $tenant['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="button" onclick="showSingleTenantInvoices()" class="mb-sm btn btn-block btn-primary"><?php echo $this->lang->line('show'); ?></button>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
            <?php if ($this->session->userdata('user_type') != 3) : ?>
                <div class="widget widget-stats bg-orange">
                    <div class="stats-icon"><i class="fa fa-money-bill-alt"></i></div>
                    <div class="stats-info">
                        <h4><b><?php echo $this->lang->line('due_rents_of'); ?> <?php echo $tenant_name; ?></b></h4>
                        <p>
                            <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                            <?php
                            $due_rent_total     =   0;
                            $due_service_total  =   0;

                            $this->db->select_sum('amount');
                            $this->db->from('tenant_rent');
                            $this->db->where('status', 0);
                            $this->db->where('tenant_id', $tenant_id);
                            $query = $this->db->get();

                            $due_rent_total = $query->row()->amount;

                            $due_invoices = $this->db->get_where('tenant_rent', array('tenant_id' => $tenant_id, 'status' => 0))->result_array();
                            foreach ($due_invoices as $due_invoice) {
                                $due_services = $this->db->get_where('invoice_service', array('invoice_id' => $due_invoice['invoice_id']))->result_array();

                                foreach ($due_services as $due_service) {
                                    $due_service_total += $this->db->get_where('service', array('service_id' => $due_service['service_id']))->row()->cost;
                                }
                            }                            

                            echo $query->row()->amount > 0 ? number_format($due_rent_total + $due_service_total) : number_format(0 + $due_service_total);
                            ?>
                        </p>
                    </div>
                </div>
                <div class="widget widget-stats bg-blue">
                    <div class="stats-icon"><i class="fa fa-credit-card"></i></div>
                    <div class="stats-info">
                        <h4><b><?php echo $this->lang->line('total_rents_of'); ?> <?php echo $tenant_name; ?></b></h4>
                        <p>
                            <?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content; ?>
                            <?php
                            $total_rent_total     =   0;
                            $total_service_total  =   0;

                            $this->db->select_sum('amount');
                            $this->db->from('tenant_rent');
                            $this->db->where('tenant_id', $tenant_id);
                            $query = $this->db->get();

                            $total_rent_total = $query->row()->amount;

                            $total_invoices = $this->db->get_where('tenant_rent', array('tenant_id' => $tenant_id))->result_array();
                            foreach ($total_invoices as $total_invoice) {
                                $total_services = $this->db->get_where('invoice_service', array('invoice_id' => $total_invoice['invoice_id']))->result_array();

                                foreach ($total_services as $total_service) {
                                    $total_service_total += $this->db->get_where('service', array('service_id' => $total_service['service_id']))->row()->cost;
                                }
                            }                            

                            echo $query->row()->amount > 0 ? number_format($total_rent_total + $total_service_total) : number_format(0 + $total_service_total);
                            ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- end col-3 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->

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

    function showSingleTenantInvoices() {
        var tenant_id = $("#tenant_id").val();

        url = "<?php echo base_url(); ?>single_tenant_invoices/" + tenant_id;

        window.location = url;
    }
</script>
