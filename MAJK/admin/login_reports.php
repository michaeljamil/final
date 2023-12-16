<!DOCTYPE html>
<?php
    require_once 'auth.php';
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
            <!-- Search input -->
            <div class="form-group">
                <label for="searchInput">Search:</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Enter employee name">
            </div>

            <div id="calendar"></div>

            <?php
                $events = [];
                $employeeColors = [
                    1 => '#3498db', // Blue
                    2 => '#e74c3c', // Red
                    3 => '#2ecc71', // Green
                    4 => '#f39c12', // Orange
                    5 => '#9b59b6', // Purple
                    // Add more colors as needed
                ];

                $attendance_qry = $conn->query("SELECT a.*, concat(e.firstname,' ',e.middlename,' ',e.lastname) as name, e.employee_id FROM `attendance` a inner join employee e on a.employee_id = e.employee_id ") or die(mysqli_error());

                while ($row = $attendance_qry->fetch_array()) {
                    $employeeId = $row['employee_id'];

                    // If the employee doesn't have a color assigned, assign a default color
                    if (!isset($employeeColors[$employeeId])) {
                        $employeeColors[$employeeId] = '#95a5a6'; // Gray
                    }

                    // Event for specific time
                    $event = [
                        'title' => $row['name'],
                        'start' => date('Y-m-d H:00:s', strtotime($row['datetime_log'])), // Only show hour
                        'allDay' => false,
                        'url' => 'javascript:void(0);',
                        'color' => $employeeColors[$employeeId], // Assign color based on employee
                    ];

                    array_push($events, $event);
                }
            ?>
           
        </div>
    </div>
</body>
<script type="text/javascript">
    $(document).ready(function () {
        var events = <?php echo json_encode($events); ?>;
        var filteredEvents = events.slice(); // Create a copy of the events array for filtering

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
            displayEventTime: false, // Hide event time
            displayEventEnd: false,  // Hide event end time
            allDaySlot: false,       // Remove the all-day slot
            events: events,
            eventClick: function(calEvent, jsEvent, view) {
                alert('Attendance: ' + calEvent.title);
                // You can replace the alert with your custom logic for detailed viewing
            },
            viewRender: function(view, element) {
                // Hide the employee names below the calendar
                $('.fc-axis.fc-widget-header').hide();
            }
        });

        // Add search functionality
        $('#searchInput').on('input', function() {
            var searchString = $(this).val().toLowerCase();

            // Filter events based on the search string
            filteredEvents = events.filter(function(event) {
                return event.title.toLowerCase().includes(searchString);
            });

            // Remove existing events from the calendar
            $('#calendar').fullCalendar('removeEvents');

            // Add filtered events to the calendar
            $('#calendar').fullCalendar('addEventSource', filteredEvents);
        });
    });
</script>
</html>
