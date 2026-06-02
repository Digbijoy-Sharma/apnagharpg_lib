<h3>
    <?php echo $this->security->xss_clean($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->subject); ?>
    &nbsp;
    <?php if ($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->status == 0) : ?>
        <span class="badge badge-warning"><?php echo $this->lang->line('open'); ?></span>
    <?php endif; ?>
    <?php if ($this->db->get_where('complaint', array('complaint_id' => $param2))->row()->status == 1) : ?>
        <span class="badge badge-primary"><?php echo $this->lang->line('closed'); ?></span>
    <?php endif; ?>
</h3>
<p><?php echo $this->lang->line('published_on'); ?>: <?php echo date('d M, Y', $this->db->get_where('complaint', array('complaint_id' => $param2))->row()->created_on); ?> &nbsp;&nbsp;&nbsp; <?php echo $this->lang->line('last_updated'); ?>: <?php echo date('d M, Y', $this->db->get_where('complaint', array('complaint_id' => $param2))->row()->timestamp); ?></p>
<hr>
<?php
$complaint_details = $this->db->get_where('complaint_details', array('complaint_id' => $param2))->result_array();
foreach ($complaint_details as $row) :
?>
    <?php if ($row['created_by'] == $this->session->userdata('user_id')) : ?>
        <div class="note note-info">
            <p><?php echo $row['content']; ?></p>
            <p><?php echo date('d M, Y', $row['created_on']); ?></p>
            <p>
                <?php
                $user_type =  $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->user_type;
                if ($user_type == 1) {
                    echo '- Admin';
                } else if ($user_type == 2) {
                    $person_id = $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->person_id;
                    echo '- ' . html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
                } else {
                    $person_id = $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->person_id;
                    echo '- ' . html_escape($this->db->get_where('tenant', array('tenant_id' => $person_id))->row()->name);
                }
                ?>
            </p>
        </div>
    <?php else : ?>
        <div class="note note-success note-with-right-icon">
            <p><?php echo $row['content']; ?></p>
            <p><?php echo date('d M, Y', $row['created_on']); ?></p>
            <p>
                <?php
                $user_type =  $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->user_type;
                if ($user_type == 1) {
                    echo '- Admin';
                } else if ($user_type == 2) {
                    $person_id = $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->person_id;
                    echo '- ' . html_escape($this->db->get_where('staff', array('staff_id' => $person_id))->row()->name);
                } else {
                    $person_id = $this->db->get_where('user', array('user_id' => $row['created_by']))->row()->person_id;
                    echo '- ' . html_escape($this->db->get_where('tenant', array('tenant_id' => $person_id))->row()->name);
                }
                ?>
            </p>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script>
    $('.modal-dialog').css('max-height', '250px', 'overflow-y', 'auto');
</script>