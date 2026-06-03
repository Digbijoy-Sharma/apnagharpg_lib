<?php
    function cleanData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    $filename = "monthly_sales_report_" . $month . "_" . $year . ".csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=utf-8");

    $tenant_rents = $this->db->get_where('tenant_rent', array('month' => $month, 'year' => (int)$year))->result_array();
    $rows_out = [];
    $total_rent = 0; $total_services = 0; $total_late_fee = 0; $total_collected = 0;

    foreach ($tenant_rents as $tr) {
        $invoice_id = (int)$tr['invoice_id'];
        $tenant_id  = (int)$tr['tenant_id'];
        $amount     = (float)$tr['amount'];
        $status     = (int)$tr['status'];

        $invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row();
        $invoice_number = $invoice ? $invoice->invoice_number : '-';
        $late_fee = $invoice ? (float)$invoice->late_fee : 0;
        $created_on = $invoice ? (int)$invoice->created_on : 0;

        $svc_total = 0; $svc_names = [];
        $services = $this->db->get_where('invoice_service', array('invoice_id' => $invoice_id))->result_array();
        foreach ($services as $s) {
            $svc = $this->db->get_where('service', array('service_id' => $s['service_id']))->row();
            if ($svc) { $svc_total += (float)$svc->cost; $svc_names[] = $svc->name; }
        }

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

        $row = [
            'Invoice #'   => $invoice_number,
            'Date'        => $created_on ? date('d-M-Y', $created_on) : '-',
            'Tenant'      => $tenant_name,
            'Services'    => implode(', ', $svc_names),
            'Rent'        => number_format($amount, 2, '.', ''),
            'Services Amt'=> number_format($svc_total, 2, '.', ''),
            'Late Fee'    => number_format($late_fee, 2, '.', ''),
            'Paid'        => number_format($paid, 2, '.', ''),
            'Status'      => $status == 1 ? 'Paid' : 'Due',
        ];
        $rows_out[] = $row;
        $total_rent += $amount; $total_services += $svc_total; $total_late_fee += $late_fee; $total_collected += $paid;
    }

    // Add a summary footer row
    $rows_out[] = [
        'Invoice #'   => 'TOTAL',
        'Date'        => '',
        'Tenant'      => '',
        'Services'    => '',
        'Rent'        => number_format($total_rent, 2, '.', ''),
        'Services Amt'=> number_format($total_services, 2, '.', ''),
        'Late Fee'    => number_format($total_late_fee, 2, '.', ''),
        'Paid'        => number_format($total_collected, 2, '.', ''),
        'Status'      => '',
    ];

    $flag = false;
    foreach ($rows_out as $row) {
        if (!$flag) {
            echo implode(",", array_map(function($h){ return '"' . str_replace('"','""',$h) . '"'; }, array_keys($row))) . "\r\n";
            $flag = true;
        }
        array_walk($row, 'cleanData');
        echo implode(",", array_map(function($v){ return '"' . str_replace('"','""',$v) . '"'; }, $row)) . "\r\n";
    }
    exit;
