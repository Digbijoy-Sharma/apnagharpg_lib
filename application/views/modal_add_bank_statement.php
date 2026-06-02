<?php echo form_open_multipart('bank_statements/add', array('data-parsley-validate' => 'true', 'name' => 'add_bank_statement')); ?>
<div class="form-group">
    <label>Month *</label>
    <select name="month" class="form-control" data-parsley-required="true">
        <option value="">Select Month</option>
        <option value="January">January</option>
        <option value="February">February</option>
        <option value="March">March</option>
        <option value="April">April</option>
        <option value="May">May</option>
        <option value="June">June</option>
        <option value="July">July</option>
        <option value="August">August</option>
        <option value="September">September</option>
        <option value="October">October</option>
        <option value="November">November</option>
        <option value="December">December</option>
    </select>
</div>
<div class="form-group">
    <label>Year *</label>
    <select name="year" class="form-control" data-parsley-required="true">
        <option value="">Select Year</option>
        <?php for($i = date('Y'); $i >= 2020; $i--): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
</div>
<div class="form-group">
    <label>Bank Statement File (PDF, JPG, PNG) *</label>
    <input type="file" name="file_name" class="form-control" data-parsley-required="true" accept=".pdf,.jpeg,.jpg,.png">
</div>

<button type="submit" class="btn btn-success">Upload Bank Statement</button>
<?php echo form_close(); ?>
