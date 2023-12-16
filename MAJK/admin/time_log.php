<?php
include 'db_connect.php';
extract($_POST);
$data = array();

// Check if the required fields are set
if (!isset($eno, $type, $hour, $minute, $second, $period)) {
    $data['status'] = 2;
    $data['msg'] = 'Failed! Incomplete data.';
    echo json_encode($data);
    exit; // Terminate script execution
}

$currentDate = date('Y-m-d');

// Query the database to check if the employee exists
$qry = $conn->query("SELECT * from employee where employee_id ='$eno'");

if ($qry->num_rows > 0) {
    $emp = $qry->fetch_array();

    // Construct the time in a format suitable for database insertion
    $logTime = $hour . ':' . $minute . ':' . $second . ' ' . $period;

    // Insert the log into the 'attendance' table

	if ($type == 1) {
		$save_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, am_in) VALUES ('$currentDate', '{$emp['employee_id']}', '$logTime')");
		$logMessage = ' time in this morning';
	} elseif ($type == 2) {
		$save_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, am_out) VALUES ('$currentDate', '{$emp['employee_id']}', '$logTime')");
		$logMessage = ' time out this morning';
	} elseif ($type == 3) {
		$save_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, pm_in) VALUES ('$currentDate', '{$emp['employee_id']}', '$logTime')");
		$logMessage = ' time in this afternoon';
	} elseif ($type == 4) {
		$save_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, pm_out) VALUES ('$currentDate', '{$emp['employee_id']}', '$logTime')");
		$logMessage = ' time out this afternoon';
	}	

    $employee = ucwords($emp['firstname']);

    if ($save_log) {
        $data['status'] = 1;
        $data['msg'] = $employee . ', your ' . $logMessage . ' has been successfully recorded. <br/>';
    } else {
        $data['status'] = 2;
        $data['msg'] = 'Failed to record time.';
    }
} else {
    $data['status'] = 2;
    $data['msg'] = 'Failed! Unknown Employee Number';
}

echo json_encode($data);
$conn->close();
?>