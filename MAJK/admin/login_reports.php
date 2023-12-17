<!DOCTYPE html>
<?php
require_once 'auth.php';
include 'db_connect.php'; // Include the file that establishes the database connection
?>
<html lang="eng">

<head>
    <title>Login Reports | Employee Attendance Record System</title>
    <?php include('header.php') ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <style>
        .alert-primary {
            background-color: #3498db;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
        }

        #calendar {
            width: 100%;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        #searchInput {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php include('nav_bar.php') ?>
    <div class="container-fluid admin">
        <div class="alert alert-primary">Login Reports</div>
        <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModallabel"></div>
        <div class="well col-lg-12">
            <div class="form-group">
                <label for="searchInput">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Enter employee name">
            </div>

            <div id="calendar"></div>

            <?php
            $events = [];
            $employeeColors = [
                1 => '#3498db',
                2 => '#e74c3c',
                3 => '#2ecc71',
                4 => '#f39c12',
                5 => '#9b59b6',
            ];

            $attendance_qry = $conn->query("SELECT atlog_id, a.employee_id, CONCAT(e.firstname, ' ', e.middlename, ' ', e.lastname) AS name, atlog_date, am_in, am_out, pm_in, pm_out, am_late, am_undertime, pm_late, pm_undertime FROM `attendance` a INNER JOIN employee e ON a.employee_id = e.employee_id") or die(mysqli_error());

            while ($row = $attendance_qry->fetch_array()) {
                $employeeId = $row['employee_id'];

                if (!isset($employeeColors[$employeeId])) {
                    $employeeColors[$employeeId] = '#95a5a6';
                }

                // Check if "AM Time In" is not null before creating the event
                if (isset($row['am_in'])) {
                    // Create "AM Time In" event
                    $eventInAM = [
                        'title' => $row['name'] . ' (AM Time In) - ' . getStatus($row['am_late'], $row['am_undertime']) . ' - ' . $row['am_in'],
                        'start' => $row['atlog_date'] . 'T' . $row['am_in'], // ISO 8601 date-time format
                        'allDay' => false,
                        'color' => $employeeColors[$employeeId],
                    ];

                    // Add "AM Time In" event to the $events array
                    array_push($events, $eventInAM);
                }

                // Check if "AM Time Out" is not null before creating the event
                if (isset($row['am_out'])) {
                    // Create "AM Time Out" event
                    $eventOutAM = [
                        'title' => $row['name'] . ' (AM Time Out) - ' . getStatus($row['am_late'], $row['am_undertime']) . ' - ' . $row['am_out'],
                        'start' => $row['atlog_date'] . 'T' . $row['am_out'], // ISO 8601 date-time format
                        'allDay' => false,
                        'color' => $employeeColors[$employeeId],
                    ];

                    // Add "AM Time Out" event to the $events array
                    array_push($events, $eventOutAM);
                }

                // Check if "PM Time In" is not null before creating the event
                if (isset($row['pm_in'])) {
                    // Create "PM Time In" event
                    $eventInPM = [
                        'title' => $row['name'] . ' (PM Time In) - ' . getStatus($row['pm_late'], $row['pm_undertime']) . ' - ' . $row['pm_in'],
                        'start' => $row['atlog_date'] . 'T' . $row['pm_in'], // ISO 8601 date-time format
                        'allDay' => false,
                        'color' => $employeeColors[$employeeId],
                    ];

                    // Add "PM Time In" event to the $events array
                    array_push($events, $eventInPM);
                }

                // Check if "PM Time Out" is not null before creating the event
                if (isset($row['pm_out'])) {
                    // Create "PM Time Out" event
                    $eventOutPM = [
                        'title' => $row['name'] . ' (PM Time Out) - ' . getStatus($row['pm_late'], $row['pm_undertime']) . ' - ' . $row['pm_out'],
                        'start' => $row['atlog_date'] . 'T' . $row['pm_out'], // ISO 8601 date-time format
                        'allDay' => false,
                        'color' => $employeeColors[$employeeId],
                    ];

                    // Add "PM Time Out" event to the $events array
                    array_push($events, $eventOutPM);
                }
            }

            // Function to determine late or undertime status
            function getStatus($late, $undertime) {
                if ($late === 'Late') {
                    return 'Late';
                } elseif ($undertime === 'Undertime') {
                    return 'Undertime';
                } else {
                    return 'On Time';
                }
            }
            ?>
        </div>
    </div>
</body>

<script type="text/javascript">
    $(document).ready(function () {
        var events = <?php echo json_encode($events); ?>;
        var filteredEvents = events.slice();

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultDate: new Date(),
            navLinks: true,
            editable: false,
            eventLimit: true,
            displayEventTime: false,
            displayEventEnd: false,
            allDaySlot: false,
            events: events,
            eventClick: function (calEvent, jsEvent, view) {
                alert('Attendance: ' + calEvent.title);
            },
            viewRender: function (view, element) {
                $('.fc-axis.fc-widget-header').hide();
            }
        });

        $('#searchInput').on('input', function () {
            var searchString = $(this).val().toLowerCase();

            filteredEvents = events.filter(function (event) {
                return event.title.toLowerCase().includes(searchString);
            });

            $('#calendar').fullCalendar('removeEvents');
            $('#calendar').fullCalendar('addEventSource', filteredEvents);
        });
    });
</script>

</html>
