<?PHP
    // header("Content-Type: text/plain");
    
    function cleanData(&$str) 
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }
    $filename = "rents_report_" . $year . ".xls";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    $flag = false;
    $query = "SELECT `month` as Month, `year` as Year, `amount` as Amount, `timestamp` as Date, `invoice_id` as Invoice, `tenant_id` as Tenant, `status` as Status FROM tenant_rent where year = '$year'";
    $data = $this->db->query($query)->result_array();
    foreach ($data as $row) {
        $service_names = '';
        $service_costs = 0;
        $services = $this->db->get_where('invoice_service', array('invoice_id' => $row['Invoice'], 'year' => $year))->result_array();
        if (sizeof($services) > 0) {
            foreach ($services as $key => $value) {
                if ($key + 1 != sizeof($services))
                    $service_names .= $this->db->get_where('service', array('service_id' => $value['service_id']))->row()->name . ' & ';
                else 
                    $service_names .= $this->db->get_where('service', array('service_id' => $value['service_id']))->row()->name;

                $service_costs += $this->db->get_where('service', array('service_id' => $value['service_id']))->row()->cost;
            }
        }
        
        $row['Date']            =   date('d-M-Y', $row['Date']);
        $row['Invoice']         =   $this->db->get_where('invoice', array('invoice_id' => $row['Invoice']))->row()->invoice_number;
        $row['Tenant']          =   $this->db->get_where('tenant', array('tenant_id' => $row['Tenant']))->row()->name;
        $row['Status']          =   $row['Status'] ? $this->lang->line('paid') : $this->lang->line('due');
        $row['Services']        =   $service_names;
        $row['Services cost']   =   $service_costs;

        if(!$flag) {
            echo implode("\t", array_keys($row)) . "\r\n";
            $flag = true;
        }

        array_walk($row, __NAMESPACE__ . '\cleanData');
        
        echo implode("\t", array_values($row)) . "\r\n";
    }
    exit;
?>