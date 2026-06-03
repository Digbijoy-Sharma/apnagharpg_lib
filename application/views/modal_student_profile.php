<?php
$param2 = isset($param2) ? $param2 : 0;
$tenant = $this->db->get_where('tenant', array('tenant_id' => $param2))->row_array();
if (!$tenant) {
    echo '<div class="alert alert-danger">Student not found.</div>';
    return;
}

// Lookups
$room          = $tenant['room_id'] ? $this->db->get_where('room', array('room_id' => $tenant['room_id']))->row() : null;
$profession    = $tenant['profession_id'] ? $this->db->get_where('profession', array('profession_id' => $tenant['profession_id']))->row() : null;
$id_type       = $tenant['id_type_id'] ? $this->db->get_where('id_type', array('id_type_id' => $tenant['id_type_id']))->row() : null;
$tenant_shifts = $this->model->get_tenant_shifts($tenant['tenant_id']);

$plan_labels = array(
    'per_day'     => 'Per Day Plan',
    'monthly'     => 'Monthly Plan',
    'quarterly'   => '3 Month Plan',
    'half_yearly' => '6 Month Plan',
    'yearly'      => '12 Month Plan'
);

$shifts_combined = array();
foreach ($tenant_shifts as $sh) {
    $shifts_combined[] = $sh['shift_name'] . ' (' . $sh['timing_label'] . ')';
}
$shifts_display = empty($shifts_combined) ? 'N/A' : implode(', ', $shifts_combined);
?>

<style>
    .student-profile .profile-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #fff;
        padding: 22px 26px;
        border-radius: 10px 10px 0 0;
    }
    .student-profile .profile-header h3 { margin: 0; font-weight: 600; }
    .student-profile .profile-header .sub { opacity: 0.85; font-size: 13px; }

    .student-profile .profile-photo {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border: 4px solid #fff;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .student-profile .doc-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px;
        background: #fafafa;
        text-align: center;
    }
    .student-profile .doc-card img {
        width: 100%;
        height: 220px;
        object-fit: contain;
        background: #fff;
        border-radius: 6px;
        cursor: zoom-in;
    }
    .student-profile .doc-card .doc-label {
        margin-top: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #374151;
    }
    .student-profile .doc-card .doc-missing {
        color: #b91c1c;
        font-style: italic;
        padding: 60px 10px;
        font-size: 12px;
    }

    .student-profile .info-list { list-style: none; padding: 0; margin: 0; }
    .student-profile .info-list li {
        display: flex;
        padding: 7px 0;
        border-bottom: 1px dashed #e5e7eb;
        font-size: 13px;
    }
    .student-profile .info-list li:last-child { border-bottom: 0; }
    .student-profile .info-list .label {
        flex: 0 0 160px;
        color: #6b7280;
        font-weight: 600;
    }
    .student-profile .info-list .value {
        flex: 1;
        color: #1f2937;
        word-break: break-word;
    }
    .student-profile .badge-soft-success { background: #d1fae5; color: #047857; }
    .student-profile .badge-soft-warning { background: #fef3c7; color: #92400e; }
</style>

<div class="student-profile">
    <div class="profile-header d-flex align-items-center" style="gap: 18px;">
        <img class="profile-photo"
             src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['image_link']); ?>"
             alt="<?php echo html_escape($tenant['name']); ?>"
             onerror="this.onerror=null;this.src='<?php echo base_url(); ?>assets/img/tenant.png';" />
        <div style="flex: 1;">
            <h3><?php echo html_escape($tenant['name']); ?></h3>
            <div class="sub">
                <?php if (!empty($tenant['status'])): ?>
                    <span class="badge badge-soft-success">Active Student</span>
                <?php else: ?>
                    <span class="badge badge-soft-warning">Inactive</span>
                <?php endif; ?>
                <?php echo html_escape($profession ? $profession->name : 'Student'); ?>
                &middot; Joined <?php echo date('d M, Y', $tenant['created_on']); ?>
            </div>
        </div>
        <button type="button" class="btn btn-white" onclick="$('#modal_student_profile').modal('hide');" aria-label="Close" style="opacity: 0.9;">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <div class="panel-body" style="padding: 22px 26px;">

        <div class="row">
            <div class="col-md-6">
                <h5 style="margin-top: 0; font-weight: 600; color: #4b5563; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Contact &amp; Personal</h5>
                <ul class="info-list">
                    <li><span class="label">Mobile</span><span class="value"><?php echo $tenant['mobile_number'] ? html_escape($tenant['mobile_number']) : 'N/A'; ?></span></li>
                    <li><span class="label">Email</span><span class="value"><?php echo $tenant['email'] ? html_escape($tenant['email']) : 'N/A'; ?></span></li>
                    <li><span class="label">Blood Group</span><span class="value"><?php echo $tenant['blood_group'] ? html_escape($tenant['blood_group']) : 'N/A'; ?></span></li>
                    <li><span class="label">Local Guardian</span><span class="value"><?php echo $tenant['lg_person'] ? html_escape($tenant['lg_person']) : 'N/A'; ?></span></li>
                    <li><span class="label">Guardian Contact</span><span class="value"><?php echo $tenant['lg_contact'] ? html_escape($tenant['lg_contact']) : 'N/A'; ?></span></li>
                    <li><span class="label">Emergency Person</span><span class="value"><?php echo $tenant['emergency_person'] ? html_escape($tenant['emergency_person']) : 'N/A'; ?></span></li>
                    <li><span class="label">Emergency Contact</span><span class="value"><?php echo $tenant['emergency_contact'] ? html_escape($tenant['emergency_contact']) : 'N/A'; ?></span></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5 style="margin-top: 0; font-weight: 600; color: #4b5563; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Seat &amp; Plan</h5>
                <ul class="info-list">
                    <li>
                        <span class="label">Seat</span>
                        <span class="value">
                            <?php if ($room): ?>
                                <?php echo html_escape($room->roomnumber . ' / Seat ' . $room->room_number); ?>
                                <?php if (!empty($room->floor)): ?> &middot; Floor <?php echo html_escape($room->floor); ?><?php endif; ?>
                            <?php else: ?>N/A<?php endif; ?>
                        </span>
                    </li>
                    <li>
                        <span class="label">Shifts</span>
                        <span class="value"><?php echo htmlspecialchars($shifts_display, ENT_QUOTES, 'UTF-8'); ?></span>
                    </li>
                    <li>
                        <span class="label">Plan</span>
                        <span class="value">
                            <?php if (!empty($tenant['plan_type']) && isset($plan_labels[$tenant['plan_type']])): ?>
                                <?php echo html_escape($plan_labels[$tenant['plan_type']]); ?>
                            <?php else: ?>N/A<?php endif; ?>
                        </span>
                    </li>
                    <li>
                        <span class="label">Lease Start</span>
                        <span class="value"><?php echo $tenant['lease_start'] ? date('d M, Y', $tenant['lease_start']) : 'N/A'; ?></span>
                    </li>
                    <li>
                        <span class="label">Lease End</span>
                        <span class="value"><?php echo $tenant['lease_end'] ? date('d M, Y', $tenant['lease_end']) : 'N/A'; ?></span>
                    </li>
                    <li>
                        <span class="label">ID Type</span>
                        <span class="value"><?php echo $id_type ? html_escape($id_type->name) : 'N/A'; ?></span>
                    </li>
                    <li>
                        <span class="label">ID Number</span>
                        <span class="value"><?php echo $tenant['id_number'] ? html_escape($tenant['id_number']) : 'N/A'; ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="row" style="margin-top: 12px;">
            <div class="col-md-6">
                <h5 style="font-weight: 600; color: #4b5563; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Home Address</h5>
                <ul class="info-list">
                    <li>
                        <span class="value">
                            <?php
                            $home = $tenant['home_address'];
                            echo ($home && $home !== '<br>') ? $home : 'N/A';
                            ?>
                        </span>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5 style="font-weight: 600; color: #4b5563; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Work / College Address</h5>
                <ul class="info-list">
                    <li>
                        <span class="value">
                            <?php
                            $work = $tenant['work_address'];
                            echo ($work && $work !== '<br>') ? $work : 'N/A';
                            ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <?php if (!empty($tenant['extra_note'])): ?>
        <div class="row" style="margin-top: 12px;">
            <div class="col-md-12">
                <h5 style="font-weight: 600; color: #4b5563; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Notes</h5>
                <ul class="info-list">
                    <li><span class="value"><?php echo nl2br(html_escape($tenant['extra_note'])); ?></span></li>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <hr style="margin: 22px 0;" />

        <h4 style="font-weight: 600; color: #1f2937; margin-bottom: 14px;">
            <i class="fa fa-id-card"></i> Documents &amp; Photos
        </h4>

        <div class="row">
            <div class="col-md-4">
                <div class="doc-card">
                    <?php if (!empty($tenant['image_link'])): ?>
                        <a href="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['image_link']); ?>" target="_blank">
                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['image_link']); ?>" alt="Profile photo" onerror="this.parentNode.parentNode.querySelector('.doc-missing').style.display='block';this.style.display='none';" />
                        </a>
                    <?php else: ?>
                        <div class="doc-missing"><i class="fa fa-image"></i> No profile photo</div>
                    <?php endif; ?>
                    <div class="doc-label">Profile Photo</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="doc-card">
                    <?php if (!empty($tenant['id_front_image_link'])): ?>
                        <a href="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['id_front_image_link']); ?>" target="_blank">
                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['id_front_image_link']); ?>" alt="ID front" onerror="this.parentNode.parentNode.querySelector('.doc-missing').style.display='block';this.style.display='none';" />
                        </a>
                    <?php else: ?>
                        <div class="doc-missing"><i class="fa fa-image"></i> No ID front image</div>
                    <?php endif; ?>
                    <div class="doc-label">ID Front Side</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="doc-card">
                    <?php if (!empty($tenant['id_back_image_link'])): ?>
                        <a href="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['id_back_image_link']); ?>" target="_blank">
                            <img src="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['id_back_image_link']); ?>" alt="ID back" onerror="this.parentNode.parentNode.querySelector('.doc-missing').style.display='block';this.style.display='none';" />
                        </a>
                    <?php else: ?>
                        <div class="doc-missing"><i class="fa fa-image"></i> No ID back image</div>
                    <?php endif; ?>
                    <div class="doc-label">ID Back Side</div>
                </div>
            </div>
        </div>

        <?php if (!empty($tenant['signed_agreement'])): ?>
        <hr style="margin: 22px 0;" />
        <h4 style="font-weight: 600; color: #1f2937; margin-bottom: 14px;">
            <i class="fa fa-file-text-o"></i> Signed Agreement
        </h4>
        <p>
            <a class="btn btn-primary btn-sm" href="<?php echo base_url(); ?>uploads/tenants/<?php echo html_escape($tenant['signed_agreement']); ?>" target="_blank">
                <i class="fa fa-download"></i> Download Signed Agreement
            </a>
        </p>
        <?php endif; ?>

    </div>
</div>
