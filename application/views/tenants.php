<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('tenants'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <a href="<?php echo base_url(); ?>add_tenant">
            <button type="button" class="btn btn-inverse"><i class="fa fa-plus"></i> Add Student</button>
        </a>
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
                                <th>Image</th>
                                <th class="text-nowrap">Student Name</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Mobile</th>
                                <th class="text-nowrap">ID Type</th>
                                <th class="text-nowrap">ID Number</th>
                                <th class="text-nowrap">Seat</th>
                                <th class="text-nowrap">Shift</th>
                                <th class="text-nowrap">Plan</th>
                                <th class="text-nowrap">Updated On</th>
                                <th class="text-nowrap">Updated By</th>
                                <th class="text-nowrap">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            $tenants = $this->db->get('tenant')->result_array();
                            foreach ($tenants as $tenant) :
                            ?>
                                <tr>
                                    <td width="1%"><?php echo $count++; ?></td>
                                    <td class="with-img">
                                        <?php if ($tenant['image_link']) : ?>
                                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['image_link']); ?>" alt="<?php echo html_escape($tenant['name']); ?>" class="img-rounded height-30" />
                                        <?php else : ?>
                                            <img src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Tenant image not found" class="img-rounded height-30" />
                                        <?php endif; ?>
                                    </td>
                                    
                                    
                                    <td><a href="<?php echo base_url(); ?>tanent_details?tid=<?php echo $tenant['tenant_id']; ?>"><?php echo html_escape($tenant['name']); ?></a></td>
                                    <td>
                                        <?php
                                        if ($tenant['status'])
                                            echo '<span class="badge badge-primary">' . $this->lang->line('active') . '</span>';
                                        else
                                            echo '<span class="badge badge-warning">' . $this->lang->line('inactive') . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo $tenant['mobile_number'] ? html_escape($tenant['mobile_number']) : 'N/A'; ?></td>
                                    <?php $id_type = $tenant['id_type_id'] ? $this->db->get_where('id_type', array('id_type_id' => $tenant['id_type_id']))->row() : null; ?>
                                    <td><?php echo ($tenant['id_number'] && $id_type) ? html_escape($id_type->name) : 'N/A'; ?></td>
                                    <td><?php echo $tenant['id_number'] ? html_escape($tenant['id_number']) : 'N/A'; ?></td>
                                    <?php $room = $tenant['room_id'] ? $this->db->get_where('room', array('room_id' => $tenant['room_id']))->row() : null; ?>
                                    <td><?php echo $room ? html_escape($room->roomnumber) . ' -> Seat ' . html_escape($room->room_number) : 'N/A'; ?></td>
                                    <?php $shift = !empty($tenant['shift_id']) ? $this->db->get_where('study_shift', array('shift_id' => $tenant['shift_id']))->row() : null; ?>
                                    <td><?php echo $shift ? html_escape($shift->shift_name . ' (' . $shift->timing_label . ')') : 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        $plan_labels = array(
                                            'per_day' => 'Per Day Plan',
                                            'monthly' => 'Monthly Plan',
                                            'quarterly' => '3 Month Plan',
                                            'half_yearly' => '6 Month Plan',
                                            'yearly' => '12 Month Plan'
                                        );
                                        echo !empty($tenant['plan_type']) && isset($plan_labels[$tenant['plan_type']]) ? $plan_labels[$tenant['plan_type']] : 'N/A';
                                        ?>
                                    </td>
                                    <td><?php echo date('d M, Y', $tenant['timestamp']); ?></td>
                                    <td>
                                        <?php
                                        $updated_user = $this->db->get_where('user', array('user_id' => $tenant['updated_by']))->row();
                                        if (!$updated_user) {
                                            echo 'Unknown';
                                        } elseif ($updated_user->user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $staff = $this->db->get_where('staff', array('staff_id' => $updated_user->person_id))->row();
                                            echo $staff ? html_escape($staff->name) : 'Unknown';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-white btn-xs"><?php echo $this->lang->line('action'); ?></button>
                                            <button type="button" class="btn btn-white btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_tenant_details/<?php echo $tenant['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('details'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_image/<?php echo $tenant['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('change_tenant_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_tenant_id_image/<?php echo $tenant['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('show_id_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_id_image/<?php echo $tenant['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('change_id_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_tenant/<?php echo $tenant['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('edit'); ?>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="<?php echo base_url(); ?>download_tenant_agreement/<?php echo $tenant['tenant_id']; ?>" target="_blank">
                                                    Download Student Agreement
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_upload_signed_agreement/<?php echo $tenant['tenant_id']; ?>');">
                                                    Upload Signed Agreement
                                                </a>
                                                <?php if ($tenant['signed_agreement']): ?>
                                                    <a class="dropdown-item" href="<?php echo base_url(); ?>uploads/tenants/<?php echo $tenant['signed_agreement']; ?>" target="_blank">
                                                        View Signed Agreement
                                                    </a>
                                                <?php endif; ?>
                                                <div class="dropdown-divider"></div>
                                                <?php if ($tenant['status']) : ?>
                                                    <a class="dropdown-item" href="javascript:;" onclick="deactivate_modal('<?php echo base_url(); ?>tenants/deactivate/<?php echo $tenant['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('deactivate'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>tenants/remove/<?php echo $tenant['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
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

<style>
    .hover_img a {
        position: relative;
    }

    .hover_img a span {
        position: absolute;
        display: none;
        z-index: 99;
    }

    .hover_img a:hover span {
        display: block;
    }
</style>
