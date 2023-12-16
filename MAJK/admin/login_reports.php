<!DOCTYPE html>
<?php
	require_once 'auth.php';
?>
<html lang="eng">
	<head>
		<title>Daily Report | Employee Attendance Record System</title>
		<?php include('header.php') ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
        <style>
            #calendar {
                width: cover;
                height: 50vh;
                border-radius: 10px;
            }
        </style>
	</head>
	<body>
		<?php include('nav_bar.php') ?>
		<div class="container-fluid admin">
			<div class="alert alert-primary">Login Reports</div>
			<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModallabel"></div>
			<div class="well col-lg-12">
				<div id="calendar"></div>

				<?php
					$events = [];
                    $attendance_qry = $conn->query("SELECT a.*, concat(e.firstname,' ',e.middlename,' ',e.lastname) as name, e.employee_id FROM `attendance` a inner join employee e on a.employee_id = e.employee_id ") or die(mysqli_error());
                    while ($row = $attendance_qry->fetch_array()) {
                        $event = [
                            'title' => $row['name'] . ' - ' . date('h:i A', strtotime($row['datetime_log'])),
                            'start' => date('Y-m-d H:i:s', strtotime($row['datetime_log'])),
                            'url' => 'javascript:void(0);',
                        ];
                        array_push($events, $event);

                        $allDayEvent = [
                            'title' => $row['name'] ,
                            'start' => date('Y-m-d', strtotime($row['datetime_log'])),
                            'url' => 'javascript:void(0);',
                        ];
                        array_push($events, $allDayEvent);
                    }
				?>
			</div>
		</div>
	</body>
	<script type="text/javascript">
        $(document).ready(function () {
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
                events: <?php echo json_encode($events); ?>
            });
        });
	</script>
</html>
