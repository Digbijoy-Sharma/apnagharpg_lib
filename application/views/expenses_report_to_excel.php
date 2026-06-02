<?PHP
    // header("Content-Type: text/plain");
    
    function cleanData(&$str) 
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }
    $filename = "expenses_report_" . $year . ".xls";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    $flag = false;
    $query = "SELECT `month` as Month, `year` as Year, `amount` as Amount, `timestamp` as Date, `name` as Name, `description` as Description FROM expense where year = '$year'";
    $data = $this->db->query($query)->result_array();
    foreach ($data as $row) {
        $row['Date']            =   date('d-M-Y', $row['Date']);

        if(!$flag) {
            echo implode("\t", array_keys($row)) . "\r\n";
            $flag = true;
        }

        array_walk($row, __NAMESPACE__ . '\cleanData');
        
        echo implode("\t", array_values($row)) . "\r\n";
    }
    exit;
?>