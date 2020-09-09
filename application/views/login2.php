<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href=".<?php echo base_url(); ?>assets/vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/main.css">
<!--===============================================================================================-->
</head>
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-85 p-b-20">
				<form class="login100-form validate-form">
					<span class="login100-form-title p-b-70">
						Welcome
					</span>
					<span class="login100-form-avatar">
						<img src="<?php echo base_url(); ?>assets/images/vf.jpg" alt="AVATAR">
					</span>

					<div class="wrap-input100 validate-input m-t-85 m-b-35" data-validate = "Enter username">
						<input class="input100" type="text" name="txtUserName" id="txtUserName" autofocus>
						<span class="focus-input100" data-placeholder="Username"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-50" data-validate="Enter password">
						<input class="input100" type="password" name="txtPassword" id="txtPassword">
						<span class="focus-input100" data-placeholder="Password"></span>

					</div>

					<div class="container-login100-form-btn">
						<button type="button" class="login100-form-btn" onclick="doLogin()">
							Login
						</button>
					</div>

					<div class="container-login100-form-btn" style="margin-top:30px;">
						<p id="error" class="text-center" style="color:red"></p>
					</div>

				</form>
			</div>
		</div>
	</div>

<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/popper.js"></script>
	<script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/vendor/daterangepicker/moment.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
	<script>
	function doLogin(){
		var userName = $('#txtUserName').val();
		var pwd = $('#txtPassword').val();
		if(userName.length == 0){
			$('#txtUserName').focus();
			return false;
		}

		if(pwd.length == 0){
			$('#txtPassword').focus();
			return false;
		}

		$.ajax({
			url:'controller/loginController.php?doLogin',
			type:'POST',
			cache:'false',
			data:{
				'txtUserName' : userName,
				'txtPassword' : pwd
			},
			success:function(rs){
				var rs = $.trim(rs);
				if(rs == 'success'){
					window.location.href = 'index.php';
				}else{
					$('#error').text(rs);
					$('#txtUserName').focus();
				}
			}
		});
	}


	$('#txtUserName').keyup(function(e){
		if(e.keyCode == 13){
			if($(this).val() != ''){
				$('#txtPassword').focus();
			}
		}
	});


	$('#txtPassword').keyup(function(e){
		if(e.keyCode == 13){
			if($(this).val() != '' && $('#txtUserName').val() != ''){
				doLogin();
			}
		}
	});


	</script>
</body>
</html>
