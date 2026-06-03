<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>staff_training">Staff Training</a></li>
        <li class="breadcrumb-item active">Edit Lesson</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Edit Training Lesson</h1>
    <!-- end page-header -->

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button><?php echo $this->session->flashdata('success'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button><?php echo $this->session->flashdata('warning'); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Edit: <?php echo html_escape($lesson->title); ?></h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open_multipart('edit_staff_training/' . (int)$lesson->lesson_id, array('method' => 'post', 'data-parsley-validate' => 'true')); ?>

                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input name="title" type="text" data-parsley-required="true" class="form-control" value="<?php echo html_escape($lesson->title); ?>" />
                    </div>

                    <div class="note note-yellow m-b-15">
                        <strong>Current video:</strong>
                        <?php if ($lesson->video_type === 'upload'): ?>
                            <span class="label label-info"><i class="fa fa-file-video-o"></i> Uploaded MP4</span>
                            <a href="<?php echo base_url(); ?>uploads/training/<?php echo html_escape($lesson->video_path); ?>" target="_blank" class="m-l-5"><?php echo html_escape($lesson->video_path); ?></a>
                        <?php elseif ($lesson->video_type === 'youtube'): ?>
                            <span class="label label-danger"><i class="fa fa-youtube-play"></i> YouTube</span>
                            <a href="<?php echo html_escape($lesson->video_path); ?>" target="_blank" class="m-l-5"><?php echo html_escape($lesson->video_path); ?></a>
                        <?php else: ?>
                            <span class="text-muted">none</span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Replace with new MP4 upload (optional)</label>
                        <input name="video_upload" type="file" accept=".mp4,video/mp4" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label>Or replace with new YouTube URL (optional)</label>
                        <input name="youtube_url" type="url" class="form-control" placeholder="Leave blank to keep current video" />
                    </div>

                    <?php if (!empty($lesson->video_path)): ?>
                    <div class="form-group">
                        <div class="checkbox checkbox-css is-changed">
                            <input type="checkbox" id="remove_video" name="remove_video" value="1" />
                            <label for="remove_video" style="padding-left:25px;">Remove current video entirely</label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Add More Training Photos (JPG / PNG — multiple)</label>
                        <input name="images[]" type="file" accept=".jpg,.jpeg,.png" multiple class="form-control" />
                    </div>

                    <?php if (!empty($lesson_images)): ?>
                    <div class="form-group">
                        <label>Current Images</label>
                        <div class="row">
                            <?php foreach ($lesson_images as $img): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 m-b-10">
                                    <div class="panel panel-default">
                                        <div class="panel-body p-5" style="text-align:center;">
                                            <a href="<?php echo base_url(); ?>uploads/training/<?php echo html_escape($img['image_path']); ?>" target="_blank">
                                                <img src="<?php echo base_url(); ?>uploads/training/<?php echo html_escape($img['image_path']); ?>" alt="" style="max-width:100%; max-height:120px;" />
                                            </a>
                                        </div>
                                        <div class="panel-footer p-5" style="text-align:center;">
                                            <a href="<?php echo base_url(); ?>staff_training/remove_image/<?php echo (int)$img['image_id']; ?>"
                                               class="btn btn-xs btn-danger"
                                               onclick="return confirm('Delete this image?');">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Content / Instructions</label>
                        <textarea name="body_html" class="summernote form-control"><?php echo html_escape($lesson->body_html); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Lesson</button>
                    <a href="<?php echo base_url(); ?>staff_training" class="btn btn-default">Cancel</a>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.css" />
<script src="<?php echo base_url(); ?>assets/plugins/summernote/summernote.js"></script>
<script>
$(function() {
    if (typeof $.fn.summernote !== 'undefined') {
        $('.summernote').summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold','italic','underline','clear']],
                ['para', ['ul','ol','paragraph']],
                ['insert', ['link','picture','video']],
                ['view', ['fullscreen','codeview']]
            ]
        });
    }
});
</script>
