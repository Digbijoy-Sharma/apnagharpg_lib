
<?php
	$count = 1;
	$tid=$_GET['tid'];
	$tenant_details = $this->db->get_where('tenant', array('tenant_id' => $tid))->result_array();
	$owner_image = $this->db->order_by('serial', 'asc')->get('board_member')->row('image');
	foreach ($tenant_details as $tenant):
?>

	
<!doctype html>
<html lang="en">

<head>
  <title>Title</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body>

  <main id="printableArea" style="padding: 10px 10px 10px 10px !important; font-weight: bold !important; font-size: 18.5px !important; color: Black;">
   <div class="container">
        <h3 class="text-center"><span style="text-decoration: underline;" style="font-size: 18.5px !important;">Student Verification Form</span></h3>
    <h3 class="text-center"><span style="text-decoration: underline;" style="font-size: 18.5px !important;">East Police District</span></h3>
    <h3><span ><strong>Land Lord Details:</strong></span></h3><br>
    <div class="row">
        <div class="col">
            <h3 class="float-left"><span >Name: ABHISHEK MALIK</span></h3><br>
            
        </div>
        <div class="col float-right">
            &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
            <?php if (!empty($owner_image) && file_exists(FCPATH . 'uploads/board_members/' . $owner_image)) : ?>
            <img class="float-right" src="<?php echo base_url(); ?>uploads/board_members/<?php echo html_escape($owner_image); ?>"  height="125px" width="125px">
            <?php endif; ?>
        </div>
    </div>
    
    <!-- <h3><span style="font-size: 18.5px !important;">S.O:</span></h3> -->
    <!-- <h3><span style="font-size: 18.5px !important;">Address:&nbsp;</span></h3>
    <h3><span style="font-size: 18.5px !important;">M/No.:</span></h3>
    <h3><span style="font-size: 18.5px !important;">Aadhar Card No.:</span></h3> -->
    <h3><span >S.O:</span></h3>
            <h3><span >Address: House no 3,  by lane No 1, Ajanta path, gopal fhukan Road, Survey, Beltola Tiniali, Assam 781028</span></h3>
    <h3><span >M/No.: 9756056848</span></h3>
    <h3><span >Aadhar Card No.: 203611900031</span></h3>
    <h3><span >Pan card No.:</span></h3>
    <h3><span >ID Proof (attach): Aadhar Card &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;<strong>Signature of Owner</strong></span></h3>
    <h3>----------------------------------------------------------------------------------------------------------------------------</h3>
    <h3 class="text-center" style="text-decoration: underline;"><strong>Student Details:</strong></h3>
    <div class="row">
        <div class="col float-left">
            <h3 class="float-left"><span >Name: <?php echo $tenant['email'] ? html_escape($tenant['name']) : 'N/A'; ?></span></h3><br>
           
        </div>
     
        <div class="col float-right">
            &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
            <img class="float-right" src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['image_link']); ?>" alt="<?php echo html_escape($tenant['name']); ?>" height="125px" width="125px">
        </div>
    </div>
     <h3 class="float-left"><span >S.O: <?php echo $tenant['emergency_person'] ? html_escape($tenant['emergency_person']) : 'N/A'; ?></span></h3><br>
     <h3 class="float-left"><span >Full Permanent Address: <?php echo $tenant['home_address'] == '' ? 'N/A' : $tenant['home_address']; ?></span></h3><br>
    <h3><span >M/No.: <?php echo $tenant['email'] ? html_escape($tenant['mobile_number']) : 'N/A'; ?></span></h3>
    <h3><span >ID Proof (attach): Voter ID</span></h3>
    <h3><span >Student Vehicle Details:</span></h3>
    
    
    <h3><span >Date of Verification:&nbsp; &nbsp; &nbsp; &nbsp;   &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<strong>Signature of Student</strong></span></h3>
      <h3>----------------------------------------------------------------------------------------------------------------------------</h3>
    <h3 class="text-center"><span ><strong><span style="text-decoration: underline;">Office use Only</span></strong></span></h3><br>
    <h3><span >Received by whom Police Personnel:&nbsp;</span></h3>
    <h3><span >Date:</span></h3>
    <h3><span >Hatigaon Police Station:</span></h3>
    </div>
      </main>
	
<div class="text-center">
	<a href="javascript:void(0);" onclick="printPageArea('printableArea')" class="btn btn-success">Print</a>
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	
</div>	
	

<script>
function printPageArea(areaID){
    var printContent = document.getElementById(areaID).innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
}
</script>

	
	
	<?php endforeach; ?>
	

</body>

</html>
