<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Bank Statements</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_add_bank_statement');" class="btn btn-primary pull-right">
            <i class="fa fa-plus"></i> Add Bank Statement
        </a>
        Bank Statements
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-md-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <div class="panel-body">
                    <table id="data-table-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>File</th>
                                <th>Created On</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if ($this->db->table_exists('bank_statement')) {
                                $this->db->order_by('timestamp', 'desc');
                                $statements = $this->db->get('bank_statement')->result_array();
                                foreach ($statements as $row):
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo html_escape($row['month']); ?></td>
                                    <td><?php echo html_escape($row['year']); ?></td>
                                    <td>
                                        <?php if ($row['file_name'] && file_exists(FCPATH . 'uploads/bank_statements/' . $row['file_name'])): ?>
                                            <a href="<?php echo base_url(); ?>uploads/bank_statements/<?php echo $row['file_name']; ?>" target="_blank" class="btn btn-info btn-xs">
                                                <i class="fa fa-eye"></i> View File
                                            </a>
                                        <?php elseif ($row['file_name']): ?>
                                            <span class="badge badge-warning">File Missing</span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M, Y', $row['created_on']); ?></td>
                                    <td>
                                        <?php
                                        $user_type =  $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->user_type;
                                        if ($user_type == 1) {
                                            echo 'Admin';
                                        } else {
                                            $person_id = $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->person_id;
                                            echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
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
                                                <a class="dropdown-item" href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>bank_statements/remove/<?php echo $row['bank_statement_id']; ?>');">
                                                    Remove
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->
