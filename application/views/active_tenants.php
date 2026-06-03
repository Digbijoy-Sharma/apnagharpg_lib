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
            <button type="button" class="btn btn-inverse"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_tenant'); ?></button>
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
                                <th class="text-nowrap"><?php echo $this->lang->line('image'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('name'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('status'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('mobile'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('id_type'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('id_number'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('room'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('emergency_person'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('emergency_contact'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_on'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_by'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('options'); ?></th>
                                <th class="text-nowrap" width="110">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            $tenants = $this->db->get_where('tenant', array('status' => 1))->result_array();
                            foreach ($tenants as $tenant) :
                            ?>
                                <tr>
                                    <td width="1%"><?php echo $count++; ?></td>
                                    <td class="with-img">
                                        <?php if ($tenant['image_link']) : ?>
                                            <span><img class="img-rounded height-30" src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['image_link']); ?>" alt="<?php echo html_escape($tenant['name']); ?>" /></span>
                                        <?php else : ?>
                                            <span><img class="img-rounded height-30" src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Tenant image not found" /></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($tenant['name']); ?></td>
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
                                    <td><?php echo $tenant['emergency_person'] ? html_escape($tenant['emergency_person']) : 'N/A'; ?></td>
                                    <td><?php echo $tenant['emergency_contact'] ? html_escape($tenant['emergency_contact']) : 'N/A'; ?></td>
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
                                                <a class="dropdown-item" href="javascript:;" onclick="deactivate_modal('<?php echo base_url(); ?>active_tenants/deactivate/<?php echo $tenant['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('deactivate'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>tenants/remove/<?php echo $tenant['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $is_expiring_soon = !empty($tenant['lease_end']) && (($tenant['lease_end'] - time()) <= (7 * 24 * 60 * 60));
                                        if ($is_expiring_soon && !empty($tenant['mobile_number'])): ?>
                                            <button type="button" class="btn btn-success btn-xs" style="background-color: #25D366; border-color: #25D366;"
                                                    onclick="sendWhatsAppReminder('<?php echo htmlspecialchars($tenant['mobile_number'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($tenant['name'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo (int) $tenant['lease_end']; ?>);"
                                                    title="Open WhatsApp Web with a pre-filled renewal reminder">
                                                <i class="fab fa-whatsapp"></i> Remind
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">&mdash;</span>
                                        <?php endif; ?>
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

<script>
function sendWhatsAppReminder(mobile, name, leaseEndTimestamp) {
    if (!mobile) { return; }
    var digits = String(mobile).replace(/\D/g, '');
    if (!digits) { return; }
    if (digits.length > 10) {
        digits = digits.slice(-10);
    }
    var d = new Date(parseInt(leaseEndTimestamp, 10) * 1000);
    if (isNaN(d.getTime())) { d = new Date(); }
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var dateStr = d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    var text = 'Dear ' + name + ', your plan at Apna Ghar PG is expiring on ' + dateStr + '. Please renew to continue.';
    var url = 'https://wa.me/91' + digits + '?text=' + encodeURIComponent(text);
    window.open(url, '_blank');
}
</script>

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
