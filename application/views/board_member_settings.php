<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active"><?php echo $this->lang->line('board_member_settings'); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    <?php echo $this->lang->line('board_member_settings_header'); ?>
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
                                <th><?php echo $this->lang->line('image'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('name'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('position'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('serial'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_on'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('updated_by'); ?></th>
                                <th class="text-nowrap"><?php echo $this->lang->line('options'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            $this->db->order_by('timestamp', 'desc');
                            $board_members = $this->db->get('board_member')->result_array();
                            foreach ($board_members as $row) :
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td class="with-img">
                                        <?php if ($row['image']) : ?>
                                            <span><img src="<?php echo base_url(); ?>uploads/board_members/<?php echo html_escape($row['image']); ?>" alt="<?php echo html_escape($row['name']); ?>" class="img-rounded height-30" /></span>
                                        <?php else : ?>
                                            <span><img src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Board member image not found" class="img-rounded height-30" /></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($row['name']); ?></td>
                                    <td><?php echo html_escape($row['position']); ?></td>
                                    <td><?php echo html_escape($row['serial']); ?></td>
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
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-white btn-xs"><?php echo $this->lang->line('action'); ?></button>
                                            <button type="button" class="btn btn-white btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_board_member/<?php echo $row['board_member_id']; ?>');">
                                                <?php echo $this->lang->line('edit'); ?>
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
        <!-- end col-9 -->
        <!-- begin col-3 -->
        <div class="col-md-3">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <?php echo form_open_multipart('board_member_settings/add', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('board_member_name'); ?> *</label>
                        <input type="text" name="name" placeholder="<?php echo $this->lang->line('board_member_name_placeholder'); ?>" class="form-control" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('board_member_image'); ?></label>
                        <br>
                        <img id="image-preview" width="90px" src="<?php echo base_url('assets/img/tenant.png'); ?>" class="media-object" />
                        <br>
                        <br>
                        <span class="btn btn-primary fileinput-button">
                            <i class="fa fa-plus"></i>
                            <span><?php echo $this->lang->line('add_file'); ?></span>
                            <input class="form-control" type="file" name="image" onchange="readURL(this);">
                        </span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('board_member_position'); ?> *</label>
                        <input type="text" name="position" placeholder="<?php echo $this->lang->line('board_member_position_placeholder'); ?>" class="form-control" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('board_member_serial'); ?> *</label>
                        <input type="text" data-parsley-type="number" name="serial" placeholder="<?php echo $this->lang->line('board_member_serial_placeholder'); ?>" class="form-control" data-parsley-required="true" min="0">
                    </div>

                    <button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('submit'); ?></button>
                    <?php echo form_close(); ?>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-3 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->

<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>