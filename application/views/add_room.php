<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Add Seat</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Add New Seat</h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-6 offset-lg-3">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <?php echo form_open('rooms/add', array('method' => 'post', 'data-parsley-validate' => 'ture')); ?>
                    <div class="form-group">
                        <label>Study Area / Room Name *</label>
                        <input type="text" name="roomnumber" placeholder="Enter study area or room name" data-parsley-required="true" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Seat No *</label>
                        <input type="text" name="room_number" placeholder="Enter seat number" data-parsley-required="true" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Floor / Section</label>
                        <input type="text" name="floor" placeholder="Enter floor or section" class="form-control">
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
                                        <input type="number" step="0.01" min="0" name="daily_rent" placeholder="Enter per day price" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Monthly Plan Price</label>
                                        <input type="number" step="0.01" min="0" name="monthly_rent" placeholder="Enter monthly plan price" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>3 Month Plan Price</label>
                                        <input type="number" step="0.01" min="0" name="quarterly_price" placeholder="Enter 3 month plan price" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>6 Month Plan Price</label>
                                        <input type="number" step="0.01" min="0" name="half_yearly_price" placeholder="Enter 6 month plan price" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>12 Month Plan Price</label>
                                        <input type="number" step="0.01" min="0" name="yearly_price" placeholder="Enter 12 month plan price" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea style="resize: none" type="text" name="remarks" placeholder="Enter remarks" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="mb-sm btn btn-primary">Save Seat</button>
                    <?php echo form_close(); ?>
                </div>
                <!-- end panel-body -->
            </div>
            <!-- end panel -->
        </div>
        <!-- end col-12 -->
    </div>
    <!-- end row -->
</div>
<!-- end #content -->
