<?php echo form_open('sms_invoice/' . $param2, array('id' => 'show_sms', 'method' => 'post')); ?>
<div class="form-group">
	<label><?php echo $this->lang->line('sms_preview'); ?></label>
	<p>
		<?php 
			echo $this->lang->line('sms_invoice_1') 
			. '#' 
			. $this->db->get_where('invoice', array('invoice_id' => $param2))->row()->invoice_number 
			. $this->lang->line('sms_invoice_2') 
			. date('d M, Y', $this->db->get_where('invoice', array('invoice_id' => $param2))->row()->due_date) 
			. '. '
			. $this->lang->line('sms_invoice_3') . $this->db->get_where('setting', array('name' => 'currency'))->row()->content . ' ' . number_format($param3) 
			. ' - '
			. $this->lang->line('from')
			. $this->db->get_where('setting', array('setting_id' => 1))->row()->content;
		?>
	</p>
</div>

<button type="submit" class="mb-sm btn btn-primary"><?php echo $this->lang->line('send_sms'); ?></button>
<?php echo form_close(); ?>