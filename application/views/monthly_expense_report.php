<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Monthly Expense Report</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Monthly Expense Report</h1>
    <!-- end page-header -->

    <div class="row m-b-20">
        <div class="col-lg-9">
            <form method="get" action="<?php echo base_url(); ?>monthly_expense_report" class="form-inline">
                <div class="form-group m-r-10">
                    <label class="m-r-10">Month:</label>
                    <select name="month" class="form-control">
                        <?php foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $m): ?>
                            <option value="<?php echo $m; ?>" <?php echo ($m === $month) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group m-r-10">
                    <label class="m-r-10">Year:</label>
                    <select name="year" class="form-control">
                        <?php for ($y = (int)date('Y') - 5; $y <= (int)date('Y') + 1; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo ($y === (int)$year) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?php echo base_url(); ?>download_monthly_expense_report_csv/<?php echo urlencode($month); ?>/<?php echo (int)$year; ?>" class="btn btn-success m-l-5"><i class="fa fa-file-excel"></i> Export CSV</a>
                <a href="<?php echo base_url(); ?>download_monthly_expense_report_pdf/<?php echo urlencode($month); ?>/<?php echo (int)$year; ?>" class="btn btn-danger m-l-5" target="_blank"><i class="fa fa-file-pdf"></i> Export PDF</a>
            </form>
        </div>
    </div>

    <?php
    $expenses = $this->db->get_where('expense', array('month' => $month, 'year' => (int)$year))->result_array();
    $total = 0;
    ?>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title">Expenses for <?php echo html_escape($month . ' ' . $year); ?></h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="text-right">Amount (Rs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No expenses found for this period.</td></tr>
                        <?php else: $i=1; foreach($expenses as $e):
                            $total += (float)$e['amount']; ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $e['timestamp'] ? date('d M, Y', $e['timestamp']) : '-'; ?></td>
                                <td><?php echo html_escape($e['name']); ?></td>
                                <td><?php echo html_escape($e['description'] ?: '-'); ?></td>
                                <td class="text-right"><?php echo number_format($e['amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="active">
                            <th colspan="4" class="text-right">Total:</th>
                            <th class="text-right"><?php echo number_format($total, 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->
