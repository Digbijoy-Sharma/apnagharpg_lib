<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Add Student</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
    Add New Student
    </h1>
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin col-12 -->
        <div class="col-lg-12">
            <!-- begin panel -->
            <div class="panel panel-inverse">
                <!-- begin panel-body -->
                <div class="panel-body">
                    <?php echo form_open_multipart('tenants/add', array('method' => 'post', 'data-parsley-validate' => 'true')); ?>
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('name'); ?> *</label>
                                <input type="text" name="name" placeholder="<?php echo $this->lang->line('enter_name'); ?>" class="form-control" data-parsley-required="true">
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('mobile'); ?> *</label>
                                <input type="text" name="mobile_number" placeholder="<?php echo $this->lang->line('enter_mobile_number'); ?>" class="form-control" data-parsley-required="true">
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('email'); ?> (For student login)</label>
                                <input type="email" name="email" placeholder="<?php echo $this->lang->line('enter_email'); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Blood Group</label>
                                <input type="text" name="blood_group" placeholder="Blood Group" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('password'); ?> (For student login)</label>
                                <input type="text" name="password" id="password-indicator-visible" class="form-control m-b-5">
                                <div id="passwordStrengthDiv2" class="is0 m-t-5"></div>
                            </div>
                            <div class="note note-yellow m-b-15">
                                <span><?php echo $this->lang->line('default_password'); ?></span>
                            </div>
                            <div class="form-group">
                                <label>Student Image</label>
                                <br>
                                <img id="image-preview" width="90px" src="<?php echo base_url('assets/img/tenant.png'); ?>" class="media-object" />
                                <br>
                                <br>
                                <span class="btn btn-primary fileinput-button">
                                    <i class="fa fa-plus"></i>
                                    <span><?php echo $this->lang->line('add_file'); ?></span>
                                    <input onchange="readImageURL(this);" class="form-control" type="file" name="image_link">
                                </span>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('id_type'); ?></label>
                                <select style="width: 100%" class="form-control default-select2" name="id_type_id">
                                    <option value=""><?php echo $this->lang->line('select_id_type'); ?></option>
                                    <?php
                                    $id_types = $this->db->get('id_type')->result_array();
                                    foreach ($id_types as $id_type) :
                                    ?>
                                        <option value="<?php echo html_escape($id_type['id_type_id']); ?>"><?php echo html_escape($id_type['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('id_number'); ?></label>
                                <input name="id_number" type="text" placeholder="<?php echo $this->lang->line('enter_id_number'); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Student ID Image</label>
                                <br>
                                <img id="id-front-preview" width="90px" src="<?php echo base_url('assets/img/tenant.png'); ?>" class="media-object" />
                                <img id="id-back-preview" width="90px" src="<?php echo base_url('assets/img/tenant.png'); ?>" class="media-object" />
                                <br>
                                <br>
                                <span class="btn btn-primary fileinput-button">
                                    <i class="fa fa-plus"></i>
                                    <span><?php echo $this->lang->line('add_file_front'); ?></span>
                                    <input onchange="readIdFrontURL(this);" class="form-control" type="file" name="id_front_image_link">
                                </span>
                                <span class="btn btn-primary fileinput-button">
                                    <i class="fa fa-plus"></i>
                                    <span><?php echo $this->lang->line('add_file_back'); ?></span>
                                    <input onchange="readIdBackURL(this);" class="form-control" type="file" name="id_back_image_link">
                                </span>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label>Plan Duration</label>
                                <div class="input-group input-daterange">
                                    <input type="text" class="form-control" name="lease_start" placeholder="Plan start date" />
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control" name="lease_end" placeholder="Plan end date (auto)" readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('home_address'); ?></label>
                                <input name="home_address_line_1" type="text" placeholder="<?php echo $this->lang->line('enter_home_address_line_1'); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <input name="home_address_line_2" type="text" placeholder="<?php echo $this->lang->line('enter_home_address_line_2'); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Parents Name</label>
                                <input type="text" name="emergency_person" placeholder="Parent's Name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Parent's Contact Number</label>
                                <input type="text" name="emergency_contact" placeholder="Patrent's Contact Number" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Local Guardian Contact Number</label>
                                <input type="text" name="lg_person" placeholder="Local Guardian Contact Number" class="form-control">
                            </div>
                             <div class="form-group">
                                <label>Local Guardian Contact Number</label>
                                <input type="text" name="lg_contact" placeholder="Local Guardian Contact Number" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Seat</label>
                                <select style="width: 100%" class="form-control default-select2" name="room_id">
                                    <option value="">Select seat</option>
                                    <?php
                                    $rooms = $this->db->get_where('room', array('status' => 0))->result_array();
                                    foreach ($rooms as $room) :
                                    ?>
                                        <option value="<?php echo html_escape($room['room_id']); ?>"
                                            data-daily="<?php echo html_escape($room['daily_rent']); ?>"
                                            data-monthly="<?php echo html_escape($room['monthly_rent']); ?>"
                                            data-quarterly="<?php echo html_escape(isset($room['quarterly_price']) ? $room['quarterly_price'] : 0); ?>"
                                            data-half-yearly="<?php echo html_escape(isset($room['half_yearly_price']) ? $room['half_yearly_price'] : 0); ?>"
                                            data-yearly="<?php echo html_escape(isset($room['yearly_price']) ? $room['yearly_price'] : 0); ?>">
                                            <?php echo html_escape($room['roomnumber']); ?> -> Seat <?php echo html_escape($room['room_number']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="note note-yellow m-b-15">
                                <span>To assign a seat, you must activate the student.</span>
                            </div>
                            <div class="form-group">
                                <label>Shifts *</label>
                                <select style="width: 100%" class="form-control default-select2" name="shift_ids[]" id="shift_ids" multiple="multiple" data-parsley-required="true" data-placeholder="Select one or more shifts">
                                    <?php foreach ($this->db->order_by('shift_id', 'asc')->get('study_shift')->result_array() as $shift) : ?>
                                        <option value="<?php echo html_escape($shift['shift_id']); ?>"><?php echo html_escape($shift['shift_name'] . ' - ' . $shift['timing_label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple shifts. A student's plan price = sum of selected shifts.</small>
                            </div>
                            <div class="form-group">
                                <label>Plan *</label>
                                <select style="width: 100%" class="form-control default-select2" name="plan_type" data-parsley-required="true">
                                    <option value="">Select plan</option>
                                    <option value="per_day">Per Day Plan</option>
                                    <option value="monthly">Monthly Plan</option>
                                    <option value="quarterly">3 Month Plan</option>
                                    <option value="half_yearly">6 Month Plan</option>
                                    <option value="yearly">12 Month Plan</option>
                                </select>
                            </div>
                            <div class="note note-info m-b-15">
                                <span id="seat-plan-price-info">Select a seat and plan to see the seat-wise plan price.</span>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('profession'); ?></label>
                                <select style="width: 100%" class="form-control default-select2" name="profession_id">
                                    <option value=""><?php echo $this->lang->line('select_profession'); ?></option>
                                    <?php
                                    $professions = $this->db->get('profession')->result_array();
                                    foreach ($professions as $profession) :
                                    ?>
                                        <option value="<?php echo html_escape($profession['profession_id']); ?>"><?php echo html_escape($profession['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('work_address'); ?></label>
                                <input name="work_address_line_1" type="text" placeholder="<?php echo $this->lang->line('enter_work_address_line_1'); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <input name="work_address_line_2" type="text" placeholder="<?php echo $this->lang->line('enter_work_address_line_2'); ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('status'); ?> *</label>
                                <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="status">
                                    <option value=""><?php echo $this->lang->line('select_status'); ?></option>
                                    <option value="1"><?php echo $this->lang->line('active'); ?></option>
                                    <option value="0"><?php echo $this->lang->line('inactive'); ?></option>
                                </select>
                            </div>
                            <div class="note note-yellow m-b-15">
                                <span>To activate a student, you must assign a seat.</span>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('extra_note'); ?></label>
                                <textarea style="resize: none" type="text" name="extra_note" placeholder="<?php echo $this->lang->line('enter_extra_note'); ?>" class="form-control"></textarea>
                            </div>
                        </div>
                    </div><br>
                     <div class="checkbox-group">
      <input type="checkbox" id="rules" name="rules" required>
      <label class="checkbox-label" for="rules">
        I will follow all the rules and regulations of the study library. If I am caught breaking any rules, the library owner has full rights to suspend my access immediately.
      </label>
    </div><br>
    <div class="checkbox-group">
      <input type="checkbox" id="drugs" name="drugs" required>
      <label class="checkbox-label" for="drugs">
        I will not take any kind of drugs or narcotics inside the library premises. If I get caught with any such items, I will be the only one responsible.
      </label>
    </div><br>

                    <button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('submit'); ?></button>
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

<script>
    function readImageURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function readIdFrontURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#id-front-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function readIdBackURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#id-back-preview')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<script>
    function updatePlanEndDate() {
        var startDate = $('input[name="lease_start"]').val();
        var planType = $('select[name="plan_type"]').val();

        if (!startDate || !planType) {
            $('input[name="lease_end"]').val('');
            return;
        }

        var parts = startDate.split('/');
        if (parts.length !== 3) {
            $('input[name="lease_end"]').val('');
            return;
        }

        var start = new Date(parts[2], parts[0] - 1, parts[1]);
        if (Number.isNaN(start.getTime())) {
            $('input[name="lease_end"]').val('');
            return;
        }

        if (planType === 'per_day') {
            $('input[name="lease_end"]').val(startDate);
            return;
        }

        var end = new Date(start);
        var monthsToAdd = 1;
        if (planType === 'quarterly') monthsToAdd = 3;
        if (planType === 'half_yearly') monthsToAdd = 6;
        if (planType === 'yearly') monthsToAdd = 12;

        end.setMonth(end.getMonth() + monthsToAdd);
        end.setDate(end.getDate() - 1);

        var month = String(end.getMonth() + 1).padStart(2, '0');
        var day = String(end.getDate()).padStart(2, '0');
        var year = end.getFullYear();
        $('input[name="lease_end"]').val(month + '/' + day + '/' + year);
    }

    $('input[name="lease_start"]').on('change', updatePlanEndDate);
    $('select[name="plan_type"]').on('change', updatePlanEndDate);

    function updateSeatPlanPriceInfo() {
        var planType = $('select[name="plan_type"]').val();
        var selectedSeat = $('select[name="room_id"] option:selected');
        var price = '';
        var label = '';

        if (!selectedSeat.val() || !planType) {
            $('#seat-plan-price-info').text('Select a seat and plan to see the seat-wise plan price.');
            return;
        }

        if (planType === 'per_day') {
            price = selectedSeat.data('daily');
            label = 'Per Day Plan';
        } else if (planType === 'monthly') {
            price = selectedSeat.data('monthly');
            label = 'Monthly Plan';
        } else if (planType === 'quarterly') {
            price = selectedSeat.data('quarterly');
            label = '3 Month Plan';
        } else if (planType === 'half_yearly') {
            price = selectedSeat.data('half-yearly');
            label = '6 Month Plan';
        } else if (planType === 'yearly') {
            price = selectedSeat.data('yearly');
            label = '12 Month Plan';
        }

        $('#seat-plan-price-info').text(label + ' price for selected seat: Rs. ' + (parseFloat(price || 0).toFixed(2)));
    }

    $('select[name="room_id"]').on('change', updateSeatPlanPriceInfo);
    $('select[name="plan_type"]').on('change', updateSeatPlanPriceInfo);
    updateSeatPlanPriceInfo();
</script>
