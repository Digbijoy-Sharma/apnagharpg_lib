<div id="content" class="content">
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Library Plan Settings</li>
    </ol>

    <h1 class="page-header">Library Plan Settings</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Shift timings and editable plan pricing</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open('library_plan_settings/update', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Shift</th>
                                    <th>Timing Label</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Per Day Plan</th>
                                    <th>Monthly Plan</th>
                                    <th>3 Month Plan</th>
                                    <th>6 Month Plan</th>
                                    <th>12 Month Plan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->db->order_by('shift_id', 'asc')->get('study_shift')->result_array() as $shift) : ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="shift_id[]" value="<?php echo html_escape($shift['shift_id']); ?>">
                                            <input type="text" class="form-control" name="shift_name[]" value="<?php echo html_escape($shift['shift_name']); ?>" required>
                                        </td>
                                        <td><input type="text" class="form-control" name="timing_label[]" value="<?php echo html_escape($shift['timing_label']); ?>" required></td>
                                        <td><input type="text" class="form-control" name="start_time[]" value="<?php echo html_escape($shift['start_time']); ?>" required></td>
                                        <td><input type="text" class="form-control" name="end_time[]" value="<?php echo html_escape($shift['end_time']); ?>" required></td>
                                        <td><input type="number" step="0.01" min="0" class="form-control" name="daily_price[]" value="<?php echo html_escape($shift['daily_price']); ?>" required></td>
                                        <td><input type="number" step="0.01" min="0" class="form-control" name="monthly_price[]" value="<?php echo html_escape($shift['monthly_price']); ?>" required></td>
                                        <td><input type="number" step="0.01" min="0" class="form-control" name="quarterly_price[]" value="<?php echo html_escape($shift['quarterly_price']); ?>" required></td>
                                        <td><input type="number" step="0.01" min="0" class="form-control" name="half_yearly_price[]" value="<?php echo html_escape($shift['half_yearly_price']); ?>" required></td>
                                        <td><input type="number" step="0.01" min="0" class="form-control" name="yearly_price[]" value="<?php echo html_escape($shift['yearly_price']); ?>" required></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Plans</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
