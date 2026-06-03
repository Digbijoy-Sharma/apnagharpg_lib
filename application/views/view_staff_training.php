<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>staff_training">Staff Training</a></li>
        <li class="breadcrumb-item active"><?php echo html_escape($lesson->title); ?></li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><?php echo html_escape($lesson->title); ?>
        <div class="pull-right">
            <a href="<?php echo base_url(); ?>edit_staff_training/<?php echo (int)$lesson->lesson_id; ?>" class="btn btn-warning"><i class="fa fa-pencil"></i> Edit</a>
            <a href="<?php echo base_url(); ?>staff_training" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </h1>
    <!-- end page-header -->

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <!-- Video section -->
            <?php if ($lesson->video_type === 'youtube'): ?>
                <div class="panel panel-inverse">
                    <div class="panel-heading"><h4 class="panel-title">Training Video</h4></div>
                    <div class="panel-body text-center">
                        <div class="embed-responsive embed-responsive-16by9" style="max-width:800px; margin:0 auto;">
                            <iframe class="embed-responsive-item" src="<?php echo html_escape($lesson->video_path); ?>" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            <?php elseif ($lesson->video_type === 'upload'): ?>
                <div class="panel panel-inverse">
                    <div class="panel-heading"><h4 class="panel-title">Training Video</h4></div>
                    <div class="panel-body text-center">
                        <video controls style="max-width:100%; max-height:500px;">
                            <source src="<?php echo base_url(); ?>uploads/training/<?php echo html_escape($lesson->video_path); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Body content -->
            <?php if (!empty($lesson->body_html)): ?>
                <div class="panel panel-inverse">
                    <div class="panel-heading"><h4 class="panel-title">Instructions</h4></div>
                    <div class="panel-body">
                        <?php echo $lesson->body_html; // Already sanitized by Summernote on input; treat as HTML for display ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Images -->
            <?php if (!empty($lesson_images)): ?>
                <div class="panel panel-inverse">
                    <div class="panel-heading"><h4 class="panel-title">Training Photos</h4></div>
                    <div class="panel-body">
                        <div class="row">
                            <?php foreach ($lesson_images as $img): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 m-b-15">
                                    <a href="<?php echo base_url(); ?>uploads/training/<?php echo html_escape($img['image_path']); ?>" target="_blank">
                                        <img src="<?php echo base_url(); ?>uploads/training/<?php echo html_escape($img['image_path']); ?>" class="img-responsive img-thumbnail" />
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($lesson->video_type === null && empty($lesson->body_html) && empty($lesson_images)): ?>
                <div class="alert alert-info m-b-0">
                    This lesson has no content yet. <a href="<?php echo base_url(); ?>edit_staff_training/<?php echo (int)$lesson->lesson_id; ?>" class="alert-link">Add some content</a>.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- end #content -->
