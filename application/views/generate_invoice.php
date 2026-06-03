<div id="content" class="content">
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Generate Plan Invoice</li>
    </ol>

    <h1 class="page-header">Generate Study Plan Invoice</h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Single Student Invoice</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open('generate_invoice/range', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="form-group">
                        <label>Student *</label>
                        <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="tenant_id">
                            <option value="">Select student</option>
                            <?php foreach ($this->db->get_where('tenant', array('status' => 1))->result_array() as $tenant) : ?>
                                <?php $seat = $tenant['room_id'] ? $this->db->get_where('room', array('room_id' => $tenant['room_id']))->row() : null; ?>
                                <?php $shift = !empty($tenant['shift_id']) ? $this->db->get_where('study_shift', array('shift_id' => $tenant['shift_id']))->row() : null; ?>
                                <option value="<?php echo html_escape($tenant['tenant_id']); ?>">
                                    <?php
                                    echo html_escape(
                                        $tenant['name']
                                        . ($seat ? ' - ' . $seat->roomnumber . ' / Seat ' . $seat->room_number : ' - No Seat')
                                        . ($shift ? ' - ' . $shift->shift_name . ' (' . $shift->timing_label . ')' : '')
                                    );
                                    ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
                            <option value="">Select status</option>
                            <option value="0">Due</option>
                            <option value="1">Paid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Plan Start Date *</label>
                        <input type="text" class="form-control" name="start" placeholder="mm/dd/yyyy" data-parsley-required="true" />
                    </div>
                    <div class="form-group">
                        <label>Due Date *</label>
                        <input name="due_date" type="text" class="form-control" placeholder="mm/dd/yyyy" data-parsley-required="true" />
                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Generate Student Invoice</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Bulk Student Invoice</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open('generate_invoice/single', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="form-group">
                        <label>Students *</label>
                        <select class="multiple-select2 form-control" multiple="multiple" name="tenants[]" data-parsley-required="true" style="width: 100%">
                            <option value="All">All Active Students</option>
                            <?php foreach ($this->db->get_where('tenant', array('status' => 1))->result_array() as $tenant) : ?>
                                <?php $seat = $tenant['room_id'] ? $this->db->get_where('room', array('room_id' => $tenant['room_id']))->row() : null; ?>
                                <option value="<?php echo html_escape($tenant['tenant_id']); ?>">
                                    <?php echo html_escape($tenant['name'] . ($seat ? ' - ' . $seat->roomnumber . ' / Seat ' . $seat->room_number : ' - No Seat')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
                            <option value="">Select status</option>
                            <option value="0">Due</option>
                            <option value="1">Paid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Plan Start Date *</label>
                        <input name="start" type="text" class="form-control" placeholder="mm/dd/yyyy" data-parsley-required="true" />
                    </div>
                    <div class="form-group">
                        <label>Due Date *</label>
                        <input name="due_date" type="text" class="form-control" placeholder="mm/dd/yyyy" data-parsley-required="true" />
                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Generate Bulk Invoices</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->
