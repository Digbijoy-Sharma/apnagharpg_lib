<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Monthly Sales Report</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Monthly Sales Report</h1>
    <!-- end page-header -->

    <!-- Filter row -->
    <div class="row m-b-20">
        <div class="col-lg-9">
            <form method="get" action="<?php echo base_url(); ?>monthly_sales_report" class="form-inline">
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
                <a href="<?php echo base_url(); ?>download_monthly_sales_report_csv/<?php echo urlencode($month); ?>/<?php echo (int)$year; ?>" class="btn btn-success m-l-5"><i class="fa fa-file-excel"></i> Export CSV</a>
                <a href="<?php echo base_url(); ?>download_monthly_sales_report_pdf/<?php echo urlencode($month); ?>/<?php echo (int)$year; ?>" class="btn btn-danger m-l-5" target="_blank"><i class="fa fa-file-pdf"></i> Export PDF</a>
            </form>
        </div>
    </div>

    <?php
    $tenant_rents = $this->db->get_where('tenant_rent', array('month' => $month, 'year' => (int)$year))->result_array();
    $total_rent = 0;
    $total_services = 0;
    $total_late_fee = 0;
    $total_collected = 0;
    $rows = [];

    foreach ($tenant_rents as $tr) {
        $invoice_id  = (int)$tr['invoice_id'];
        $tenant_id   = (int)$tr['tenant_id'];
        $amount      = (float)$tr['amount'];
        $status      = (int)$tr['status'];

        $invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row();
        $invoice_number = $invoice ? $invoice->invoice_number : '-';
        $late_fee = $invoice ? (float)$invoice->late_fee : 0;
        $created_on = $invoice ? (int)$invoice->created_on : 0;

        // Service costs for this invoice
        $svc_total = 0;
        $svc_names = [];
        $services = $this->db->get_where('invoice_service', array('invoice_id' => $invoice_id))->result_array();
        foreach ($services as $s) {
            $svc = $this->db->get_where('service', array('service_id' => $s['service_id']))->row();
            if ($svc) {
                $svc_total += (float)$svc->cost;
                $svc_names[] = $svc->name;
            }
        }

        // Paid amount (sum transactions)
        $this->db->select_sum('amount');
        $this->db->from('invoice_transaction');
        $this->db->where('invoice_id', $invoice_id);
        $paid = (float)($this->db->get()->row()->amount ?? 0);
        if ($paid <= 0 && $status == 1) {
            $paid = $amount + $svc_total + $late_fee;
        }

        $tenant_name = '-';
        $t = $this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row();
        if ($t) $tenant_name = $t->name;

        $rows[] = [
            'invoice_number' => $invoice_number,
            'date'           => $created_on,
            'tenant_name'    => $tenant_name,
            'services'       => implode(', ', $svc_names),
            'rent'           => $amount,
            'services_amt'   => $svc_total,
            'late_fee'       => $late_fee,
            'paid'           => $paid,
            'status'         => $status,
        ];

        $total_rent      += $amount;
        $total_services  += $svc_total;
        $total_late_fee  += $late_fee;
        $total_collected += $paid;
    }
    $grand_total = $total_rent + $total_services + $total_late_fee;
    ?>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title">Sales for <?php echo html_escape($month . ' ' . $year); ?></h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Tenant</th>
                            <th>Services</th>
                            <th class="text-right">Rent</th>
                            <th class="text-right">Services</th>
                            <th class="text-right">Late Fee</th>
                            <th class="text-right">Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr><td colspan="10" class="text-center text-muted">No sales records found for this period.</td></tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($rows as $r): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo html_escape($r['invoice_number']); ?></td>
                                    <td><?php echo $r['date'] ? date('d M, Y', $r['date']) : '-'; ?></td>
                                    <td><?php echo html_escape($r['tenant_name']); ?></td>
                                    <td><?php echo html_escape($r['services'] ?: '-'); ?></td>
                                    <td class="text-right"><?php echo number_format($r['rent'], 2); ?></td>
                                    <td class="text-right"><?php echo number_format($r['services_amt'], 2); ?></td>
                                    <td class="text-right"><?php echo number_format($r['late_fee'], 2); ?></td>
                                    <td class="text-right"><?php echo number_format($r['paid'], 2); ?></td>
                                    <td>
                                        <?php if ($r['status'] == 1): ?>
                                            <span class="label label-success">Paid</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Due</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="active">
                            <th colspan="5" class="text-right">Totals:</th>
                            <th class="text-right"><?php echo number_format($total_rent, 2); ?></th>
                            <th class="text-right"><?php echo number_format($total_services, 2); ?></th>
                            <th class="text-right"><?php echo number_format($total_late_fee, 2); ?></th>
                            <th class="text-right"><?php echo number_format($total_collected, 2); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="alert alert-info m-t-20 m-b-0">
                <strong>Summary:</strong>
                Gross Sales (Rent + Services + Late Fee) = <strong>Rs. <?php echo number_format($grand_total, 2); ?></strong> &nbsp;|&nbsp;
                Collected = <strong>Rs. <?php echo number_format($total_collected, 2); ?></strong> &nbsp;|&nbsp;
                Pending = <strong>Rs. <?php echo number_format(max(0, $grand_total - $total_collected), 2); ?></strong>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->
