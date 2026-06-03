<?php
// Build a list of all available shifts for the filter dropdown
$all_shifts = $this->db
    ->where('status', 1)
    ->order_by('shift_id', 'asc')
    ->get('study_shift')
    ->result_array();

// Pull all rooms (seats) with their area & status
$rooms = $this->db
    ->select('room_id, roomnumber, room_number, status, floor, remarks')
    ->from('room')
    ->get()
    ->result_array();

// Pull all active tenants with their multiple shifts
$tenants_by_room = array();
$tenant_rows = $this->db
    ->select('tenant_id, name, mobile_number, room_id, plan_type, lease_end')
    ->where('status', 1)
    ->where('room_id >', 0)
    ->get('tenant')
    ->result_array();

$tenant_shifts_map = array();
$ts_rows = $this->db
    ->select('ts.tenant_id, ts.shift_id, s.shift_name, s.timing_label')
    ->from('tenant_shift ts')
    ->join('study_shift s', 's.shift_id = ts.shift_id', 'left')
    ->get()
    ->result_array();
foreach ($ts_rows as $tsr) {
    $tenant_shifts_map[$tsr['tenant_id']][] = $tsr;
}

// Fall back to tenant.shift_id for any tenant that has no tenant_shift rows
foreach ($tenant_rows as $tenant_row) {
    if (empty($tenant_shifts_map[$tenant_row['tenant_id']])) {
        if (!empty($tenant_row['shift_id'])) {
            $sh = $this->db->get_where('study_shift', array('shift_id' => $tenant_row['shift_id']))->row_array();
            if ($sh) {
                $tenant_shifts_map[$tenant_row['tenant_id']][] = array(
                    'tenant_id'    => $tenant_row['tenant_id'],
                    'shift_id'     => $sh['shift_id'],
                    'shift_name'   => $sh['shift_name'],
                    'timing_label' => $sh['timing_label']
                );
            }
        }
    }
}

foreach ($tenant_rows as $tenant_row) {
    $shift_list = isset($tenant_shifts_map[$tenant_row['tenant_id']]) ? $tenant_shifts_map[$tenant_row['tenant_id']] : array();
    $primary_shift = !empty($shift_list) ? $shift_list[0] : null;
    $shift_name    = $primary_shift ? $primary_shift['shift_name'] : '';
    $shift_timing  = $primary_shift ? $primary_shift['timing_label'] : '';
    $tenants_by_room[$tenant_row['room_id']] = array(
        'tenant'      => $tenant_row,
        'shifts'      => $shift_list,
        'shift_name'  => $shift_name,
        'shift_timing'=> $shift_timing
    );
}

$plan_labels = array(
    'per_day'     => 'Per Day Plan',
    'monthly'     => 'Monthly Plan',
    'quarterly'   => '3 Month Plan',
    'half_yearly' => '6 Month Plan',
    'yearly'      => '12 Month Plan'
);

// Natural sort the rooms by their alphanumeric room_number (A1, A2, A3, A10, B1...)
$sort_keys = array();
foreach ($rooms as $i => $room) {
    $sort_keys[$i] = $room['roomnumber'] . '|' . $room['room_number'];
}
natsort($sort_keys);
$sorted_rooms = array();
foreach (array_keys($sort_keys) as $idx) {
    $sorted_rooms[] = $rooms[$idx];
}
$rooms = $sorted_rooms;

// Shift filter
$selected_shift_id = isset($_GET['shift']) ? (int) $_GET['shift'] : 0;
$selected_shift = null;
foreach ($all_shifts as $sh) {
    if ((int) $sh['shift_id'] === $selected_shift_id) {
        $selected_shift = $sh;
        break;
    }
}

$total_seats    = count($rooms);
$occupied_count = 0;
$vacant_count   = 0;
foreach ($rooms as $room) {
    $is_occupied = false;
    if (isset($tenants_by_room[$room['room_id']])) {
        $record = $tenants_by_room[$room['room_id']];
        if ($selected_shift_id > 0) {
            foreach ($record['shifts'] as $sh) {
                if ((int) $sh['shift_id'] === $selected_shift_id) {
                    $is_occupied = true;
                    break;
                }
            }
        } else {
            $is_occupied = true;
        }
    }
    if ($is_occupied) {
        $occupied_count++;
    } else {
        $vacant_count++;
    }
}
?>

<style>
    .seat-occupancy-toolbar {
        background: #fff;
        border-radius: 10px;
        padding: 18px 22px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .seat-stat {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        margin-right: 8px;
    }

    .seat-stat.total    { background: #e7f1ff; color: #1d4ed8; }
    .seat-stat.occupied { background: #d1fae5; color: #047857; }
    .seat-stat.vacant   { background: #fee2e2; color: #b91c1c; }

    .seat-filter-form .form-group {
        margin-bottom: 0;
    }

    .seat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 14px;
        padding: 22px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    }

    .seat-badge {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 86px;
        padding: 12px 8px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
        border: 2px solid transparent;
        text-align: center;
    }

    .seat-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .seat-badge .seat-label {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 4px;
        letter-spacing: 0.02em;
    }

    .seat-badge .seat-sub {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .seat-badge.occupied {
        background: #28a745;
        color: #fff;
        border-color: #1f9d3a;
    }

    .seat-badge.occupied .seat-sub {
        color: rgba(255, 255, 255, 0.9);
    }

    .seat-badge.vacant {
        background: #dc3545;
        color: #fff;
        border-color: #b02a37;
    }

    .seat-badge .seat-tooltip {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: #fff;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.15s ease;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .seat-badge .seat-tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1f2937;
    }

    .seat-badge:hover .seat-tooltip,
    .seat-badge:focus .seat-tooltip {
        opacity: 1;
    }

    .seat-legend {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
        margin-top: 10px;
        font-size: 13px;
        color: #4b5563;
    }

    .seat-legend .dot {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 4px;
        margin-right: 6px;
        vertical-align: middle;
    }

    .seat-legend .dot.green { background: #28a745; }
    .seat-legend .dot.red   { background: #dc3545; }
</style>

<!-- begin #content -->
<div id="content" class="content">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>"><?php echo $this->lang->line('dashboard'); ?></a></li>
        <li class="breadcrumb-item active">Seat Occupancy</li>
    </ol>
    <!-- end breadcrumb -->

    <!-- begin page-header -->
    <h1 class="page-header">
        Seat Occupancy
        <small>Live status of every seat in the library</small>
    </h1>
    <!-- end page-header -->

    <!-- begin toolbar -->
    <div class="seat-occupancy-toolbar">
        <form method="get" action="<?php echo base_url(); ?>seat_occupancy_chart" class="seat-filter-form row" id="shiftFilterForm" style="margin-bottom: 14px;">
            <div class="form-group col-md-4">
                <label style="font-weight: 600; font-size: 13px;">Filter by Shift</label>
                <select name="shift" id="shiftFilter" class="form-control" onchange="document.getElementById('shiftFilterForm').submit();">
                    <option value="0">All Shifts (overall occupancy)</option>
                    <?php foreach ($all_shifts as $sh): ?>
                        <option value="<?php echo (int) $sh['shift_id']; ?>" <?php if ($selected_shift_id === (int) $sh['shift_id']) echo 'selected'; ?>>
                            <?php echo html_escape($sh['shift_name'] . ' - ' . $sh['timing_label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-8" style="padding-top: 28px;">
                <?php if ($selected_shift_id > 0 && $selected_shift): ?>
                    <span class="badge badge-info" style="font-size: 13px; padding: 8px 12px;">
                        Showing occupancy for <?php echo html_escape($selected_shift['shift_name'] . ' (' . $selected_shift['timing_label'] . ')'); ?>
                    </span>
                    <a href="<?php echo base_url(); ?>seat_occupancy_chart" class="btn btn-link btn-sm">Clear filter</a>
                <?php endif; ?>
            </div>
        </form>
        <div>
            <span class="seat-stat total">Total Seats: <?php echo (int) $total_seats; ?></span>
            <span class="seat-stat occupied">Occupied: <?php echo (int) $occupied_count; ?></span>
            <span class="seat-stat vacant">Vacant: <?php echo (int) $vacant_count; ?></span>
        </div>
        <div class="seat-legend">
            <span><span class="dot green"></span> Occupied for the selected shift (click or hover for student details)</span>
            <span><span class="dot red"></span> Vacant for the selected shift (or no student has it)</span>
        </div>
    </div>
    <!-- end toolbar -->

    <?php if ($total_seats === 0): ?>
        <div class="alert alert-warning text-center">
            No seats have been added yet. Use the <strong>Add Seat</strong> option to start.
        </div>
    <?php else: ?>
        <!-- begin seat grid -->
        <div class="seat-grid">
            <?php foreach ($rooms as $room):
                $record = isset($tenants_by_room[$room['room_id']]) ? $tenants_by_room[$room['room_id']] : null;

                $is_occupied = false;
                $matched_shift = null;
                if ($record) {
                    if ($selected_shift_id > 0) {
                        foreach ($record['shifts'] as $sh) {
                            if ((int) $sh['shift_id'] === $selected_shift_id) {
                                $is_occupied = true;
                                $matched_shift = $sh;
                                break;
                            }
                        }
                    } else {
                        $is_occupied = true;
                        $matched_shift = !empty($record['shifts']) ? $record['shifts'][0] : null;
                    }
                }

                $tenant = $record ? $record['tenant'] : null;
                $seat_number = $room['room_number'] !== null && $room['room_number'] !== '' ? $room['room_number'] : $room['room_id'];
                $badge_class = $is_occupied ? 'occupied' : 'vacant';

                if ($is_occupied) {
                    $shift_label = $matched_shift ? $matched_shift['shift_name'] : 'Shift';
                    $all_shifts_for_tenant = $record ? $record['shifts'] : array();
                    $shift_names = array();
                    foreach ($all_shifts_for_tenant as $sh) {
                        $shift_names[] = $sh['shift_name'] . ' (' . $sh['timing_label'] . ')';
                    }
                    $badge_sub = $tenant['name'];
                } else {
                    $shift_label = 'Vacant';
                    $all_shifts_for_tenant = array();
                    $shift_names = array();
                    $badge_sub = 'Vacant';
                }

                $tooltip_lines = array();
                $tooltip_lines[] = 'Seat ' . htmlspecialchars($seat_number, ENT_QUOTES, 'UTF-8');
                if (!empty($room['floor'])) {
                    $tooltip_lines[] = 'Floor: ' . htmlspecialchars($room['floor'], ENT_QUOTES, 'UTF-8');
                }
                if ($is_occupied && $tenant) {
                    $tooltip_lines[] = 'Student: ' . htmlspecialchars($tenant['name'], ENT_QUOTES, 'UTF-8');
                    if (!empty($tenant['mobile_number'])) {
                        $tooltip_lines[] = 'Mobile: ' . htmlspecialchars($tenant['mobile_number'], ENT_QUOTES, 'UTF-8');
                    }
                    if (!empty($shift_names)) {
                        $tooltip_lines[] = 'Shifts: ' . htmlspecialchars(implode(', ', $shift_names), ENT_QUOTES, 'UTF-8');
                    }
                    if (!empty($tenant['plan_type']) && isset($plan_labels[$tenant['plan_type']])) {
                        $tooltip_lines[] = 'Plan: ' . htmlspecialchars($plan_labels[$tenant['plan_type']], ENT_QUOTES, 'UTF-8');
                    }
                    if (!empty($tenant['lease_end'])) {
                        $tooltip_lines[] = 'Lease End: ' . date('d M, Y', $tenant['lease_end']);
                    }
                } else {
                    if (!empty($room['remarks'])) {
                        $tooltip_lines[] = 'Note: ' . htmlspecialchars($room['remarks'], ENT_QUOTES, 'UTF-8');
                    } else {
                        $tooltip_lines[] = 'Click to assign a student';
                    }
                }
                $tooltip_text = implode(' &#8226; ', $tooltip_lines);
                $title_attr   = 'Seat ' . $seat_number . ' - ' . ($is_occupied ? $tenant['name'] : 'Vacant');
            ?>
                <div class="seat-badge <?php echo $badge_class; ?>"
                     title="<?php echo htmlspecialchars($title_attr, ENT_QUOTES, 'UTF-8'); ?>"
                     tabindex="0"
                     aria-label="<?php echo htmlspecialchars($title_attr, ENT_QUOTES, 'UTF-8'); ?>"
                     <?php if ($is_occupied): ?>
                         onclick="window.location='<?php echo base_url(); ?>tenants';"
                     <?php else: ?>
                         onclick="window.location='<?php echo base_url(); ?>rooms';"
                     <?php endif; ?>>
                    <span class="seat-tooltip"><?php echo $tooltip_text; ?></span>
                    <span class="seat-label">Seat <?php echo htmlspecialchars($seat_number, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="seat-sub"><?php echo htmlspecialchars($badge_sub, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- end seat grid -->
    <?php endif; ?>
</div>
