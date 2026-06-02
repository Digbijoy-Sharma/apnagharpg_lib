<!-- begin #content -->
<div id="content" class="content">
	<!-- begin breadcrumb -->
	<ol class="breadcrumb pull-right">
		<li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
		<li class="breadcrumb-item active"><?php echo $this->lang->line('board_members'); ?></li>
	</ol>
	<!-- end breadcrumb -->
	<!-- begin page-header -->
	<h1 class="page-header">
		<?php echo $this->lang->line('board_members'); ?>
	</h1>
	<!-- end page-header -->

	<!-- begin row -->
	<div class="row">
		<!-- begin col-12 -->
		<div class="col-md-12">
			<div class="row">
				<?php
					$query	=	$this->db->get_where('board_member', array('serial' => 1));
					if ($query->num_rows() > 0):
				?>
				<div class="col-md-4 offset-md-4">
					<div class="card border-0">
						<?php if ($query->row()->image): ?>
							<img class="card-img-top" src="<?php echo base_url('uploads/board_members/' . $query->row()->image); ?>" alt="" />
						<?php else: ?>
							<img class="card-img-top" src="<?php echo base_url('assets/img/tenant.png'); ?>" alt="" />
						<?php endif; ?>
						<div class="card-body">
							<h4 class="card-title mb-10px"><?php echo $query->row()->name; ?></h4>
							<p class="card-text"><?php echo $query->row()->position; ?></p>
						</div>
					</div>
				</div>
				<?php else: ?>
	
				<?php endif; ?>
			</div>

			<div class="row">
				<?php
					$board_members = $this->db->get_where('board_member', array('serial !=' => 1))->result_array();
					foreach ($board_members as $board_member):
				?>
				<div class="col-md-4">
					<div class="card border-0">
						<?php if ($board_member['image']): ?>
							<img class="card-img-top" src="<?php echo base_url('uploads/board_members/' . $board_member['image']); ?>" alt="" />
						<?php else: ?>
							<img class="card-img-top" src="<?php echo base_url('assets/img/tenant.png'); ?>" alt="" />
						<?php endif; ?>
						<div class="card-body">
							<h4 class="card-title mb-10px"><?php echo $board_member['name']; ?></h4>
							<p class="card-text"><?php echo $board_member['position']; ?></p>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<!-- end col-12 -->
	</div>
	<!-- end row -->
</div>
<!-- end #content -->