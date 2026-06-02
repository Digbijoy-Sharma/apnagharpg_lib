<!-- begin #content -->
<div id="content" class="content">
    <?php
    $selected_date = isset($selected_date) && $selected_date ? $selected_date : date('Y-m-d');
    $selected_year = isset($selected_year) && $selected_year ? $selected_year : date('Y');
    $cash_book_daily_totals = isset($cash_book_daily_totals) && is_array($cash_book_daily_totals) ? $cash_book_daily_totals : array(
        'manual_income' => 0,
        'manual_expense' => 0,
        'linked_expense' => 0,
        'total_expense' => 0,
        'balance' => 0
    );
    $cash_book_entries = isset($cash_book_entries) && is_array($cash_book_entries) ? $cash_book_entries : array();
    $cash_book_monthly_summary = isset($cash_book_monthly_summary) && is_array($cash_book_monthly_summary) ? $cash_book_monthly_summary : array();
    $currency = $this->db->get_where('setting', array('name' => 'currency'))->row()->content;
    $selected_month_number = (int) date('n', strtotime($selected_date));
    $selected_month_label = date('F Y', strtotime($selected_date));
    $selected_month_summary = array(
        'manual_income' => 0,
        'manual_expense' => 0,
        'linked_expense' => 0,
        'total_expense' => 0,
        'balance' => 0
    );
    $year_manual_income = 0;
    $year_manual_expense = 0;
    $year_linked_expense = 0;
    $year_balance = 0;

    foreach ($cash_book_monthly_summary as $summary_row) {
        $year_manual_income += $summary_row['manual_income'];
        $year_manual_expense += $summary_row['manual_expense'];
        $year_linked_expense += $summary_row['linked_expense'];
        $year_balance += $summary_row['balance'];

        if ((int) $summary_row['month_number'] === $selected_month_number) {
            $selected_month_summary = $summary_row;
        }
    }
    ?>

    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>account"><?php echo $this->lang->line('account'); ?></a></li>
        <li class="breadcrumb-item active">Daily Cash Book</li>
    </ol>

    <h1 class="page-header">
        Daily Cash Book
        <small>Track daily income, daily expenses, linked expense-module records, and monthly closing balance.</small>
    </h1>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-green">
                <div class="stats-icon"><i class="fa fa-arrow-circle-down"></i></div>
                <div class="stats-info">
                    <h4>Daily Income</h4>
                    <p><?php echo $currency . ' ' . number_format($cash_book_daily_totals['manual_income'], 2); ?></p>
                    <small><?php echo date('d M, Y', strtotime($selected_date)); ?></small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-red">
                <div class="stats-icon"><i class="fa fa-arrow-circle-up"></i></div>
                <div class="stats-info">
                    <h4>Daily Expense</h4>
                    <p><?php echo $currency . ' ' . number_format($cash_book_daily_totals['total_expense'], 2); ?></p>
                    <small>Manual + linked expense module</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats <?php echo $cash_book_daily_totals['balance'] >= 0 ? 'bg-blue' : 'bg-orange'; ?>">
                <div class="stats-icon"><i class="fa fa-wallet"></i></div>
                <div class="stats-info">
                    <h4>Daily Balance</h4>
                    <p><?php echo $currency . ' ' . number_format($cash_book_daily_totals['balance'], 2); ?></p>
                    <small>Income - total expenses</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-purple">
                <div class="stats-icon"><i class="fa fa-calendar-alt"></i></div>
                <div class="stats-info">
                    <h4><?php echo $selected_month_label; ?></h4>
                    <p><?php echo $currency . ' ' . number_format($selected_month_summary['balance'], 2); ?></p>
                    <small>Selected month balance</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Filter Cash Book</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open('daily_cash_book', array('method' => 'get')); ?>
                    <div class="form-group">
                        <label>Select Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo html_escape($selected_date); ?>">
                    </div>
                    <div class="form-group">
                        <label>Summary Year</label>
                        <select name="year" class="form-control default-select2">
                            <?php for ($year = date('Y') - 4; $year <= date('Y') + 4; $year++) : ?>
                                <option value="<?php echo $year; ?>" <?php if ((int) $selected_year === (int) $year) echo 'selected'; ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Show Cash Book</button>
                    <?php echo form_close(); ?>
                </div>
            </div>

            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Daily Breakdown</h4>
                </div>
                <div class="panel-body">
                    <div class="m-b-10">
                        <strong>Manual Income:</strong>
                        <span class="pull-right"><?php echo $currency . ' ' . number_format($cash_book_daily_totals['manual_income'], 2); ?></span>
                    </div>
                    <div class="m-b-10">
                        <strong>Manual Expense:</strong>
                        <span class="pull-right"><?php echo $currency . ' ' . number_format($cash_book_daily_totals['manual_expense'], 2); ?></span>
                    </div>
                    <div class="m-b-10">
                        <strong>Expense Module:</strong>
                        <span class="pull-right"><?php echo $currency . ' ' . number_format($cash_book_daily_totals['linked_expense'], 2); ?></span>
                    </div>
                    <hr>
                    <div class="m-b-10">
                        <strong>Total Expense:</strong>
                        <span class="pull-right"><?php echo $currency . ' ' . number_format($cash_book_daily_totals['total_expense'], 2); ?></span>
                    </div>
                    <div>
                        <strong>Closing Balance:</strong>
                        <span class="pull-right"><?php echo $currency . ' ' . number_format($cash_book_daily_totals['balance'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Add Daily Entry</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open('daily_cash_book/add', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="form-group">
                        <label>Entry Date *</label>
                        <input type="date" name="entry_date" class="form-control" value="<?php echo html_escape($selected_date); ?>" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label>Entry Type *</label>
                        <select name="entry_type" class="form-control default-select2" data-parsley-required="true">
                            <option value="">Select Type</option>
                            <option value="income">Daily Income</option>
                            <option value="expense">Daily Expense</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Title / Head *</label>
                        <input type="text" name="title" class="form-control" placeholder="Example: Cash Sale / Milk Purchase / Office Cash" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label>Amount (<?php echo $currency; ?>) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="Enter amount" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" style="resize:none;" placeholder="Add notes, vendor details, or income remarks"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Add To Cash Book</button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Expense Module Link</h4>
                </div>
                <div class="panel-body">
                    <div class="note note-info">
                        <p class="m-b-5"><strong>Linked automatically:</strong></p>
                        <p class="m-b-0">All records added in the existing Expenses module are automatically counted in Daily Cash Book expenses. They appear in the daily ledger as linked entries and also contribute to the month-wise summary.</p>
                    </div>
                    <a href="<?php echo base_url(); ?>expenses" class="btn btn-primary btn-block m-b-10">
                        <i class="fa fa-external-link-alt"></i> Open Expense Module
                    </a>
                    <div class="note note-yellow m-b-0">
                        <p class="m-b-5"><strong>Year <?php echo $selected_year; ?> Overview</strong></p>
                        <p class="m-b-5">Manual Income: <?php echo $currency . ' ' . number_format($year_manual_income, 2); ?></p>
                        <p class="m-b-5">Manual Expense: <?php echo $currency . ' ' . number_format($year_manual_expense, 2); ?></p>
                        <p class="m-b-5">Expense Module: <?php echo $currency . ' ' . number_format($year_linked_expense, 2); ?></p>
                        <p class="m-b-0">Closing Balance: <?php echo $currency . ' ' . number_format($year_balance, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Daily Ledger for <?php echo date('d M, Y', strtotime($selected_date)); ?></h4>
                </div>
                <div class="panel-body">
                    <table id="data-table-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Source</th>
                                <th>Head</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Created On</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 1; ?>
                            <?php foreach ($cash_book_entries as $entry) : ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td>
                                        <?php if ($entry['source_key'] == 'expense_module') : ?>
                                            <span class="badge badge-warning"><?php echo $entry['source_label']; ?></span>
                                        <?php else : ?>
                                            <span class="badge badge-primary"><?php echo $entry['source_label']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape($entry['title']); ?></td>
                                    <td>
                                        <?php if ($entry['entry_type'] == 'income') : ?>
                                            <span class="text-success"><strong>Income</strong></span>
                                        <?php else : ?>
                                            <span class="text-danger"><strong>Expense</strong></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $currency . ' ' . number_format((float) $entry['amount'], 2); ?></td>
                                    <td><?php echo $entry['description'] ? html_escape($entry['description']) : 'N/A'; ?></td>
                                    <td><?php echo !empty($entry['created_on']) ? date('d M, Y h:i A', $entry['created_on']) : 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        if (!empty($entry['created_by']) && $this->db->get_where('user', array('user_id' => $entry['created_by']))->num_rows() > 0) {
                                            $user_row = $this->db->get_where('user', array('user_id' => $entry['created_by']))->row();
                                            if ($user_row->user_type == 1) {
                                                echo 'Admin';
                                            } elseif ($user_row->user_type == 2 && $this->db->get_where('staff', array('staff_id' => $user_row->person_id))->num_rows() > 0) {
                                                echo html_escape($this->db->get_where('staff', array('staff_id' => $user_row->person_id))->row()->name);
                                            } else {
                                                echo 'User #' . html_escape($entry['created_by']);
                                            }
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($entry['can_remove']) : ?>
                                            <a href="javascript:;" onclick="confirm_modal('<?php echo base_url(); ?>daily_cash_book/remove/<?php echo $entry['cash_book_id']; ?>?date=<?php echo urlencode($selected_date); ?>&year=<?php echo urlencode($selected_year); ?>');" class="btn btn-danger btn-xs">Remove</a>
                                        <?php else : ?>
                                            <a href="javascript:;" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_expense/<?php echo $entry['source_id']; ?>');" class="btn btn-warning btn-xs">Linked Expense</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Monthly Summary for <?php echo html_escape($selected_year); ?></h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Month</th>
                                    <th>Manual Income</th>
                                    <th>Manual Expense</th>
                                    <th>Expense Module</th>
                                    <th>Total Expense</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $month_count = 1; ?>
                                <?php foreach ($cash_book_monthly_summary as $summary_row) : ?>
                                    <tr>
                                        <td><?php echo $month_count++; ?></td>
                                        <td><?php echo html_escape($summary_row['month_name']); ?></td>
                                        <td><?php echo $currency . ' ' . number_format($summary_row['manual_income'], 2); ?></td>
                                        <td><?php echo $currency . ' ' . number_format($summary_row['manual_expense'], 2); ?></td>
                                        <td><?php echo $currency . ' ' . number_format($summary_row['linked_expense'], 2); ?></td>
                                        <td><?php echo $currency . ' ' . number_format($summary_row['total_expense'], 2); ?></td>
                                        <td class="<?php echo $summary_row['balance'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            <strong><?php echo $currency . ' ' . number_format($summary_row['balance'], 2); ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->
