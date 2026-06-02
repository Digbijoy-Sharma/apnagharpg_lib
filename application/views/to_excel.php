<?PHP
    header("Content-Type: text/plain");
    
    // function cleanData(&$str) 
    // {
    //     $str = preg_replace("/\t/", "\\t", $str);
    //     $str = preg_replace("/\r?\n/", "\\n", $str);
    //     if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    // }
    // $filename = "rents_data_" . date('Ymd') . ".xls";
    // header("Content-Disposition: attachment; filename=\"$filename\"");
    // header("Content-Type: application/vnd.ms-excel");

    $flag = false;
    $data = $this->db->get_where('tenant_rent', array('year' => 2021))->result_array();
    foreach ($data as $row) {
        
        // $row['utility_bill_category_id'] = $this->db->get_where('utility_bill_category', array('utility_bill_category_id' => $row['utility_bill_category_id']))->row()->name;
        $row['status'] = $row['status'] ? 'Paid' : 'Due';
        $row['tenant_id'] = $this->db->get_where('tenant', array('tenant_id' => $row['tenant_id']))->row()->name;
        $row['timestamp']= date('d-M-Y', $row['timestamp']);
        $row['created_on']= date('d-M-Y', $row['created_on']);
        // $row['paid_on']= date('d-M-Y', $row['paid_on']);
        if(!$flag) {
            echo implode("\t", array_keys($row)) . "\r\n";
            $flag = true;
        }

        // array_walk($row, __NAMESPACE__ . '\cleanData');
        
        echo implode("\t", array_values($row)) . "\r\n";
    }
    exit;
?>