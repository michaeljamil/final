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
						?>
							<tr>
								<td><?php echo $row['atlog_id'] ?></td>
								<td><?php echo $row['employee_id'] ?></td>
								<td><?php echo htmlentities($row['name']) ?></td>
								<td><?php echo date("F d, Y", strtotime($row['atlog_date'])) ?></td>
								<td><?php echo ($row['am_in'] !== null) ?  getFormattedTime($row['am_in']) : 'N/A'; ?></td>
								<td><?php echo ($row['am_out'] !== null) ?  getFormattedTime($row['am_out']) : 'N/A'; ?></td>
								<td><?php echo ($row['pm_in'] !== null) ?  getFormattedTime($row['pm_in']) : 'N/A'; ?></td>
								<td><?php echo ($row['pm_out'] !== null) ?  getFormattedTime($row['pm_out']) : 'N/A'; ?></td>
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
				<?php
					// Function to convert time to 12-hour format with AM/PM
					function getFormattedTime($time)
					{
						$formatted_time = date("h:i a", strtotime($time));
						return $formatted_time;
					}
				?>
		
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