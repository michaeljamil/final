<!DOCTYPE html>
<?php
	require_once 'auth.php';
?>
<html lang = "eng">
	<head>
		<title>Attendance List | Employee Attendance Record System</title>
		<?php include('header.php') ?>
		
	</head>
	<body>
		<?php include('nav_bar.php') ?>
		<div class = "container-fluid admin" >
			<div class = "alert alert-primary">Attendance List</div>
			<div class = "modal fade" id = "delete" tabindex = "-1" role = "dialog" aria-labelledby = "myModallabel">
				
			</div>
			<div class = "well col-lg-12">
				<table id="table" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Attendancane Log ID</th>
							<th>Employee ID</th>
							<th>Name</th>
							<th>Date</th>
							<th>AM In</th>
							<th>AM Out</th>
							<th>PM In</th>
							<th>PM Out</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$attendance_qry = $conn->query("SELECT a.*,concat(e.firstname,' ',e.middlename,' ',e.lastname)
														as name, e.employee_id 
														FROM `attendance` a 
														inner join employee e 
														on a.employee_id = e.employee_id ") or die(mysqli_error());

						while ($row = $attendance_qry->fetch_array()) {
							// // Calculate status for morning (AM)
							// $am_late = (strtotime($row['am_in']) > strtotime('08:00:00')) ? 'Late' : 'On Time';
							// $am_undertime = (strtotime($row['am_out']) < strtotime('12:00:00')) ? 'Undertime' : 'On Time';

							// // Calculate status for afternoon (PM)
							// $pm_late = (strtotime($row['pm_in']) > strtotime('13:00:00')) ? 'Late' : 'On Time';
							// $pm_undertime = (strtotime($row['pm_out']) < strtotime('17:00:00')) ? 'Undertime' : 'On Time';

							// // Update 'am_late', 'am_undertime', 'pm_late', and 'pm_undertime' columns in the database
							// $update_status_qry = $conn->query("UPDATE `attendance` 
							// 								SET am_late = '$am_late', am_undertime = '$am_undertime', 
							// 									pm_late = '$pm_late', pm_undertime = '$pm_undertime' 
							// 								WHERE atlog_id = {$row['id']}");
							// if (!$update_status_qry) {
							// 	// Handle database update errors
							// 	echo "Error updating record: " . $conn->error;
							// }

						?>

							

							<tr>
								<td><?php echo $row['atlog_id'] ?></td>
								<td><?php echo $row['employee_id'] ?></td>
								<td><?php echo htmlentities($row['name']) ?></td>
								<td><?php echo date("F d, Y", strtotime($row['atlog_date'])) ?></td>
								<td><?php echo ($row['am_in'] !== null) ? date("h:i a", strtotime($row['am_in'])) : 'N/A'; ?></td>
								<td><?php echo ($row['am_out'] !== null) ? date("h:i a", strtotime($row['am_out'])) : 'N/A'; ?></td>
								<td><?php echo ($row['pm_in'] !== null) ? date("h:i a", strtotime($row['pm_in'])) : 'N/A'; ?></td>
								<td><?php echo ($row['pm_out'] !== null) ? date("h:i a", strtotime($row['pm_out'])) : 'N/A'; ?></td>
								<td>
									<center><button data-id="<?php echo $row['atlog_id'] ?>" class="btn btn-sm btn-outline-danger remove_log"
													type="button"><i class="fa fa-trash"></i></button></center>
								</td>
							</tr><?php

							
						}
						?>
					</tbody>
				</table>
			<br />
			<br />	
			<br />	
			</div>
		</div>
		
	</body>
	<script type = "text/javascript">
		$(document).ready(function(){
			$('#table').DataTable();
		});
	</script>
	<script type = "text/javascript">
		$(document).ready(function(){
			$('.remove_log').click(function(){
				var id=$(this).attr('data-id');
				var _conf = confirm("Are you sure to delete this data ?");
				if(_conf == true){
					$.ajax({
						url:'delete_log.php?id='+id,
						error:err=>console.log(err),
						success:function(resp){
							if(typeof resp != undefined){
								resp = JSON.parse(resp)
								if(resp.status == 1){
									alert(resp.msg);
									location.reload()
								}
							}
						}
					})
				}
			});
		});
	</script>
</html>