<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Annual Report (Financial Year)</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Annual Report — Financial Year <?php echo html_escape($fy); ?></h1>
    <!-- end page-header -->

    <?php
    // Parse FY label e.g. "2025-2026" -> start year = 2025 (April 1) -> end year = 2026 (March 31)
    list($start_year, $end_year) = array_map('intval', explode('-', $fy));
    $fy_start_str = $start_year . '-04-01';
    $fy_end_str   = $end_year   . '-03-31';
    $fy_start_ts  = strtotime($fy_start_str . ' 00:00:00');
    $fy_end_ts    = strtotime($fy_end_str   . ' 23:59:59');

    // Build a list of (month, year) pairs for the 12 FY months
    $fy_months = [];
    for ($i = 0; $i < 12; $i++) {
        $m = ($i % 12) + 4; // 4..15
        if ($m > 12) { $m -= 12; $y = $start_year + 1; } else { $y = $start_year; }
        $fy_months[] = ['month' => date('F', mktime(0,0,0,$m,1,2000)), 'year' => $y];
    }

    // ---- Total Sales (from tenant_rent) ----
    $total_sales_rent = 0;
    $total_sales_services = 0;
    $total_sales_late = 0;
    $total_sales_collected = 0;
    $sales_invoice_count = 0;
    foreach ($fy_months as $mm) {
        $rows = $this->db->get_where('tenant_rent', array('month' => $mm['month'], 'year' => (int)$mm['year']))->result_array();
        foreach ($rows as $r) {
            $inv_id = (int)$r['invoice_id'];
            $amt    = (float)$r['amount'];
            $status = (int)$r['status'];
            $inv    = $this->db->get_where('invoice', array('invoice_id' => $inv_id))->row();
            $late   = $inv ? (float)$inv->late_fee : 0;
            $svc_total = 0;
            $svcs = $this->db->get_where('invoice_service', array('invoice_id' => $inv_id))->result_array();
            foreach ($svcs as $s) {
                $svc = $this->db->get_where('service', array('service_id' => $s['service_id']))->row();
                if ($svc) $svc_total += (float)$svc->cost;
            }
            $this->db->select_sum('amount');
            $this->db->from('invoice_transaction');
            $this->db->where('invoice_id', $inv_id);
            $paid = (float)($this->db->get()->row()->amount ?? 0);
            if ($paid <= 0 && $status == 1) $paid = $amt + $svc_total + $late;

            $total_sales_rent      += $amt;
            $total_sales_services  += $svc_total;
            $total_sales_late      += $late;
            $total_sales_collected += $paid;
            $sales_invoice_count++;
        }
    }
    $total_sales_gross = $total_sales_rent + $total_sales_services + $total_sales_late;

    // ---- Purchases (by purchase_date) ----
    $this->db->select_sum('amount');
    $this->db->from('purchase');
    $this->db->where('purchase_date >=', $fy_start_str);
    $this->db->where('purchase_date <=', $fy_end_str);
    $total_purchases = (float)($this->db->get()->row()->amount ?? 0);

    $purchases = $this->db->get_where('purchase', array())->result_array();
    $purchases_in_fy = [];
    foreach ($purchases as $p) {
        $pd = $p['purchase_date'];
        if ($pd && strtotime($pd) >= $fy_start_ts && strtotime($pd) <= $fy_end_ts) {
            $purchases_in_fy[] = $p;
        }
    }

    // ---- Expenses (by month/year) ----
    $total_expenses = 0;
    $expenses_in_fy = [];
    foreach ($fy_months as $mm) {
        $rows = $this->db->get_where('expense', array('month' => $mm['month'], 'year' => (int)$mm['year']))->result_array();
        foreach ($rows as $r) {
            $total_expenses += (float)$r['amount'];
            $expenses_in_fy[] = $r;
        }
    }

    // ---- Salary (by month/year) ----
    $total_salary = 0;
    $salary_in_fy = [];
    foreach ($fy_months as $mm) {
        $rows = $this->db->get_where('staff_salary', array('month' => $mm['month'], 'year' => (int)$mm['year']))->result_array();
        foreach ($rows as $r) {
            $total_salary += (float)$r['amount'];
            $salary_in_fy[] = $r;
        }
    }

    $total_outgoing = $total_purchases + $total_expenses + $total_salary;
    $net_position   = $total_sales_collected - $total_outgoing;
    ?>

    <!-- Filter row -->
    <div class="row m-b-20">
        <div class="col-lg-9">
            <form method="get" action="<?php echo base_url(); ?>annual_report" class="form-inline">
                <div class="form-group m-r-10">
                    <label class="m-r-10">Financial Year:</label>
                    <select name="fy" class="form-control">
                        <?php
                        $cur_y = (int)date('Y');
                        // Build base list (last 3 to next 2 years)
                        $fy_options = [];
                        for ($i = -3; $i <= 2; $i++) {
                            $sy = $cur_y + $i;
                            $fy_options[] = $sy . '-' . ($sy + 1);
                        }
                        // Always include the currently selected FY (in case it's older/newer)
                        if (isset($fy) && $fy !== '' && !in_array($fy, $fy_options, true)) {
                            $fy_options[] = $fy;
                            sort($fy_options);
                        }
                        foreach ($fy_options as $opt):
                        ?>
                            <option value="<?php echo $opt; ?>" <?php echo ($opt === (isset($fy) ? $fy : '')) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?php echo base_url(); ?>download_annual_report_pdf/<?php echo urlencode($fy); ?>" class="btn btn-danger m-l-5" target="_blank"><i class="fa fa-file-pdf"></i> Export PDF</a>
            </form>
        </div>
    </div>

    <p class="text-muted">
        Period: <strong><?php echo date('d M Y', $fy_start_ts); ?></strong> to <strong><?php echo date('d M Y', $fy_end_ts); ?></strong>
        (12 months)
    </p>

    <!-- Summary cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-green">
                <div class="stats-icon"><i class="fa fa-line-chart"></i></div>
                <div class="stats-info">
                    <h4>Total Sales (Collected)</h4>
                    <p>Rs. <?php echo number_format($total_sales_collected, 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-blue">
                <div class="stats-icon"><i class="fa fa-shopping-cart"></i></div>
                <div class="stats-info">
                    <h4>Total Purchases</h4>
                    <p>Rs. <?php echo number_format($total_purchases, 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-orange">
                <div class="stats-icon"><i class="fa fa-credit-card"></i></div>
                <div class="stats-info">
                    <h4>Total Expenses</h4>
                    <p>Rs. <?php echo number_format($total_expenses, 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="widget widget-stats bg-purple">
                <div class="stats-icon"><i class="fa fa-users"></i></div>
                <div class="stats-info">
                    <h4>Total Salary</h4>
                    <p>Rs. <?php echo number_format($total_salary, 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail tables -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Combined Summary (for ITR / CA use)</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-right">Invoices / Items</th>
                                <th class="text-right">Amount (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Total Sales — Rent (Plan/Fees collected from students)</strong></td>
                                <td class="text-right"><?php echo $sales_invoice_count; ?></td>
                                <td class="text-right"><?php echo number_format($total_sales_rent, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Total Sales — Service charges</td>
                                <td class="text-right">-</td>
                                <td class="text-right"><?php echo number_format($total_sales_services, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Total Sales — Late fees</td>
                                <td class="text-right">-</td>
                                <td class="text-right"><?php echo number_format($total_sales_late, 2); ?></td>
                            </tr>
                            <tr class="active">
                                <td><strong>Gross Sales</strong></td>
                                <td class="text-right"><?php echo $sales_invoice_count; ?></td>
                                <td class="text-right"><strong><?php echo number_format($total_sales_gross, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Total Amount Collected (paid transactions)</td>
                                <td class="text-right">-</td>
                                <td class="text-right"><?php echo number_format($total_sales_collected, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Total Purchases</td>
                                <td class="text-right"><?php echo count($purchases_in_fy); ?></td>
                                <td class="text-right"><?php echo number_format($total_purchases, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Total Expenses</td>
                                <td class="text-right"><?php echo count($expenses_in_fy); ?></td>
                                <td class="text-right"><?php echo number_format($total_expenses, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Total Salary Paid</td>
                                <td class="text-right"><?php echo count($salary_in_fy); ?></td>
                                <td class="text-right"><?php echo number_format($total_salary, 2); ?></td>
                            </tr>
                            <tr class="active">
                                <td><strong>Total Outgoing (Purchases + Expenses + Salary)</strong></td>
                                <td class="text-right">-</td>
                                <td class="text-right"><strong><?php echo number_format($total_outgoing, 2); ?></strong></td>
                            </tr>
                            <tr class="<?php echo $net_position >= 0 ? 'success' : 'danger'; ?>">
                                <td><strong>Net Position (Collected - Outgoing)</strong></td>
                                <td class="text-right">-</td>
                                <td class="text-right"><strong>Rs. <?php echo number_format($net_position, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->
