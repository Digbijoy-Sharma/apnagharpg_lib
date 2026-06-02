<?php $param2 = isset($param2) ? $param2 : ''; ?>
<?php echo form_open_multipart('upload_signed_agreement/' . $param2, array('id' => 'upload_signed_agreement', 'method' => 'post')); ?>
<div class="form-group">
    <label>Upload Signed Agreement (PDF, JPG, PNG)</label>
    <br>
    <input class="form-control" type="file" name="signed_agreement" accept=".pdf,.jpg,.jpeg,.png" required>
</div>

<hr>

<button type="submit" class="mb-sm btn btn-primary">Upload</button>
<?php echo form_close(); ?>
