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

                $attendance_qry = $conn->query("SELECT a.*, CONCAT(e.firstname, ' ', e.middlename, ' ', e.lastname) AS name FROM `attendance` a INNER JOIN employee e ON a.employee_id = e.employee_id") or die(mysqli_error());

                while ($row = $attendance_qry->fetch_array()) {
                    $employeeId = $row['employee_id'];

                    if (!isset($employeeColors[$employeeId])) {
                        $employeeColors[$employeeId] = '#95a5a6'; 
                    }

                    // Create "time in" event
                    $eventIn = [
                        'title' => $row['name'] . ' (Time In)',
                        'start' => $row['atlog_date'] . ' ' . $row['am_in'], // Adjust this according to your database structure
                        'allDay' => false,
                        'url' => 'javascript:void(0);',
                        'color' => $employeeColors[$employeeId], 
                    ];

                    // Create "time out" event
                    $eventOut = [
                        'title' => $row['name'] . ' (Time Out)',
                        'start' => $row['atlog_date'] . ' ' . $row['pm_out'], // Adjust this according to your database structure
                        'allDay' => false,
                        'url' => 'javascript:void(0);',
                        'color' => $employeeColors[$employeeId], 
                    ];

                    array_push($events, $eventIn, $eventOut);
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
            eventClick: function(calEvent, jsEvent, view) {
                alert('Attendance: ' + calEvent.title);
            },
            viewRender: function(view, element) {
                $('.fc-axis.fc-widget-header').hide();
            }
        });

        $('#searchInput').on('input', function() {
            var searchString = $(this).val().toLowerCase();
            
            filteredEvents = events.filter(function(event) {
                return event.title.toLowerCase().includes(searchString);
            });
            
            $('#calendar').fullCalendar('removeEvents');
            $('#calendar').fullCalendar('addEventSource', filteredEvents);
        });
    });
</script>
</html>
