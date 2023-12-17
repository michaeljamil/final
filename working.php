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
$qry = $conn->query("SELECT * FROM employee WHERE employee_id ='$eno'");

if ($qry->num_rows > 0) {
    $emp = $qry->fetch_array();

    // Construct the time in a format suitable for database insertion
    $logTime = $hour . ':' . $minute . ':' . $second . ' ' . $period;

    // Check if an entry already exists for the day
    $check_entry = $conn->query("SELECT * FROM attendance WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");

    if ($check_entry->num_rows > 0) {
        // If entry exists, update the appropriate column based on $type
        if ($type == 1) {
            $update_log = $conn->query("UPDATE attendance SET am_in = '$logTime' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time in this morning';
        } elseif ($type == 2) {
            $update_log = $conn->query("UPDATE attendance SET am_out = '$logTime' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time out this morning';
        } elseif ($type == 3) {
            $update_log = $conn->query("UPDATE attendance SET pm_in = '$logTime' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time in this afternoon';
        } elseif ($type == 4) {
            $update_log = $conn->query("UPDATE attendance SET pm_out = '$logTime' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time out this afternoon';
        }

    } else {
        // If entry doesn't exist, create a new entry
        $create_log = $conn->query("INSERT INTO attendance (atlog_date, employee_id, am_in, am_out, pm_in, pm_out) VALUES ('$currentDate', '{$emp['employee_id']}', NULL, NULL, NULL, NULL)");

        if ($create_log) {
            $data['status'] = 1;
            $data['msg'] = $emp['firstname'] . ', your log entry has been successfully created. <br/>';
        } else {
            $data['status'] = 2;
            $data['msg'] = 'Failed to create time log entry.';
        }
    }
} else {
    $data['status'] = 2;
    $data['msg'] = 'Failed! Unknown Employee Number';
}

echo json_encode($data);
$conn->close();
?>
