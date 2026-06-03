<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Staff Training</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Staff Training <small>Lessons &amp; resources for staff</small></h1>
    <!-- end page-header -->

    <!-- Flash messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('warning'); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <div class="row m-b-15">
        <div class="col-lg-12">
            <a href="<?php echo base_url(); ?>add_staff_training" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Training Lesson
            </a>
        </div>
    </div>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title">All Training Lessons</h4>
        </div>
        <div class="panel-body">
            <?php
            $lessons = $this->db->order_by('created_on', 'DESC')->get('training_lesson')->result_array();
            if (empty($lessons)):
            ?>
                <div class="alert alert-info m-b-0">
                    <i class="fa fa-info-circle"></i> No training lessons yet. Click <strong>Add New Training Lesson</strong> to create your first one.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Video</th>
                                <th>Images</th>
                                <th>Body</th>
                                <th>Created</th>
                                <th>By</th>
                                <th class="text-center" style="min-width:180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($lessons as $l):
                                $img_count = $this->db->get_where('training_lesson_image', array('lesson_id' => $l['lesson_id']))->num_rows();
                                $creator   = $this->db->get_where('user', array('user_id' => $l['created_by']))->row();
                                $creator_name = $creator ? $creator->email : '-';
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><strong><?php echo html_escape($l['title']); ?></strong></td>
                                    <td>
                                        <?php if ($l['video_type'] === 'upload'): ?>
                                            <span class="label label-info"><i class="fa fa-file-video-o"></i> Uploaded MP4</span>
                                        <?php elseif ($l['video_type'] === 'youtube'): ?>
                                            <span class="label label-danger"><i class="fa fa-youtube-play"></i> YouTube</span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $img_count; ?></td>
                                    <td><?php echo $l['body_html'] ? '<i class="fa fa-check text-success"></i> Yes' : '<span class="text-muted">—</span>'; ?></td>
                                    <td><?php echo $l['created_on'] ? date('d M, Y', $l['created_on']) : '-'; ?></td>
                                    <td><?php echo html_escape($creator_name); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo base_url(); ?>view_staff_training/<?php echo (int)$l['lesson_id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?php echo base_url(); ?>edit_staff_training/<?php echo (int)$l['lesson_id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="<?php echo base_url(); ?>staff_training/remove/<?php echo (int)$l['lesson_id']; ?>" class="btn btn-sm btn-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this lesson? This cannot be undone.');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- end #content -->
