<!-- begin #content -->
<div id="content" class="content">
	<!-- begin breadcrumb -->
	<ol class="breadcrumb pull-right">
		<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
		<li class="breadcrumb-item active"><?php echo $this->lang->line('notices'); ?></li>
	</ol>
	<!-- end breadcrumb -->
	<!-- begin page-header -->
	<h1 class="page-header">
		<?php if ($this->session->userdata('user_type') != 3) : ?>
			<a href="<?php echo base_url(); ?>add_notice">
				<button type="button" class="btn btn-inverse"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_notice'); ?></button>
			</a>
		<?php else : ?>
			<?php echo $this->lang->line('all_the_notices'); ?>
		<?php endif; ?>
	</h1>
	<!-- end page-header -->

	<!-- begin row -->
	<div class="row">
		<!-- begin col-12 -->
		<div class="col-md-12">
			<!-- begin panel -->
			<div class="panel panel-inverse">
				<div class="panel-body">
					<table id="data-table-buttons" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="1%">#</th>
								<th>Title</th>
								<th>Photo</th>
								<th><?php echo $this->lang->line('created_on'); ?></th>
								<th><?php echo $this->lang->line('created_by'); ?></th>
								<th><?php echo $this->lang->line('updated_on'); ?></th>
								<th><?php echo $this->lang->line('updated_by'); ?></th>
								<?php if ($this->session->userdata('user_type') != 3) : ?>
									<th><?php echo $this->lang->line('options'); ?></th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							$this->db->order_by('timestamp', 'desc');
							$notices = $this->security->xss_clean($this->db->get('notice')->result_array());
							foreach ($notices as $row) :
							?>
								<tr>
									<td><?php echo $count++; ?></td>
									<td>
										<a onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_view_notice_details/<?php echo $row['notice_id']; ?>');" href="javascript:;">
											<?php echo $row['title']; ?>
										</a>
									</td>
									<td>
										<?php if (!empty($row['image_link']) && file_exists(FCPATH . 'uploads/notice/' . $row['image_link'])) : ?>
											<a class="btn btn-warning" target="_blank" href="<?php echo base_url(); ?>uploads/notice/<?php echo html_escape($row['image_link']); ?>">View Photo</a>
										<?php elseif (!empty($row['image_link'])) : ?>
											<span class="badge badge-warning">File Missing</span>
										<?php else : ?>
											N/A
										<?php endif; ?>
									</td>
									<td><?php echo date('d M, Y', $row['created_on']); ?></td>
									<td>
										<?php
										$user_type =  $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->user_type;
										if ($user_type == 1) {
											echo 'Admin';
										} else {
											$person_id = $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->person_id;
											echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
										}
										?>
									</td>
									<td><?php echo date('d M, Y', $row['timestamp']); ?></td>
									<td>
										<?php
										$user_type =  $this->db->get_where('user', array('user_id' => $row['updated_by']))->row()->user_type;
										if ($user_type == 1) {
											echo 'Admin';
										} else {
											$person_id = $this->db->get_where('user', array('user_id' => $row['updated_by']))->row()->person_id;
											echo html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
										}
										?>
									</td>
									<?php if ($this->session->userdata('user_type') != 3) : ?>
										<td>
											<div class="btn-group">
												<button type="button" class="btn btn-white btn-xs"><?php echo $this->lang->line('action'); ?></button>
												<button type="button" class="btn btn-white btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<span class="sr-only">Toggle Dropdown</span>
												</button>
												<div class="dropdown-menu dropdown-menu-right">
													<a class="dropdown-item" onclick="showAjaxModal('<?php echo base_url(); ?>modal/popup/modal_edit_notice/<?php echo $row['notice_id']; ?>');" href="javascript:;"><?php echo $this->lang->line('edit'); ?></a>
													<div class="dropdown-divider"></div>
													<a class="dropdown-item" onclick="confirm_modal('<?php echo base_url(); ?>notices/remove/<?php echo $row['notice_id']; ?>');" href="javascript:;"><?php echo $this->lang->line('remove'); ?></a>
												</div>
											</div>
										</td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<!-- end panel -->
		</div>
		<!-- end col-12 -->
	</div>
	<!-- end row -->
</div>
<!-- end #content -->
