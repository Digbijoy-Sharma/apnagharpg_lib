<?php $param2 = isset($param2) ? $param2 : ''; ?>
<?php
	$count = 1;
	$tenant_details = $this->db->get_where('tenant', array('tenant_id' => $param2))->result_array();
	foreach ($tenant_details as $tenant):
?>
	<div class="table-responsive container" id="printableArea" style="padding: 10px 10px 10px 10px;  font-weight: bold !important; font-size: 14px; color: blue;">
		<table class="table" >
			<thead>
				<tr>
			<h1 class="bg bg-danger"> Student Details</h1>
				</tr>
			</thead>
			<tbody class="container">
				<tr>
					<img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['image_link']); ?>" alt="<?php echo html_escape($tenant['name']); ?>" class="img-rounded height-150 width-150" />
				</tr>
				<tr>
				    
		
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong>Name: </strong></td>
					<td><strong><?php echo $tenant['email'] ? html_escape($tenant['name']) : 'N/A'; ?></strong></td>
				</tr>
				<tr>
				    
		
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong><?php echo $tenant['id_number'] ? html_escape($this->db->get_where('id_type', array('id_type_id' => $tenant['id_type_id']))->row()->name) : 'N/A'; ?></strong></td>
					<td><strong><?php echo $tenant['id_number'] ? html_escape($tenant['id_number']) : 'N/A'; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong><?php echo $this->lang->line('profession'); ?></strong></td>
					<td><strong><?php echo $tenant['profession_id'] ? html_escape($this->db->get_where('profession', array('profession_id' => $tenant['profession_id']))->row()->name) : 'N/A'; ?></strong></td>
				</tr>
					<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong>Blood Group</strong></td>
					<td><strong><?php echo $tenant['blood_group'] ? html_escape($tenant['blood_group']) : 'N/A'; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong>Parents Name</strong></td>
					<td><strong><?php echo $tenant['emergency_person'] ? html_escape($tenant['emergency_person']) : 'N/A'; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong>Parents Contact Number</strong></td>
					<td><strong><?php echo $tenant['emergency_contact'] ? html_escape($tenant['emergency_contact']) : 'N/A'; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong>Mobile Number</strong></td>
				<td><strong><?php echo $tenant['email'] ? html_escape($tenant['mobile_number']) : 'N/A'; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong><?php echo $this->lang->line('lease_period'); ?></strong></td>
					<td><strong><?php echo ($tenant['lease_start'] ? date('d M, Y', $tenant['lease_start']) : 'N/A') . ' to ' . ($tenant['lease_end'] ? date('d M, Y', $tenant['lease_end']) : 'N/A'); ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong><?php echo $this->lang->line('home_address'); ?></strong></td>
					<td><strong><?php echo $tenant['home_address'] == '<br>' ? 'N/A' : $tenant['home_address']; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong><?php echo $this->lang->line('work_address'); ?></strong></td>
					<td><strong><?php echo $tenant['work_address'] == '<br>' ? 'N/A' : $tenant['work_address']; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong><?php echo $this->lang->line('extra_note'); ?></strong></td>
					<td><strong><?php echo $tenant['extra_note']?  html_escape($tenant['extra_note']) : 'N/A'; ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong><?php echo $this->lang->line('created_on'); ?></strong></td>
					<td><strong><?php echo date('d M, Y', $tenant['created_on']); ?></strong></td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong>Id's Front Side Photo Copy: </strong></td>
					
				<td>	
				<img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['id_front_image_link']); ?>"  class=" height-300 width-500" />
				</td>
				</tr>
				<tr>
					<td><strong><?php echo $count++; ?></strong></td>
					<td><strong>Id's Back Side Photo Copy: </strong></td>
					
				<td>	
				<img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['id_back_image_link']); ?>"  class=" height-300 width-500" />
				</td>
				</tr>
			</tbody>
		</table>
	</div>
	

	
	
	<a href="javascript:void(0);" onclick="printPageArea('printableArea')" class="btn btn-success">Print</a>
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	

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
