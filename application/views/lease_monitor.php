<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('lease_monitor'); ?></li>
    </ol>
    <!-- end breadcrumb -->

    <?php
    $expired_leases = [];
    $less_than_30_leases = [];
    $less_than_60_leases = [];
    $less_than_90_leases = [];

    $tenants = $this->db->get_where('tenant')->result_array();
    foreach ($tenants as $tenant) {
        if ($tenant['lease_end']) {
            $time_differences = $tenant['lease_end'] - time();

            $i = $time_differences / (24 * 3600);

            switch ($i) {
                case $i <= 1:
                    array_push($expired_leases, $tenant);
                    break;
                case $i <= 30:
                    array_push($less_than_30_leases, $tenant);
                    break;
                case $i > 30 && $i <= 60:
                    array_push($less_than_60_leases, $tenant);
                    break;
                case $i > 60 && $i <= 90:
                    array_push($less_than_90_leases, $tenant);
                    break;
                default:
                    array_push($less_than_90_leases, $tenant);
                    break;
            }
        }
    }
    ?>

    <!-- begin page-header -->
    <h1 class="page-header">
        <?php echo $this->lang->line('expired_leases'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr style="background: #E70001;">
                                <th width="1%">#</th>
                                <th><?php echo $this->lang->line('image'); ?></th>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('status'); ?></th>
                                <th><?php echo $this->lang->line('mobile'); ?></th>
                                <th><?php echo $this->lang->line('id_type'); ?></th>
                                <th><?php echo $this->lang->line('id_number'); ?></th>
                                <th><?php echo $this->lang->line('room'); ?></th>
                                <th><?php echo $this->lang->line('emergency_person'); ?></th>
                                <th><?php echo $this->lang->line('emergency_contact'); ?></th>
                                <th><?php echo $this->lang->line('updated_on'); ?></th>
                                <th><?php echo $this->lang->line('updated_by'); ?></th>
                                <th><?php echo $this->lang->line('options'); ?></th>
                                <th width="110">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            foreach ($expired_leases as $expired_lease) :
                            ?>
                                <tr>
                                <td width="1%"><?php echo $count++; ?></td>
                                    <td class="with-img">
                                        <?php if ($expired_lease['image_link']) : ?>
                                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($expired_lease['image_link']); ?>" alt="<?php echo html_escape($expired_lease['name']); ?>" class="img-rounded height-30" />
                                        <?php else : ?>
                                            <img src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Tenant image not found" class="img-rounded height-30" />
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($expired_lease['name']); ?></td>
                                    <td>
                                        <?php
                                        if ($expired_lease['status'])
                                            echo '<span class="badge badge-primary">' . $this->lang->line('active') . '</span>';
                                        else
                                            echo '<span class="badge badge-warning">' . $this->lang->line('inactive') . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo $expired_lease['mobile_number'] ? html_escape($expired_lease['mobile_number']) : 'N/A'; ?></td>
                                    <td><?php echo $expired_lease['id_number'] ? html_escape($this->db->get_where('id_type', array('id_type_id' => $expired_lease['id_type_id']))->row()->name) : 'N/A'; ?></td>
                                    <td><?php echo $expired_lease['id_number'] ? html_escape($expired_lease['id_number']) : 'N/A'; ?></td>
                                    <td><?php echo $expired_lease['room_id'] ? html_escape($this->db->get_where('room', array('room_id' => $expired_lease['room_id']))->row()->roomnumber) . ' -> Seat ' . html_escape($this->db->get_where('room', array('room_id' => $expired_lease['room_id']))->row()->room_number) : 'N/A'; ?></td>
                                    <td><?php echo $expired_lease['emergency_person'] ? html_escape($expired_lease['emergency_person']) : 'N/A'; ?></td>
                                    <td><?php echo $expired_lease['emergency_contact'] ? html_escape($expired_lease['emergency_contact']) : 'N/A'; ?></td>
                                    <td><?php echo date('d M, Y', $expired_lease['timestamp']); ?></td>
                                    <td>
                                        <?php
                                        $user_type =  $this->db->get_where('user', array('user_id' => $expired_lease['updated_by']))->row()->user_type;
                                        if ($user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $person_id = $this->db->get_where('user', array('user_id' => $expired_lease['updated_by']))->row()->person_id;
                                            echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
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
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_tenant_details/<?php echo $expired_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('details'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_image/<?php echo $expired_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_tenant_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_id_image/<?php echo $expired_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_id_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_tenant/<?php echo $expired_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('edit'); ?>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <?php if ($expired_lease['status']) : ?>
                                                    <a class="dropdown-item" href="javascript:;" onclick="deactivate_modal('<?php echo base_url(); ?>tenants/deactivate/<?php echo $expired_lease['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('deactivate'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>tenants/remove/<?php echo $expired_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($expired_lease['mobile_number'])): ?>
                                            <button type="button" class="btn btn-success btn-xs" style="background-color: #25D366; border-color: #25D366;"
                                                    onclick="sendWhatsAppReminder('<?php echo htmlspecialchars($expired_lease['mobile_number'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($expired_lease['name'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo (int) $expired_lease['lease_end']; ?>);"
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

    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('less_than_30_leases'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr style="background: #E95420;">
                                <th width="1%">#</th>
                                <th><?php echo $this->lang->line('image'); ?></th>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('status'); ?></th>
                                <th><?php echo $this->lang->line('mobile'); ?></th>
                                <th><?php echo $this->lang->line('id_type'); ?></th>
                                <th><?php echo $this->lang->line('id_number'); ?></th>
                                <th><?php echo $this->lang->line('room'); ?></th>
                                <th><?php echo $this->lang->line('emergency_person'); ?></th>
                                <th><?php echo $this->lang->line('emergency_contact'); ?></th>
                                <th><?php echo $this->lang->line('updated_on'); ?></th>
                                <th><?php echo $this->lang->line('updated_by'); ?></th>
                                <th><?php echo $this->lang->line('options'); ?></th>
                                <th width="110">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            foreach ($less_than_30_leases as $less_than_30_lease) :
                            ?>
                                <tr>
                                <td width="1%"><?php echo $count++; ?></td>
                                    <td class="with-img">
                                        <?php if ($less_than_30_lease['image_link']) : ?>
                                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($less_than_30_lease['image_link']); ?>" alt="<?php echo html_escape($less_than_30_lease['name']); ?>" class="img-rounded height-30" />
                                        <?php else : ?>
                                            <img src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Tenant image not found" class="img-rounded height-30" />
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($less_than_30_lease['name']); ?></td>
                                    <td>
                                        <?php
                                        if ($less_than_30_lease['status'])
                                            echo '<span class="badge badge-primary">' . $this->lang->line('active') . '</span>';
                                        else
                                            echo '<span class="badge badge-warning">' . $this->lang->line('inactive') . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo $less_than_30_lease['mobile_number'] ? html_escape($less_than_30_lease['mobile_number']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_30_lease['id_number'] ? html_escape($this->db->get_where('id_type', array('id_type_id' => $less_than_30_lease['id_type_id']))->row()->name) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_30_lease['id_number'] ? html_escape($less_than_30_lease['id_number']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_30_lease['room_id'] ? html_escape($this->db->get_where('room', array('room_id' => $less_than_30_lease['room_id']))->row()->roomnumber) . ' -> Seat ' . html_escape($this->db->get_where('room', array('room_id' => $less_than_30_lease['room_id']))->row()->room_number) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_30_lease['emergency_person'] ? html_escape($less_than_30_lease['emergency_person']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_30_lease['emergency_contact'] ? html_escape($less_than_30_lease['emergency_contact']) : 'N/A'; ?></td>
                                    <td><?php echo date('d M, Y', $less_than_30_lease['timestamp']); ?></td>
                                    <td>
                                        <?php
                                        $user_type =  $this->db->get_where('user', array('user_id' => $less_than_30_lease['updated_by']))->row()->user_type;
                                        if ($user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $person_id = $this->db->get_where('user', array('user_id' => $less_than_30_lease['updated_by']))->row()->person_id;
                                            echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
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
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_tenant_details/<?php echo $less_than_30_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('details'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_image/<?php echo $less_than_30_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_tenant_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_id_image/<?php echo $less_than_30_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_id_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_tenant/<?php echo $less_than_30_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('edit'); ?>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <?php if ($less_than_30_lease['status']) : ?>
                                                    <a class="dropdown-item" href="javascript:;" onclick="deactivate_modal('<?php echo base_url(); ?>tenants/deactivate/<?php echo $less_than_30_lease['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('deactivate'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>tenants/remove/<?php echo $less_than_30_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $is_expiring_soon_l30 = !empty($less_than_30_lease['lease_end']) && (($less_than_30_lease['lease_end'] - time()) <= (7 * 24 * 60 * 60));
                                        if ($is_expiring_soon_l30 && !empty($less_than_30_lease['mobile_number'])): ?>
                                            <button type="button" class="btn btn-success btn-xs" style="background-color: #25D366; border-color: #25D366;"
                                                    onclick="sendWhatsAppReminder('<?php echo htmlspecialchars($less_than_30_lease['mobile_number'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($less_than_30_lease['name'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo (int) $less_than_30_lease['lease_end']; ?>);"
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

    <!-- begin page-header -->
    <h1 class="page-header">
        <?php echo $this->lang->line('less_than_60_leases'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr style="background: #1F70C1;">
                                <th width="1%">#</th>
                                <th><?php echo $this->lang->line('image'); ?></th>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('status'); ?></th>
                                <th><?php echo $this->lang->line('mobile'); ?></th>
                                <th><?php echo $this->lang->line('id_type'); ?></th>
                                <th><?php echo $this->lang->line('id_number'); ?></th>
                                <th><?php echo $this->lang->line('room'); ?></th>
                                <th><?php echo $this->lang->line('emergency_person'); ?></th>
                                <th><?php echo $this->lang->line('emergency_contact'); ?></th>
                                <th><?php echo $this->lang->line('updated_on'); ?></th>
                                <th><?php echo $this->lang->line('updated_by'); ?></th>
                                <th><?php echo $this->lang->line('options'); ?></th>
                                <th width="110">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            foreach ($less_than_60_leases as $less_than_60_lease) :
                            ?>
                                <tr>
                                <td width="1%"><?php echo $count++; ?></td>
                                    <td class="with-img">
                                        <?php if ($less_than_60_lease['image_link']) : ?>
                                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($less_than_60_lease['image_link']); ?>" alt="<?php echo html_escape($less_than_60_lease['name']); ?>" class="img-rounded height-30" />
                                        <?php else : ?>
                                            <img src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Tenant image not found" class="img-rounded height-30" />
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($less_than_60_lease['name']); ?></td>
                                    <td>
                                        <?php
                                        if ($less_than_60_lease['status'])
                                            echo '<span class="badge badge-primary">' . $this->lang->line('active') . '</span>';
                                        else
                                            echo '<span class="badge badge-warning">' . $this->lang->line('inactive') . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo $less_than_60_lease['mobile_number'] ? html_escape($less_than_60_lease['mobile_number']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_60_lease['id_number'] ? html_escape($this->db->get_where('id_type', array('id_type_id' => $less_than_60_lease['id_type_id']))->row()->name) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_60_lease['id_number'] ? html_escape($less_than_60_lease['id_number']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_60_lease['room_id'] ? html_escape($this->db->get_where('room', array('room_id' => $less_than_60_lease['room_id']))->row()->roomnumber) . ' -> Seat ' . html_escape($this->db->get_where('room', array('room_id' => $less_than_60_lease['room_id']))->row()->room_number) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_60_lease['emergency_person'] ? html_escape($less_than_60_lease['emergency_person']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_60_lease['emergency_contact'] ? html_escape($less_than_60_lease['emergency_contact']) : 'N/A'; ?></td>
                                    <td><?php echo date('d M, Y', $less_than_60_lease['timestamp']); ?></td>
                                    <td>
                                        <?php
                                        $user_type =  $this->db->get_where('user', array('user_id' => $less_than_60_lease['updated_by']))->row()->user_type;
                                        if ($user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $person_id = $this->db->get_where('user', array('user_id' => $less_than_60_lease['updated_by']))->row()->person_id;
                                            echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
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
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_tenant_details/<?php echo $less_than_60_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('details'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_image/<?php echo $less_than_60_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_tenant_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_id_image/<?php echo $less_than_60_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_id_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_tenant/<?php echo $less_than_60_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('edit'); ?>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <?php if ($less_than_60_lease['status']) : ?>
                                                    <a class="dropdown-item" href="javascript:;" onclick="deactivate_modal('<?php echo base_url(); ?>tenants/deactivate/<?php echo $less_than_60_lease['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('deactivate'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>tenants/remove/<?php echo $less_than_60_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $is_expiring_soon_l60 = !empty($less_than_60_lease['lease_end']) && (($less_than_60_lease['lease_end'] - time()) <= (7 * 24 * 60 * 60));
                                        if ($is_expiring_soon_l60 && !empty($less_than_60_lease['mobile_number'])): ?>
                                            <button type="button" class="btn btn-success btn-xs" style="background-color: #25D366; border-color: #25D366;"
                                                    onclick="sendWhatsAppReminder('<?php echo htmlspecialchars($less_than_60_lease['mobile_number'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($less_than_60_lease['name'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo (int) $less_than_60_lease['lease_end']; ?>);"
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

    <!-- begin page-header -->
    <h1 class="page-header">
        <?php echo $this->lang->line('less_than_90_leases'); ?>
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr style="background: #4FCE5D;">
                                <th width="1%">#</th>
                                <th><?php echo $this->lang->line('image'); ?></th>
                                <th><?php echo $this->lang->line('name'); ?></th>
                                <th><?php echo $this->lang->line('status'); ?></th>
                                <th><?php echo $this->lang->line('mobile'); ?></th>
                                <th><?php echo $this->lang->line('id_type'); ?></th>
                                <th><?php echo $this->lang->line('id_number'); ?></th>
                                <th><?php echo $this->lang->line('room'); ?></th>
                                <th><?php echo $this->lang->line('emergency_person'); ?></th>
                                <th><?php echo $this->lang->line('emergency_contact'); ?></th>
                                <th><?php echo $this->lang->line('updated_on'); ?></th>
                                <th><?php echo $this->lang->line('updated_by'); ?></th>
                                <th><?php echo $this->lang->line('options'); ?></th>
                                <th width="110">WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            foreach ($less_than_90_leases as $less_than_90_lease) :
                            ?>
                                <tr>
                                <td width="1%"><?php echo $count++; ?></td>
                                    <td class="with-img">
                                        <?php if ($less_than_90_lease['image_link']) : ?>
                                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($less_than_90_lease['image_link']); ?>" alt="<?php echo html_escape($less_than_90_lease['name']); ?>" class="img-rounded height-30" />
                                        <?php else : ?>
                                            <img src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Tenant image not found" class="img-rounded height-30" />
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($less_than_90_lease['name']); ?></td>
                                    <td>
                                        <?php
                                        if ($less_than_90_lease['status'])
                                            echo '<span class="badge badge-primary">' . $this->lang->line('active') . '</span>';
                                        else
                                            echo '<span class="badge badge-warning">' . $this->lang->line('inactive') . '</span>';
                                        ?>
                                    </td>
                                    <td><?php echo $less_than_90_lease['mobile_number'] ? html_escape($less_than_90_lease['mobile_number']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_90_lease['id_number'] ? html_escape($this->db->get_where('id_type', array('id_type_id' => $less_than_90_lease['id_type_id']))->row()->name) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_90_lease['id_number'] ? html_escape($less_than_90_lease['id_number']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_90_lease['room_id'] ? html_escape($this->db->get_where('room', array('room_id' => $less_than_90_lease['room_id']))->row()->roomnumber) . ' -> Seat ' . html_escape($this->db->get_where('room', array('room_id' => $less_than_90_lease['room_id']))->row()->room_number) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_90_lease['emergency_person'] ? html_escape($less_than_90_lease['emergency_person']) : 'N/A'; ?></td>
                                    <td><?php echo $less_than_90_lease['emergency_contact'] ? html_escape($less_than_90_lease['emergency_contact']) : 'N/A'; ?></td>
                                    <td><?php echo date('d M, Y', $less_than_90_lease['timestamp']); ?></td>
                                    <td>
                                        <?php
                                        $user_type =  $this->db->get_where('user', array('user_id' => $less_than_90_lease['updated_by']))->row()->user_type;
                                        if ($user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $person_id = $this->db->get_where('user', array('user_id' => $less_than_90_lease['updated_by']))->row()->person_id;
                                            echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
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
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_show_tenant_details/<?php echo $less_than_90_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('details'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_image/<?php echo $less_than_90_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_tenant_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_change_tenant_id_image/<?php echo $less_than_90_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('change_id_image'); ?>
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_tenant/<?php echo $less_than_90_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('edit'); ?>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <?php if ($less_than_90_lease['status']) : ?>
                                                    <a class="dropdown-item" href="javascript:;" onclick="deactivate_modal('<?php echo base_url(); ?>tenants/deactivate/<?php echo $less_than_90_lease['tenant_id']; ?>');">
                                                    <?php echo $this->lang->line('deactivate'); ?>
                                                    </a>
                                                <?php endif; ?>
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>tenants/remove/<?php echo $less_than_90_lease['tenant_id']; ?>');">
                                                <?php echo $this->lang->line('remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $is_expiring_soon_l90 = !empty($less_than_90_lease['lease_end']) && (($less_than_90_lease['lease_end'] - time()) <= (7 * 24 * 60 * 60));
                                        if ($is_expiring_soon_l90 && !empty($less_than_90_lease['mobile_number'])): ?>
                                            <button type="button" class="btn btn-success btn-xs" style="background-color: #25D366; border-color: #25D366;"
                                                    onclick="sendWhatsAppReminder('<?php echo htmlspecialchars($less_than_90_lease['mobile_number'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($less_than_90_lease['name'], ENT_QUOTES, 'UTF-8'); ?>', <?php echo (int) $less_than_90_lease['lease_end']; ?>);"
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
