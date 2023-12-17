<!DOCTYPE html>
<?php
	session_start();
	if(ISSET($_SESSION['login_id'])){
		header('location: home.php');
	}
?>
<html lang = "eng">
	<head>
		<title>MAJK Employee Attendance System</title>
		<?php include 'header.php' ?>
	</head>
	<body>
		<div class="modal fade" id="login_alert" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content panel-primary">
							<div class="modal-header panel-heading">
								<h6 class="modal-title">Login Failed</h6>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body">
        						<p>Your login attempt has failed. Please check your username and password and try again.</p>
							</div>

						
						</div>	
					</div>
		</div>

		<div class="button-container-admin bg-dark">
			<button class="adminbutton"><a href="../index.php">Employee Login</a></button>
		</div>
		<div id ="main" class="bg-dark">
		<div class = "container" >
			<div class = "col-lg-12">
			<div class = "row">
				<div class = "col-md-6 offset-md-3 ">
					<div class = "card login-field">
						<div class = "card-header">
							<h2> Admin Login</h2>
						</div>
						<div class = "card-body">
							<form id = "login-frm">
								<div id = "" class = "form-group">
									<label class = "control-label" >Username:</label>
									<input type = "text" name = "username" class = "form-control" required/>
								</div>
								<div id = "" class = "form-group">
									<label class = "control-label">Password:</label>
									<input type = "password" maxlength = "20" name = "password" class = "form-control" required/>
								</div>
								<br />
								<button type = "submit" class = "btn btn-secondary btn-block" >Login <i class="fa fa-arrow-right"></i></button>
							</form>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		</div>
	</body>
	<script src = "../assets/js/jquery.js"></script>
	<script src = "../assets/js/bootstrap.js"></script>

	<script>
		$(document).ready(function(){
			$('#login-frm').submit(function(e){
				e.preventDefault();
				$.ajax({
					url:"login.php",
					method:'POST',
					data:$(this).serialize(),
					error:err=>{
						console.log(err);
					},
					success:function(resp){
						if(resp == true){
							location.replace('home.php');
						} else{
							$('#login_alert .modal-title').html('Login Failed')
							$('#login_alert').modal('show')
						}
					}
				})
			})
		})
	</script>
</html>