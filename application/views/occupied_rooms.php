<style>
    .occupied-rooms-table-wrap {
        width: 100%;
        max-height: 70vh;
        overflow: auto;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #fff;
        -webkit-overflow-scrolling: touch;
    }
    .occupied-rooms-table-wrap > table {
        margin-bottom: 0;
        min-width: 1500px;
    }
    .occupied-rooms-table-wrap > table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f5f5f5;
        box-shadow: inset 0 -1px 0 #ddd;
    }
    .occupied-rooms-table-wrap > table .btn-group {
        white-space: nowrap;
    }
</style>
<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('rooms'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <a href="<?php echo base_url(); ?>add_room">
            <button type="button" class="btn btn-inverse"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_room'); ?></button>
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
                    <div class="occupied-rooms-table-wrap">
                        <table id="data-table-buttons" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="1%">#</th>
                                    <th class="text-nowrap">Study Area</th>
                                    <th class="text-nowrap">Seat No</th>
                                    <th class="text-nowrap"><?php echo $this->lang->line('status'); ?></th>
                                    <th class="text-nowrap">Student Name</th>
                                    <th class="text-nowrap">Mobile Number</th>
                                    <th class="text-nowrap">Emergency Person</th>
                                    <th class="text-nowrap">Emergency Contact</th>
                                    <th class="text-nowrap">ID Type</th>
                                    <th class="text-nowrap">ID Number</th>
                                    <th class="text-nowrap">Floor / Section</th>
                                    <th class="text-nowrap"><?php echo $this->lang->line('remarks'); ?></th>
                                    <th class="text-nowrap"><?php echo $this->lang->line('updated_on'); ?></th>
                                    <th class="text-nowrap"><?php echo $this->lang->line('updated_by'); ?></th>
                                    <th class="text-nowrap"><?php echo $this->lang->line('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                $this->db->order_by('timestamp', 'desc');
                                $rooms = $this->db->get_where('room', array('status' => 1))->result_array();
                                foreach ($rooms as $room) :
                                    $active_tenant = $this->db
                                        ->order_by('tenant_id', 'asc')
                                        ->get_where('tenant', array('room_id' => $room['room_id'], 'status' => 1), 1)
                                        ->row();
                                    $id_type_name = '';
                                    if ($active_tenant && !empty($active_tenant->id_type_id)) {
                                        $id_type_row = $this->db->get_where('id_type', array('id_type_id' => $active_tenant->id_type_id))->row();
                                        if ($id_type_row) {
                                            $id_type_name = $id_type_row->name;
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td width="1%"><?php echo $count++; ?></td>
                                        <td><?php echo html_escape($room['roomnumber']); ?></td>
                                        <td><?php echo html_escape($room['room_number']); ?></td>
                                        <td>
                                            <?php
                                            if ($room['status'])
                                                echo '<span class="badge badge-primary">' . $this->lang->line('occupied') . '</span>';
                                            else
                                                echo '<span class="badge badge-warning">' . $this->lang->line('unoccupied') . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($active_tenant && !empty($active_tenant->name)) {
                                                echo '<strong>' . html_escape($active_tenant->name) . '</strong>';
                                            } else {
                                                echo '<span class="text-muted">N/A</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo ($active_tenant && !empty($active_tenant->mobile_number)) ? html_escape($active_tenant->mobile_number) : '<span class="text-muted">N/A</span>'; ?>
                                        </td>
                                        <td>
                                            <?php echo ($active_tenant && !empty($active_tenant->emergency_person)) ? html_escape($active_tenant->emergency_person) : '<span class="text-muted">N/A</span>'; ?>
                                        </td>
                                        <td>
                                            <?php echo ($active_tenant && !empty($active_tenant->emergency_contact)) ? html_escape($active_tenant->emergency_contact) : '<span class="text-muted">N/A</span>'; ?>
                                        </td>
                                        <td><?php echo $id_type_name ? html_escape($id_type_name) : '<span class="text-muted">N/A</span>'; ?></td>
                                        <td>
                                            <?php echo ($active_tenant && !empty($active_tenant->id_number)) ? html_escape($active_tenant->id_number) : '<span class="text-muted">N/A</span>'; ?>
                                        </td>
                                        <td><?php echo $room['floor'] ? html_escape($room['floor']) : 'N/A'; ?></td>
                                        <td><?php echo $room['remarks'] ? html_escape($room['remarks']) : 'N/A'; ?></td>
                                        <td><?php echo date('d M, Y', $room['timestamp']); ?></td>
                                        <td>
                                            <?php
                                            $user_type =  $this->db->get_where('user', array('user_id' => $room['updated_by']))->row()->user_type;
                                            if ($user_type == 1) {
                                                echo 'Admin';
                                            } else {
                                                $person_id = $this->db->get_where('user', array('user_id' => $room['updated_by']))->row()->person_id;
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
                                                    <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_room/<?php echo $room['room_id']; ?>');">
                                                    <?php echo $this->lang->line('edit'); ?>
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="vacant_modal('<?php echo base_url(); ?>rooms/vacant/<?php echo $room['room_id']; ?>');">
                                                    <?php echo $this->lang->line('vacant_room'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row -->
</div>
