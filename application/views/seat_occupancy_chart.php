<?php
$month_input = $this->input->get('month');
if (!preg_match('/^\d{4}-\d{2}$/', (string) $month_input)) {
    $month_input = date('Y-m');
}

$month_start = DateTime::createFromFormat('Y-m-d', $month_input . '-01');
if (!$month_start) {
    $month_start = new DateTime(date('Y-m-01'));
}
$month_start->setTime(0, 0, 0);

$month_end = clone $month_start;
$month_end->modify('last day of this month')->setTime(23, 59, 59);

$calendar_start = clone $month_start;
if ($calendar_start->format('N') != 1) {
    $calendar_start->modify('last monday');
}

$calendar_end = clone $month_end;
if ($calendar_end->format('N') != 7) {
    $calendar_end->modify('next sunday');
}

$previous_month = (clone $month_start)->modify('-1 month')->format('Y-m');
$next_month = (clone $month_start)->modify('+1 month')->format('Y-m');
$days_in_month = (int) $month_start->format('t');
$month_start_ts = $month_start->getTimestamp();
$month_end_ts = $month_end->getTimestamp();

$rooms = $this->db->order_by('roomnumber', 'asc')->order_by('room_number', 'asc')->get('room')->result_array();
$tenant_rows = $this->db->where('room_id >', 0)->where('status', 1)->get('tenant')->result_array();
$shift_rows = $this->db->where('status', 1)->order_by('shift_id', 'asc')->get('study_shift')->result_array();

$tenants_by_room = array();
foreach ($tenant_rows as $tenant_row) {
    if (!isset($tenants_by_room[$tenant_row['room_id']])) {
        $tenants_by_room[$tenant_row['room_id']] = $tenant_row;
    }
}

$shifts_by_id = array();
foreach ($shift_rows as $shift_row) {
    $shifts_by_id[$shift_row['shift_id']] = $shift_row;
}

$plan_labels = array(
    'per_day' => 'Per Day Plan',
    'monthly' => 'Monthly Plan',
    'quarterly' => '3 Month Plan',
    'half_yearly' => '6 Month Plan',
    'yearly' => '12 Month Plan'
);

$seat_cards = array();
$occupied_seat_count = 0;
$fully_available_count = 0;
$total_occupied_days = 0;

foreach ($rooms as $room) {
    $tenant = isset($tenants_by_room[$room['room_id']]) ? $tenants_by_room[$room['room_id']] : null;
    $occupied_lookup = array();
    $occupied_days = 0;

    if ($tenant) {
        $lease_start_ts = !empty($tenant['lease_start']) ? (int) $tenant['lease_start'] : $month_start_ts;
        $lease_end_ts = !empty($tenant['lease_end']) ? (int) $tenant['lease_end'] : $month_end_ts;
        $effective_start = max($lease_start_ts, $month_start_ts);
        $effective_end = min($lease_end_ts, $month_end_ts);

        if ($effective_start <= $effective_end) {
            $cursor = $effective_start;
            while ($cursor <= $effective_end) {
                $occupied_lookup[date('Y-m-d', $cursor)] = true;
                $occupied_days++;
                $cursor = strtotime('+1 day', $cursor);
            }
        }
    } elseif ((int) $room['status'] === 1) {
        $cursor = $month_start_ts;
        while ($cursor <= $month_end_ts) {
            $occupied_lookup[date('Y-m-d', $cursor)] = true;
            $occupied_days++;
            $cursor = strtotime('+1 day', $cursor);
        }
    }

    $occupancy_rate = $days_in_month > 0 ? round(($occupied_days / $days_in_month) * 100) : 0;
    $total_occupied_days += $occupied_days;

    if ($occupied_days > 0) {
        $occupied_seat_count++;
    } else {
        $fully_available_count++;
    }

    $shift_label = 'No Shift Assigned';
    if ($tenant && !empty($tenant['shift_id']) && isset($shifts_by_id[$tenant['shift_id']])) {
        $shift_label = $shifts_by_id[$tenant['shift_id']]['shift_name'] . ' (' . $shifts_by_id[$tenant['shift_id']]['timing_label'] . ')';
    }

    $seat_cards[] = array(
        'room' => $room,
        'tenant' => $tenant,
        'occupied_lookup' => $occupied_lookup,
        'occupied_days' => $occupied_days,
        'available_days' => max($days_in_month - $occupied_days, 0),
        'occupancy_rate' => $occupancy_rate,
        'shift_label' => $shift_label,
        'plan_label' => $tenant && !empty($tenant['plan_type']) && isset($plan_labels[$tenant['plan_type']]) ? $plan_labels[$tenant['plan_type']] : 'No Plan Assigned'
    );
}

$average_occupancy = (!empty($rooms) && $days_in_month > 0) ? round(($total_occupied_days / (count($rooms) * $days_in_month)) * 100) : 0;
?>

<style>
    .occupancy-summary-card {
        border-radius: 12px;
        border: 0;
        color: #fff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .occupancy-summary-card .card-body {
        padding: 18px 20px;
    }

    .occupancy-summary-card h4 {
        font-size: 13px;
        letter-spacing: 0.04em;
        margin-bottom: 8px;
        opacity: 0.9;
        text-transform: uppercase;
    }

    .occupancy-summary-card p {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .summary-total {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
    }

    .summary-occupied {
        background: linear-gradient(135deg, #11998e, #38ef7d);
    }

    .summary-available {
        background: linear-gradient(135deg, #ff9966, #ff5e62);
    }

    .summary-average {
        background: linear-gradient(135deg, #7f00ff, #e100ff);
    }

    .occupancy-toolbar {
        background: #fff;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .occupancy-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
        color: #6c757d;
        font-size: 13px;
    }

    .occupancy-legend span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .legend-box {
        width: 14px;
        height: 14px;
        border-radius: 4px;
        display: inline-block;
    }

    .legend-occupied {
        background: #28a745;
    }

    .legend-free {
        background: #f4f6f9;
        border: 1px solid #d9e0e7;
    }

    .legend-today {
        background: #fff3cd;
        border: 1px solid #ffe69c;
    }

    .seat-occupancy-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .seat-occupancy-header {
        padding: 18px 20px;
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .seat-occupancy-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .seat-occupancy-title h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    .seat-occupancy-title p {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 13px;
    }

    .seat-status-badge {
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .seat-status-badge.occupied {
        background: #d1fae5;
        color: #047857;
    }

    .seat-status-badge.available {
        background: #fee2e2;
        color: #b91c1c;
    }

    .occupancy-meta {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .occupancy-meta-item {
        background: #f8fafc;
        border-radius: 10px;
        padding: 12px;
    }

    .occupancy-meta-item .label {
        display: block;
        color: #6b7280;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .occupancy-meta-item .value {
        color: #111827;
        font-size: 15px;
        font-weight: 600;
    }

    .occupancy-progress {
        height: 8px;
        background: #e9ecef;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 12px;
    }

    .occupancy-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    .seat-occupancy-body {
        padding: 18px 20px 20px;
    }

    .occupancy-calendar {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    .occupancy-calendar-weekday,
    .occupancy-calendar-day {
        text-align: center;
        border-radius: 10px;
    }

    .occupancy-calendar-weekday {
        padding: 8px 4px;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        background: #f8fafc;
    }

    .occupancy-calendar-day {
        min-height: 56px;
        padding: 10px 6px;
        border: 1px solid #eef2f7;
        background: #fff;
        position: relative;
    }

    .occupancy-calendar-day .day-number {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        display: block;
    }

    .occupancy-calendar-day .day-note {
        font-size: 11px;
        margin-top: 4px;
        display: block;
    }

    .occupancy-calendar-day.outside-month {
        background: #f8fafc;
        opacity: 0.45;
    }

    .occupancy-calendar-day.occupied {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .occupancy-calendar-day.occupied .day-note {
        color: #047857;
        font-weight: 700;
    }

    .occupancy-calendar-day.free {
        background: #fff;
    }

    .occupancy-calendar-day.free .day-note {
        color: #9ca3af;
    }

    .occupancy-calendar-day.today {
        box-shadow: inset 0 0 0 2px #ffc107;
        background: #fffaf0;
    }

    .empty-occupancy-state {
        padding: 50px 20px;
        text-align: center;
        color: #6c757d;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 991px) {
        .occupancy-meta {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .seat-occupancy-title {
            flex-direction: column;
        }

        .occupancy-meta {
            grid-template-columns: 1fr;
        }

        .occupancy-calendar {
            gap: 6px;
        }

        .occupancy-calendar-day {
            min-height: 50px;
        }
    }
</style>

<div id="content" class="content">
    <ol class="breadcrumb pull-right">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>rooms">Seats</a></li>
        <li class="breadcrumb-item active">Occupancy Chart</li>
    </ol>

    <h1 class="page-header">Seat Occupancy Chart <small><?php echo htmlspecialchars($month_start->format('F Y')); ?></small></h1>

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="occupancy-summary-card summary-total card">
                <div class="card-body">
                    <h4>Total Seats</h4>
                    <p><?php echo count($rooms); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="occupancy-summary-card summary-occupied card">
                <div class="card-body">
                    <h4>Occupied In Month</h4>
                    <p><?php echo $occupied_seat_count; ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="occupancy-summary-card summary-available card">
                <div class="card-body">
                    <h4>Fully Available</h4>
                    <p><?php echo $fully_available_count; ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="occupancy-summary-card summary-average card">
                <div class="card-body">
                    <h4>Average Occupancy</h4>
                    <p><?php echo $average_occupancy; ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="occupancy-toolbar">
        <form method="get" class="row align-items-end">
            <div class="col-lg-3 col-md-4">
                <label class="font-weight-bold">Select Month</label>
                <input type="month" class="form-control" name="month" value="<?php echo htmlspecialchars($month_start->format('Y-m')); ?>">
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="font-weight-bold">Search Seat / Student</label>
                <input type="text" id="seat-occupancy-search" class="form-control" placeholder="Search by seat, area or student">
            </div>
            <div class="col-lg-6 col-md-4">
                <button type="submit" class="btn btn-primary">Load Chart</button>
                <a href="<?php echo base_url(); ?>seat_occupancy_chart?month=<?php echo $previous_month; ?>" class="btn btn-outline-secondary m-l-5">Previous Month</a>
                <a href="<?php echo base_url(); ?>seat_occupancy_chart?month=<?php echo $next_month; ?>" class="btn btn-outline-secondary m-l-5">Next Month</a>
            </div>
        </form>

        <div class="occupancy-legend">
            <span><i class="legend-box legend-occupied"></i> Occupied days</span>
            <span><i class="legend-box legend-free"></i> Free days</span>
            <span><i class="legend-box legend-today"></i> Today</span>
        </div>
    </div>

    <?php if (empty($seat_cards)) : ?>
        <div class="empty-occupancy-state">
            <h4>No seats available</h4>
            <p>Add seats first to view the occupancy calendar.</p>
        </div>
    <?php else : ?>
        <div class="row" id="seat-occupancy-grid">
            <?php foreach ($seat_cards as $seat_card) : ?>
                <?php
                $room = $seat_card['room'];
                $tenant = $seat_card['tenant'];
                $seat_title = trim(($room['roomnumber'] ? $room['roomnumber'] . ' / ' : '') . 'Seat ' . $room['room_number']);
                $tenant_name = $tenant ? $tenant['name'] : 'No student assigned';
                $search_blob = strtolower($seat_title . ' ' . $tenant_name . ' ' . $room['floor']);
                ?>
                <div class="col-lg-6 seat-occupancy-item" data-search="<?php echo html_escape($search_blob); ?>">
                    <div class="seat-occupancy-card">
                        <div class="seat-occupancy-header">
                            <div class="seat-occupancy-title">
                                <div>
                                    <h4><?php echo html_escape($seat_title); ?></h4>
                                    <p><?php echo $room['floor'] ? html_escape($room['floor']) : 'Floor / Section not added'; ?></p>
                                </div>
                                <span class="seat-status-badge <?php echo $seat_card['occupied_days'] > 0 ? 'occupied' : 'available'; ?>">
                                    <?php echo $seat_card['occupied_days'] > 0 ? 'Occupied In Selected Month' : 'Available In Selected Month'; ?>
                                </span>
                            </div>

                            <div class="occupancy-meta">
                                <div class="occupancy-meta-item">
                                    <span class="label">Student</span>
                                    <span class="value"><?php echo html_escape($tenant_name); ?></span>
                                </div>
                                <div class="occupancy-meta-item">
                                    <span class="label">Plan</span>
                                    <span class="value"><?php echo html_escape($seat_card['plan_label']); ?></span>
                                </div>
                                <div class="occupancy-meta-item">
                                    <span class="label">Shift</span>
                                    <span class="value"><?php echo html_escape($seat_card['shift_label']); ?></span>
                                </div>
                                <div class="occupancy-meta-item">
                                    <span class="label">Occupied / Free</span>
                                    <span class="value"><?php echo $seat_card['occupied_days']; ?> / <?php echo $seat_card['available_days']; ?> days</span>
                                </div>
                            </div>

                            <div class="occupancy-progress">
                                <div class="occupancy-progress-bar" style="width: <?php echo $seat_card['occupancy_rate']; ?>%;"></div>
                            </div>
                            <small class="text-muted d-block m-t-10"><?php echo $seat_card['occupancy_rate']; ?>% occupancy for <?php echo html_escape($month_start->format('F Y')); ?></small>
                        </div>

                        <div class="seat-occupancy-body">
                            <div class="occupancy-calendar">
                                <?php foreach (array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun') as $weekday) : ?>
                                    <div class="occupancy-calendar-weekday"><?php echo $weekday; ?></div>
                                <?php endforeach; ?>

                                <?php
                                $day_cursor = clone $calendar_start;
                                while ($day_cursor <= $calendar_end) :
                                    $day_key = $day_cursor->format('Y-m-d');
                                    $is_current_month = $day_cursor->format('m') === $month_start->format('m');
                                    $is_today = $day_key === date('Y-m-d');
                                    $is_occupied = isset($seat_card['occupied_lookup'][$day_key]);
                                    $classes = array('occupancy-calendar-day');

                                    if (!$is_current_month) {
                                        $classes[] = 'outside-month';
                                    }

                                    $classes[] = $is_occupied ? 'occupied' : 'free';

                                    if ($is_today) {
                                        $classes[] = 'today';
                                    }

                                    $title_text = $seat_title . ' - ' . ($is_occupied ? 'Occupied' : 'Available');
                                    if ($tenant && $is_occupied) {
                                        $title_text .= ' by ' . $tenant['name'];
                                    }
                                ?>
                                    <div class="<?php echo implode(' ', $classes); ?>" title="<?php echo html_escape($title_text); ?>">
                                        <span class="day-number"><?php echo $day_cursor->format('d'); ?></span>
                                        <span class="day-note"><?php echo $is_occupied ? 'Occupied' : 'Free'; ?></span>
                                    </div>
                                <?php
                                    $day_cursor->modify('+1 day');
                                endwhile;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    $('#seat-occupancy-search').on('keyup', function () {
        var value = $(this).val().toLowerCase().trim();

        $('.seat-occupancy-item').each(function () {
            var haystack = ($(this).data('search') || '').toString();
            $(this).toggle(haystack.indexOf(value) !== -1);
        });
    });
</script>
