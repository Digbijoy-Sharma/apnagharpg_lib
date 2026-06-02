<?PHP
    // header("Content-Type: text/plain");
    
    function cleanData(&$str) 
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }
    $filename = "utilities_report_" . $year . ".xls";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    $flag = false;
    $query = "SELECT `month` as Month, `year` as Year, `amount` as Amount, `timestamp` as Date, `status` as Status, `utility_bill_category_id` as Category FROM utility_bill where year = '$year'";
    $data = $this->db->query($query)->result_array();
    foreach ($data as $row) {
        $row['Date']        =   date('d-M-Y', $row['Date']);
        $row['Status']      =   $row['Status'] ? $this->lang->line('paid') : $this->lang->line('due');
        $row['Category']    =   $this->db->get_where('utility_bill_category', array('utility_bill_category_id' => $row['Category']))->row()->name;

        if(!$flag) {
            echo implode("\t", array_keys($row)) . "\r\n";
            $flag = true;
        }

        array_walk($row, __NAMESPACE__ . '\cleanData');
        
        echo implode("\t", array_values($row)) . "\r\n";
    }
    exit;
?>