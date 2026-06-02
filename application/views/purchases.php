<!-- begin #content -->
<div id="content" class="content">
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>account"><?php echo $this->lang->line('account'); ?></a></li>
        <li class="breadcrumb-item active">Purchases</li>
    </ol>

    <h1 class="page-header">
        <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_add_purchase');" class="btn btn-primary pull-right">
            <i class="fa fa-plus"></i> Add Purchase
        </a>
        Purchases
        <small>Track all purchase entries date wise with optional bill upload.</small>
    </h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-inverse">
                <div class="panel-body">
                    <table id="data-table-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Purchase Head</th>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Bill</th>
                                <th>Created On</th>
                                <th>Created By</th>
                                <th>Updated On</th>
                                <th>Updated By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if ($this->db->table_exists('purchase')) {
                                $this->db->order_by('purchase_date', 'desc');
                                $this->db->order_by('timestamp', 'desc');
                                $purchases = $this->db->get('purchase')->result_array();
                                foreach ($purchases as $purchase):
                            ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo $purchase['purchase_date'] ? date('d M, Y', strtotime($purchase['purchase_date'])) : 'N/A'; ?></td>
                                        <td><?php echo html_escape($purchase['purchase_head']); ?></td>
                                        <td><?php echo $purchase['vendor_name'] ? html_escape($purchase['vendor_name']) : 'N/A'; ?></td>
                                        <td><?php echo $this->db->get_where('setting', array('name' => 'currency'))->row()->content . ' ' . number_format((float) $purchase['amount'], 2); ?></td>
                                        <td><?php echo $purchase['description'] ? html_escape($purchase['description']) : 'N/A'; ?></td>
                                        <td>
                                            <?php if (!empty($purchase['bill_file']) && file_exists(FCPATH . 'uploads/purchases/' . $purchase['bill_file'])) : ?>
                                                <a href="<?php echo base_url(); ?>uploads/purchases/<?php echo $purchase['bill_file']; ?>" target="_blank" class="btn btn-info btn-xs">
                                                    <i class="fa fa-eye"></i> View Bill
                                                </a>
                                            <?php elseif (!empty($purchase['bill_file'])) : ?>
                                                <span class="badge badge-warning">File Missing</span>
                                            <?php else : ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo !empty($purchase['created_on']) ? date('d M, Y', $purchase['created_on']) : 'N/A'; ?></td>
                                        <td>
                                            <?php
                                            if (!empty($purchase['created_by']) && $this->db->get_where('user', array('user_id' => $purchase['created_by']))->num_rows() > 0) {
                                                $user_row = $this->db->get_where('user', array('user_id' => $purchase['created_by']))->row();
                                                if ($user_row->user_type == 1) {
                                                    echo 'Admin';
                                                } else {
                                                    $staff_row = $this->db->get_where('staff', array('staff_id' => $user_row->person_id))->row();
                                                    echo $staff_row ? html_escape($staff_row->name) : 'User #' . html_escape($purchase['created_by']);
                                                }
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo !empty($purchase['timestamp']) ? date('d M, Y', $purchase['timestamp']) : 'N/A'; ?></td>
                                        <td>
                                            <?php
                                            if (!empty($purchase['updated_by']) && $this->db->get_where('user', array('user_id' => $purchase['updated_by']))->num_rows() > 0) {
                                                $user_row = $this->db->get_where('user', array('user_id' => $purchase['updated_by']))->row();
                                                if ($user_row->user_type == 1) {
                                                    echo 'Admin';
                                                } else {
                                                    $staff_row = $this->db->get_where('staff', array('staff_id' => $user_row->person_id))->row();
                                                    echo $staff_row ? html_escape($staff_row->name) : 'User #' . html_escape($purchase['updated_by']);
                                                }
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-white btn-xs">Action</button>
                                                <button type="button" class="btn btn-white btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_purchase/<?php echo $purchase['purchase_id']; ?>');">
                                                        Edit
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>purchases/remove/<?php echo $purchase['purchase_id']; ?>');">
                                                        Remove
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                            <?php
                                endforeach;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->
