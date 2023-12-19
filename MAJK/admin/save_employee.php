<?php
	include 'db_connect.php';
		extract($_POST);
		if(empty($id)){
				$save=$conn->query("INSERT INTO `employee` 
									VALUES('', '$firstname', '$middlename', '$lastname', '$position')") or die(mysqli_error());
				if($save){
						echo json_encode(array('status'=>1,'msg'=>"Data successfully Saved"));
				}		
		}else{
			$save=$conn->query("UPDATE `employee` set firstname='$firstname',middlename='$middlename',lastname='$lastname',position='$position' where employee_id = $id ") or die(mysqli_error());
				if($save){
						echo json_encode(array('status'=>1,'msg'=>"Data successfully Updated"));
				}
		}	

$conn->close();
?>