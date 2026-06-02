<?php $param2 = isset($param2) ? $param2 : ''; ?>
<?php $seat = $this->db->get_where('room', array('room_id' => $param2))->row(); ?>
<?php echo form_open('rooms/update/' . $param2, array('id' => 'edit_room', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
<div class="form-group">
	<label>Study Area / Room Name *</label>
	<input value="<?php echo html_escape($seat->roomnumber); ?>" type="text" name="roomnumber" placeholder="Enter study area or room name" data-parsley-required="true" class="form-control">
</div>
<div class="form-group">
	<label>Seat No *</label>
	<input value="<?php echo html_escape($seat->room_number); ?>" type="text" name="room_number" placeholder="Enter seat number" data-parsley-required="true" class="form-control">
</div>
<div class="form-group">
	<label>Floor / Section</label>
	<input value="<?php echo html_escape($seat->floor); ?>" type="text" name="floor" placeholder="Enter floor or section" class="form-control">
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">Seat Plan Prices</h4>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label>Per Day Price</label>
					<input value="<?php echo html_escape($seat->daily_rent); ?>" type="number" step="0.01" min="0" name="daily_rent" placeholder="Enter per day price" class="form-control">
				</div>
				<div class="form-group">
					<label>Monthly Plan Price</label>
					<input value="<?php echo html_escape($seat->monthly_rent); ?>" type="number" step="0.01" min="0" name="monthly_rent" placeholder="Enter monthly plan price" class="form-control">
				</div>
				<div class="form-group">
					<label>3 Month Plan Price</label>
					<input value="<?php echo html_escape(isset($seat->quarterly_price) ? $seat->quarterly_price : 0); ?>" type="number" step="0.01" min="0" name="quarterly_price" placeholder="Enter 3 month plan price" class="form-control">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label>6 Month Plan Price</label>
					<input value="<?php echo html_escape(isset($seat->half_yearly_price) ? $seat->half_yearly_price : 0); ?>" type="number" step="0.01" min="0" name="half_yearly_price" placeholder="Enter 6 month plan price" class="form-control">
				</div>
				<div class="form-group">
					<label>12 Month Plan Price</label>
					<input value="<?php echo html_escape(isset($seat->yearly_price) ? $seat->yearly_price : 0); ?>" type="number" step="0.01" min="0" name="yearly_price" placeholder="Enter 12 month plan price" class="form-control">
				</div>
			</div>
		</div>
	</div>
</div>
<div class="form-group">
	<label>Remarks</label>
	<textarea style="resize: none" type="text" name="remarks" placeholder="Enter remarks" class="form-control"><?php echo html_escape($seat->remarks); ?></textarea>
</div>

<button type="submit" class="mb-sm btn btn-primary">Update Seat</button>
<?php echo form_close(); ?>

<script>
	$('#edit_room').parsley();
</script>
