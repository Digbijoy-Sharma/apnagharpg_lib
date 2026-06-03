<?php
    function cleanData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    $filename = "monthly_expense_report_" . $month . "_" . $year . ".csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: text/csv; charset=utf-8");

    $expenses = $this->db->get_where('expense', array('month' => $month, 'year' => (int)$year))->result_array();
    $rows = [];
    $total = 0;
    foreach ($expenses as $e) {
        $amount = (float)$e['amount'];
        $total += $amount;
        $rows[] = [
            'Date'        => $e['timestamp'] ? date('d-M-Y', $e['timestamp']) : '-',
            'Name'        => $e['name'],
            'Description' => $e['description'] ?: '-',
            'Amount'      => number_format($amount, 2, '.', ''),
        ];
    }
    $rows[] = ['Date'=>'TOTAL', 'Name'=>'', 'Description'=>'', 'Amount'=>number_format($total, 2, '.', '')];

    $flag = false;
    foreach ($rows as $row) {
        if (!$flag) {
            echo implode(",", array_map(function($h){ return '"' . str_replace('"','""',$h) . '"'; }, array_keys($row))) . "\r\n";
            $flag = true;
        }
        array_walk($row, 'cleanData');
        echo implode(",", array_map(function($v){ return '"' . str_replace('"','""',$v) . '"'; }, $row)) . "\r\n";
    }
    exit;
