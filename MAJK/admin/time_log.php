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
        // If entry exists, check if the specific field is NULL
        $existing_entry = $check_entry->fetch_assoc();

        if ($type == 1 && $existing_entry['am_in'] === null) {
            $am_late = (strtotime($logTime) > strtotime('8:00:00')) ? 'Late' : 'On Time';
            
            $update_log = $conn->query("UPDATE attendance SET am_in = '$logTime', am_late = '$am_late' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time in this morning';
        
        } elseif ($type == 2 && $existing_entry['am_out'] === null) {
            $am_undertime = (strtotime($logTime) < strtotime('12:00:00')) ? 'Undertime' : 'No';
           
            $update_log = $conn->query("UPDATE attendance SET am_out = '$logTime', am_undertime = '$am_undertime' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time out this morning';
        } elseif ($type == 3 && $existing_entry['pm_in'] === null) {
            $pm_late = (strtotime($logTime) > strtotime('13:00:00')) ? 'Late' : 'On Time';

            $update_log = $conn->query("UPDATE attendance SET pm_in = '$logTime', pm_late = '$pm_late' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time in this afternoon';
        } elseif ($type == 4 && $existing_entry['pm_out'] === null) {
            $pm_undertime = (strtotime($logTime) < strtotime('17:00:00')) ? 'Undertime' : 'No';

            $update_log = $conn->query("UPDATE attendance SET pm_out = '$logTime', pm_undertime = '$pm_undertime' WHERE atlog_date = '$currentDate' AND employee_id = '{$emp['employee_id']}'");
            $logMessage = ' time out this afternoon';
        } else {
            $data['status'] = 2;
            $data['msg'] = 'You already have an entry for today!';
            echo json_encode($data);
            exit; // Terminate script execution
        }
    } else {
        // If entry doesn't exist, create a new entry
        $logMessage = ''; // Initialize $logMessage here

        if ($type == 1) {
            $create_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, am_in, am_out, pm_in, pm_out) VALUES ('$currentDate', '{$emp['employee_id']}', '$logTime', NULL, NULL, NULL)");
            $logMessage = ' time in this morning';
        } elseif ($type == 2) {
            $create_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, am_in, am_out, pm_in, pm_out) VALUES ('$currentDate', '{$emp['employee_id']}', NULL,'$logTime', NULL, NULL)");
            $logMessage = ' time out this morning';
        } elseif ($type == 3) {
            $create_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, am_in, am_out, pm_in, pm_out) VALUES ('$currentDate', '{$emp['employee_id']}', NULL, NULL, '$logTime', NULL)");
            $logMessage = ' time in this afternoon';
        } elseif ($type == 4) {
            $create_log = $conn->query("INSERT IGNORE INTO attendance (atlog_date, employee_id, am_in, am_out, pm_in, pm_out) VALUES ('$currentDate', '{$emp['employee_id']}', NULL, NULL, NULL, '$logTime')");
            $logMessage = ' time out this afternoon';
        }

        if ($create_log) {
            $data['status'] = 1;
            $data['msg'] = isset($emp['firstname']) ? $emp['firstname'] . ', your ' . $logMessage . ' has been successfully recorded. <br/>' : 'Your ' . $logMessage . ' has been successfully recorded. <br/>';
        } else {
            $data['status'] = 2;
            $data['msg'] = 'Failed to record time.';
        }
    }
    // Add success message if an update or new entry was successful
    $data['status'] = 1;
    $data['msg'] = $emp['firstname'] . ', your ' . $logMessage . ' has been successfully recorded. <br/>';
} else {
    $data['status'] = 2;
    $data['msg'] = 'Failed! Unknown Employee Number';
}
echo json_encode($data);
$conn->close();
?>
