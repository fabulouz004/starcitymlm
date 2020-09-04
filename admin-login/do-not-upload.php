<!--This is Explicitely for pre registration of admin-->

<?php
include('php-includes/connect.php');
$capping = 500;
?>
<?php
//User cliced on join
if(isset($_GET['join_user'])){
	//$side='';
	$userid = mysqli_real_escape_string($con,$_GET['email']);
	//$password = $_GET['password'];
	$password = mysqli_real_escape_string($con,$_GET['password']);
	$confirm_password = mysqli_real_escape_string($con,$_GET['confirm_password']);


	
	$flag = 0;
	
		if($password == $confirm_password){
			//password match	
			$flag=1;									
		}
		else{
			//check if password match with confirmation
		    echo '<script>alert("Password did not match, Please enter.");</script>';
		}
	
	
	//Now we are heree
	//It means all the information is correct
	//Now we will save all the information
	if($flag==1){
		
		
		//encrypt password
		$hash = password_hash($password, PASSWORD_DEFAULT);

		//Insert into admin table
		$query = mysqli_query($con,"insert into admin(`userid`,`password`) values('$userid','$hash')");
	
		echo mysqli_error($con);
		
		echo '<script>alert("Registration Successful.");window.location.assign("index.php");</script>';
	}
	
}
?><!--/join user-->

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="OJ Technologies">

    <title>StarCity Hub  - Register</title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

 

</head>

<body>



            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header" align = "center">Register</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
				    <div class="col-lg-4">
					</div>
                	<div class="col-lg-4">
					<div class="jumbotron">
                    	<form method="get">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" required>
							</div>
							<div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
							<div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                        	<input type="submit" name="join_user" class="btn btn-primary" value="Join">
                        </div>
                        </form>
					</div><!--/.jumbotron-->
                    </div>
                </div><!--/.row-->
            </div>
            <!-- /.container-fluid -->
        >

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

</body>

</html>
