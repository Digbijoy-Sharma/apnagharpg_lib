<?php $tenant_id = isset($tenant_id) ? $tenant_id : 0; ?>
<?php
$tenant = $this->db->get_where('tenant', array('tenant_id' => $tenant_id))->row_array();
$room = $this->db->get_where('room', array('room_id' => $tenant['room_id']))->row_array();
$template_row = $this->db->get_where('setting', array('name' => 'tenant_agreement_template'))->row();
$template = $template_row ? $template_row->content : '';
$seat_label = 'N/A';

if (!empty($room)) {
    $seat_label = trim(($room['roomnumber'] ? $room['roomnumber'] . ' -> ' : '') . 'Seat ' . $room['room_number']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Study Plan Agreement - <?php echo $tenant['name']; ?></title>
    <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #fff;
            color: #000;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .tenant-details {
            display: flex;
            margin-bottom: 20px;
        }
        .tenant-photo {
            width: 150px;
            margin-right: 20px;
        }
        .tenant-photo img {
            width: 100%;
            height: auto;
            border: 1px solid #ccc;
        }
        .tenant-info {
            flex: 1;
        }
        .tenant-info table {
            width: 100%;
        }
        .tenant-info th, .tenant-info td {
            padding: 5px;
            text-align: left;
        }
        .agreement-body {
            margin-top: 30px;
            line-height: 1.6;
        }
        @media print {
            .hidden-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="text-right hidden-print" style="margin-bottom: 20px;">
        <button onclick="window.print();" class="btn btn-primary">Print Agreement</button>
    </div>

    <div class="header">
        <h2>STUDENT STUDY PLAN AGREEMENT</h2>
    </div>

    <div class="tenant-details">
        <div class="tenant-photo">
            <?php if ($tenant['image_link']): ?>
                <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo $tenant['image_link']; ?>" alt="Student Photo">
            <?php else: ?>
                <img src="<?php echo base_url(); ?>assets/img/tenant.png" alt="Student Photo">
            <?php endif; ?>
        </div>
        <div class="tenant-info">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Student Name</th>
                    <td><?php echo $tenant['name']; ?></td>
                </tr>
                <tr>
                    <th>Mobile Number</th>
                    <td><?php echo $tenant['mobile_number']; ?></td>
                </tr>
                <tr>
                    <th>Seat Details</th>
                    <td><?php echo $seat_label; ?> (<?php echo $this->db->get_where('setting', array('name' => 'system_name'))->row()->content; ?>)</td>
                </tr>
                <tr>
                    <th>Home Address</th>
                    <td><?php echo $tenant['home_address'] ? $tenant['home_address'] : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Plan Start Date</th>
                    <td><?php echo !empty($tenant['lease_start']) ? date('d M Y', $tenant['lease_start']) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Emergency Contact</th>
                    <td><?php echo $tenant['emergency_person'] . ' - ' . $tenant['emergency_contact']; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="agreement-body">
        <?php echo $template; ?>
    </div>

    <div style="margin-top: 100px; display: flex; justify-content: space-between;">
        <div style="text-align: center;">
            <p>___________________________</p>
            <p><strong>Landlord Signature</strong></p>
        </div>
        <div style="text-align: center;">
            <p>___________________________</p>
            <p><strong>Student Signature</strong></p>
        </div>
    </div>
</body>
</html>
