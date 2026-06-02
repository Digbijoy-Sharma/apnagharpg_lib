<?php echo form_open('invoice_services/' . $param2, array('id' => 'edit_invoice_services', 'method' => 'post', 'data-parsley-validate' => 'true')); ?>
    <div class="row">
        <div class="col-md-12">
            <span style="margin-bottom: 10px" class="btn btn-sm btn-primary pull-right" id="add-more">
                <i class="fa fa-plus"></i>
                <span>Add more</span>
            </span>
        </div>
    </div>

    <div id="dynamic-field">
    <?php
        $invoice_services = $this->db->get_where('invoice_service', array('invoice_id' => $param2))->result_array();
        foreach ($invoice_services as $row):
    ?>
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label><?php echo $this->lang->line('service_name'); ?> *</label>
                    <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="service_ids[]">
                        <option value=""><?php echo $this->lang->line('select_invoice_service'); ?></option>
                        <?php
                        $services = $this->db->get('service')->result_array();
                        foreach ($services as $service) :
                        ?>
                            <option <?php if ($service['service_id'] == $row['service_id']) echo 'selected'; ?> value="<?php echo html_escape($service['service_id']); ?>"><?php echo html_escape($service['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('year'); ?> *</label>
                    <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="years[]">
                        <option value=""><?php echo $this->lang->line('select_year'); ?></option>
                        <option <?php if ($row['year'] == date('Y') - 4) echo 'selected'; ?> value="<?php echo date('Y') - 4; ?>"><?php echo date('Y') - 4; ?></option>
                        <option <?php if ($row['year'] == date('Y') - 3) echo 'selected'; ?> value="<?php echo date('Y') - 3; ?>"><?php echo date('Y') - 3; ?></option>
                        <option <?php if ($row['year'] == date('Y') - 2) echo 'selected'; ?> value="<?php echo date('Y') - 2; ?>"><?php echo date('Y') - 2; ?></option>
                        <option <?php if ($row['year'] == date('Y') - 1) echo 'selected'; ?> value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                        <option <?php if ($row['year'] == date('Y')) echo 'selected'; ?> value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                        <option <?php if ($row['year'] == date('Y') + 1) echo 'selected'; ?> value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
                        <option <?php if ($row['year'] == date('Y') + 2) echo 'selected'; ?> value="<?php echo date('Y') + 2; ?>"><?php echo date('Y') + 2; ?></option>
                        <option <?php if ($row['year'] == date('Y') + 3) echo 'selected'; ?> value="<?php echo date('Y') + 3; ?>"><?php echo date('Y') + 3; ?></option>
                        <option <?php if ($row['year'] == date('Y') + 4) echo 'selected'; ?> value="<?php echo date('Y') + 4; ?>"><?php echo date('Y') + 4; ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><?php echo $this->lang->line('month'); ?> *</label>
                    <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="months[]">
                        <option value=""><?php echo $this->lang->line('select_month'); ?></option>
                        <option <?php if ($row['month'] == 'January') echo 'selected'; ?> value="January"><?php echo $this->lang->line('january'); ?></option>
                        <option <?php if ($row['month'] == 'February') echo 'selected'; ?> value="February"><?php echo $this->lang->line('february'); ?></option>
                        <option <?php if ($row['month'] == 'March') echo 'selected'; ?> value="March"><?php echo $this->lang->line('march'); ?></option>
                        <option <?php if ($row['month'] == 'April') echo 'selected'; ?> value="April"><?php echo $this->lang->line('april'); ?></option>
                        <option <?php if ($row['month'] == 'May') echo 'selected'; ?> value="May"><?php echo $this->lang->line('may'); ?></option>
                        <option <?php if ($row['month'] == 'June') echo 'selected'; ?> value="June"><?php echo $this->lang->line('june'); ?></option>
                        <option <?php if ($row['month'] == 'July') echo 'selected'; ?> value="July"><?php echo $this->lang->line('july'); ?></option>
                        <option <?php if ($row['month'] == 'August') echo 'selected'; ?> value="August"><?php echo $this->lang->line('august'); ?></option>
                        <option <?php if ($row['month'] == 'September') echo 'selected'; ?> value="September"><?php echo $this->lang->line('september'); ?></option>
                        <option <?php if ($row['month'] == 'October') echo 'selected'; ?> value="October"><?php echo $this->lang->line('october'); ?></option>
                        <option <?php if ($row['month'] == 'November') echo 'selected'; ?> value="November"><?php echo $this->lang->line('november'); ?></option>
                        <option <?php if ($row['month'] == 'December') echo 'selected'; ?> value="December"><?php echo $this->lang->line('december'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    

	<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('update'); ?></button>
<?php echo form_close(); ?>

<script>
	$('#edit_invoice_services').parsley();
	FormPlugins.init();

	

    $(document).ready(function() {
		"use strict";

		var i = 0;
		$('#add-more').on('click', function() {
			i++;
			$('#dynamic-field').append(`
			<div id="row` + i + `">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('service_name'); ?> *</label>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="service_ids[]">
                                <option value=""><?php echo $this->lang->line('select_invoice_service'); ?></option>
                                <?php
                                $services = $this->db->get('service')->result_array();
                                foreach ($services as $service) :
                                ?>
                                    <option value="<?php echo html_escape($service['service_id']); ?>"><?php echo html_escape($service['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('year'); ?> *</label>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="years[]">
                                <option value=""><?php echo $this->lang->line('select_year'); ?></option>
                                <option value="<?php echo date('Y') - 4; ?>"><?php echo date('Y') - 4; ?></option>
                                <option value="<?php echo date('Y') - 3; ?>"><?php echo date('Y') - 3; ?></option>
                                <option value="<?php echo date('Y') - 2; ?>"><?php echo date('Y') - 2; ?></option>
                                <option value="<?php echo date('Y') - 1; ?>"><?php echo date('Y') - 1; ?></option>
                                <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                                <option value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
                                <option value="<?php echo date('Y') + 2; ?>"><?php echo date('Y') + 2; ?></option>
                                <option value="<?php echo date('Y') + 3; ?>"><?php echo date('Y') + 3; ?></option>
                                <option value="<?php echo date('Y') + 4; ?>"><?php echo date('Y') + 4; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('month'); ?> *</label>
                            <select style="width: 100%" class="form-control default-select2" data-parsley-required="true" name="months[]">
                                <option value=""><?php echo $this->lang->line('select_month'); ?></option>
                                <option value="January"><?php echo $this->lang->line('january'); ?></option>
                                <option value="February"><?php echo $this->lang->line('february'); ?></option>
                                <option value="March"><?php echo $this->lang->line('march'); ?></option>
                                <option value="April"><?php echo $this->lang->line('april'); ?></option>
                                <option value="May"><?php echo $this->lang->line('may'); ?></option>
                                <option value="June"><?php echo $this->lang->line('june'); ?></option>
                                <option value="July"><?php echo $this->lang->line('july'); ?></option>
                                <option value="August"><?php echo $this->lang->line('august'); ?></option>
                                <option value="September"><?php echo $this->lang->line('september'); ?></option>
                                <option value="October"><?php echo $this->lang->line('october'); ?></option>
                                <option value="November"><?php echo $this->lang->line('november'); ?></option>
                                <option value="December"><?php echo $this->lang->line('december'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label></label>
						    <button type="button" name="remove" id="` + i + `" class="btn btn-sm btn-danger btn_remove pull-right" style="margin-top: 11px">X</button>
                        </div>
					</div>
                </div>

				
			</div>
			`);

			FormPlugins.init();

            $('select:not(.normal)').each(function() {
                $(this).select2({
                    dropdownParent: $(this).parent()
                });
            });
		});

        $('select:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

		$(document).on('click', '.btn_remove', function() {
			var button_id = $(this).attr("id");
			$('#row' + button_id).remove();
		});
	});
</script>