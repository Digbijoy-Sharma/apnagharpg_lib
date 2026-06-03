<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>staff_training">Staff Training</a></li>
        <li class="breadcrumb-item active">Add Lesson</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Add Training Lesson</h1>
    <!-- end page-header -->

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">New Lesson</h4>
                </div>
                <div class="panel-body">
                    <?php echo form_open_multipart('add_staff_training', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>

                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        <input name="title" type="text" data-parsley-required="true" class="form-control" placeholder="e.g. Library Opening &amp; Closing Procedures" />
                    </div>

                    <div class="note note-yellow m-b-15">
                        <strong>Video:</strong> Provide <em>either</em> an MP4 upload <em>or</em> a YouTube link — not both. If you provide both, the YouTube link takes priority.
                    </div>

                    <div class="form-group">
                        <label>Option A — Upload MP4 Video (optional)</label>
                        <input name="video_upload" type="file" accept=".mp4,video/mp4" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label>Option B — YouTube / Unlisted Video URL (optional)</label>
                        <input name="youtube_url" type="url" class="form-control" placeholder="https://youtu.be/dQw4w9WgXcQ  or  https://www.youtube.com/watch?v=..." />
                        <small class="f-s-12 text-muted">Supports youtu.be, youtube.com/watch, youtube.com/shorts. Unlisted videos work fine.</small>
                    </div>

                    <div class="form-group">
                        <label>Training Photos (JPG / PNG — multiple files allowed)</label>
                        <input name="images[]" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" multiple class="form-control" />
                    </div>

                    <div class="form-group">
                        <label>Content / Instructions</label>
                        <textarea name="body_html" class="summernote form-control" placeholder="Write the training content here..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Lesson</button>
                    <a href="<?php echo base_url(); ?>staff_training" class="btn btn-default">Cancel</a>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end #content -->

<!-- Summernote CSS + JS for this page -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.css" />
<script src="<?php echo base_url(); ?>assets/plugins/summernote/summernote.js"></script>
<script>
$(function() {
    if (typeof $.fn.summernote !== 'undefined') {
        $('.summernote').summernote({
            height: 250,
            placeholder: 'Write the training content here...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold','italic','underline','clear']],
                ['para', ['ul','ol','paragraph']],
                ['insert', ['link','picture','video']],
                ['view', ['fullscreen','codeview']]
            ]
        });
    } else {
        // Fallback: simple textarea
        $('.summernote').each(function(){ this.style.minHeight = '200px'; });
    }
});
</script>
