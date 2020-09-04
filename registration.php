<!--This is Explicitely for front end users without dashboard access-->

<?php
include('php-includes/connect.php');
$capping = 500;
?>
<?php
//User cliced on join
if(isset($_GET['join_user'])){
	//$side='';
	$pin = mysqli_real_escape_string($con,$_GET['pin']);
	$email = mysqli_real_escape_string($con,$_GET['email']);
	$firstname = mysqli_real_escape_string($con,$_GET['firstname']);
	$surname = mysqli_real_escape_string($con,$_GET['surname']);
	$mobile = mysqli_real_escape_string($con,$_GET['mobile']);
	$address = mysqli_real_escape_string($con,$_GET['address']);
	$account = mysqli_real_escape_string($con,$_GET['account']);
	$under_userid = mysqli_real_escape_string($con,$_GET['under_userid']);
	//$password = $_GET['password'];
	$password = mysqli_real_escape_string($con,$_GET['password']);
	$confirm_password = mysqli_real_escape_string($con,$_GET['confirm_password']);


	
	$flag = 0;
	
	if($pin!='' && $email!='' && $mobile!='' && $address!='' && $account!='' && $under_userid!='' && $firstname!='' && $surname!='' && $password!='' && $confirm_password!=''){
		//User filled all the fields.
		if($password == $confirm_password){
			//password match
			if(pin_check($pin)){
				//Pin is ok
				if(email_check($email)){
					//Email is ok
					if(!email_check($under_userid)){
						//Under userid is ok
							$flag=1;	
					}
					else{
						//check under userid
						echo '<script>alert("Invalid referral email.");</script>';
					}
				}
				else{
					//check email
					echo '<script>alert("This Email already availble.");</script>';
				}
			}
			else{
				//check pin
				echo '<script>alert("Invalid pin");</script>';
			}
		}
		else{
			//check if password match with confirmation
		    echo '<script>alert("Password did not match, Please enter.");</script>';
		}
	}
	else{
		//check all fields are fill
		echo '<script>alert("Please fill all the fields.");</script>';
	}
	
	//Now we are heree
	//It means all the information is correct
	//Now we will save all the information
	if($flag==1){
		
		//generate random from the available user in tree and check if user has space
		$new_gen=randuser_generate();
		//collect side info
		$decided_side = side_decide($new_gen);

		//encrypt password
		$hash = password_hash($password, PASSWORD_DEFAULT);

		//Insert into User profile
		$query = mysqli_query($con,"insert into users(`email`,`password`,`firstname`,`surname`,`mobile`,`address`,`account`,`under_userid`,`side`) values('$email','$hash','$firstname','$surname','$mobile','$address','$account','$under_userid','')");

		//Insert into Tree
		//So that later on we can view tree.
		$query = mysqli_query($con,"insert into tree(`userid`) values('$email')");

		//update side in tree
		$query = mysqli_query($con,"update tree set `$decided_side`='$email' where id='$new_gen'");
		
		//Update pin status to close
		$query = mysqli_query($con,"update pin_list set status='close' where pin='$pin'");
		
		//Inset into Icome
		$query = mysqli_query($con,"insert into income (`userid`) values('$email')");
		echo mysqli_error($con);
		//This is the main part to join a user\
		//If you will do any mistake here. Then the site will not work.

		//update referral count 
		$k = mysqli_query($con,"select * from users where email='$under_userid'");
		$l = mysqli_fetch_array($k);
		$current_now_count = $l['count']+1;
		mysqli_query($con,"update users set `count`=$current_now_count where email='$under_userid'");

		//update referral bonus
		$kk = mysqli_query($con,"select * from users where email='$under_userid'");
		$ll = mysqli_fetch_array($kk);
		if ($ll['count'] > 25){
			$current_now_bonus = $ll['referral_bonus']+5000;
			mysqli_query($con,"update users set `referral_bonus`=$current_now_bonus where email='$under_userid'");
		}
		else{
			$current_now_bonus = $ll['referral_bonus']+1500;
			mysqli_query($con,"update users set `referral_bonus`=$current_now_bonus where email='$under_userid'");
		}
		
		
		//Update sidecount and Income.
		$temp_under_userid = $new_gen;
		$temp_side_count = $decided_side.'count'; //leftcount or rightcount
		
		$temp_side = $decided_side;
		/*$total_count=1;
		$i=1;
		while($total_count>0){
			$i;*/
			$q = mysqli_query($con,"select * from tree where id='$temp_under_userid'");
			$r = mysqli_fetch_array($q);
			$current_temp_side_count = $r[$temp_side_count]+1;
			/*$temp_under_userid;
			$temp_side_count;*/
			mysqli_query($con,"update tree set `$temp_side_count`=$current_temp_side_count where id='$temp_under_userid'");
			
		//Update Upline sidecount and income.

		//get the upline of the random user
		$uppp_under_userid = randuser_upline($new_gen);
		//get the sidecount
		$randuser_upline_side = randuser_upline_side($new_gen);
		$uppp_side_count = $randuser_upline_side.'count'; //leftcount or rightcount
		$uppp_side = $randuser_upline_side;
     
			$uppp_q = mysqli_query($con,"select * from tree where userid='$uppp_under_userid'");
			$uppp_r = mysqli_fetch_array($uppp_q);
			$current_uppp_side_count = $uppp_r[$uppp_side_count]+1;

			mysqli_query($con,"update tree set `$uppp_side_count`=$current_uppp_side_count where userid='$uppp_under_userid'");


		//update Income Table
		$inc_q = mysqli_query($con,"select * from tree where userid='$uppp_under_userid'");
		$inc_r = mysqli_fetch_array($inc_q);

		if($inc_r['leftcount'] == 3 & $inc_r['rightcount'] == 3){
			$upd_inc_q = mysqli_query($con,"select * from income where userid='$uppp_under_userid'");
			$upd_inc_r = mysqli_fetch_array($upd_inc_q);

			//update current Earning
			$current_earn_income = $upd_inc_r['current_earnings']+60000;

			mysqli_query($con, "update income set `current_earnings` = '$current_earn_income' where userid = '$uppp_under_userid' ");

			//update Lifetime Earning
			$lifetime_earn_income = $upd_inc_r['lifetime_earnings']+60000;

			mysqli_query($con, "update income set `lifetime_earnings` = '$lifetime_earn_income' where userid = '$uppp_under_userid' ");

		}
			
		//Loop}
		
		
		
		
		echo mysqli_error($con);
		
		echo '<script>alert("Registration Successful.");window.location.assign("registration.php");</script>';
	}
	
}
?><!--/join user-->
<?php 
//functions

//get random user upline
function randuser_upline($the_id){
	global $con;

	//convert id to email
	$query =mysqli_query($con,"select * from `tree` where `id`='$the_id'");
	$result = mysqli_fetch_array ($query);
	$the_email = $result['userid'];

	$query =mysqli_query($con,"select * from `tree` where `left`='$the_email'");
		if(mysqli_num_rows($query)>0){
			$result = mysqli_fetch_array ($query);
				return $result['userid'];
		  }else{
			$query2 =mysqli_query($con,"select * from `tree` where `right`='$the_email'");
		    if(mysqli_num_rows($query2)>0){
				$result2 = mysqli_fetch_array ($query2);
					return $result2['userid'];
			}else{
				return false;
			}
		}


}

//get random user upline side 
function randuser_upline_side($the_id){
	global $con;

	//convert id to email
	$query =mysqli_query($con,"select * from `tree` where `id`='$the_id'");
	$result = mysqli_fetch_array ($query);
	$the_email = $result['userid'];

	$query =mysqli_query($con,"select * from `tree` where `left`='$the_email'");
		if(mysqli_num_rows($query)>0){
			$result = mysqli_fetch_array ($query);
				return 'left';
		  }else{
			$query2 =mysqli_query($con,"select * from `tree` where `right`='$the_email'");
		    if(mysqli_num_rows($query2)>0){
				$result2 = mysqli_fetch_array ($query2);
					return 'right';
			}else{
				return false;
			}
		}


}

//get list of i.d from array and randomly pick one then send to randuser_generate function
function id_generate(){
	global $con;
	$sql="select `id` from `tree`";
	$query =mysqli_query($con, $sql);
	$storeArray = Array();
	while  ($result = mysqli_fetch_array($query)){
		$storeArray[] =  $result['id']; 
	}
	
	$rand_keys = array_rand($storeArray, 2);
	return $storeArray[$rand_keys[0]] . "\n";
	
}

//collect the random user from id generate function and use id checker function to confirm if he is in the table
function randuser_generate(){
	global $con;

	while(1+1){

	
		$generated_rand = id_generate();
		$check_2;

		$recv_4_id = idchecker($generated_rand);
		if ($recv_4_id = true){
			//chec for left side
			$check = side_check($generated_rand, 'left');
			$check_2=side_check($generated_rand, 'right');
			
			if ($check == true && $check_2 == true){
				return $generated_rand;
			}elseif ($check == true && $check_2 == false){
				return $generated_rand;
			}elseif ($check == false && $check_2 == true){
				return $generated_rand;
			}else {
				randuser_generate();
			}
			
		}else{
			randuser_generate();
		}
	
	}
		
	
}

// confirm if the random number is in the table of tree
function idchecker($prospective_id){
    global $con;
    $sql="select `id` from `tree`";
    $query =mysqli_query($con, $sql);
	while ($result = mysqli_fetch_assoc ($query)){
		if (in_array($prospective_id, $result)){
			return true;
		}
	
	}

	return false;

}
	
function side_decide($d_randd){
	global $con;
	$check_2;
	
	//chec for left side
	$check = side_check($d_randd, 'left');
	$check_2=side_check($d_randd, 'right');
	
	if ($check == true && $check_2 == true){
		return 'left';
	}elseif ($check == true && $check_2 == false){
		return 'left';
	}elseif ($check == false && $check_2 == true){
		return 'right';
	}else {
		randuser_generate();
	}
}
	

function pin_check($pin){
	global $con;
	
	$query =mysqli_query($con,"select * from pin_list where pin='$pin' and status='open'");
	if(mysqli_num_rows($query)>0){
		return true;
	}
	else{
		return false;
	}
}
function email_check($email){
	global $con;
	
	$query =mysqli_query($con,"select * from users where email='$email'");
	if(mysqli_num_rows($query)>0){
		return false;
	}
	else{
		return true;
	}
}
function side_check($rand_num,$side){
	global $con;
	
	$query =mysqli_query($con,"select * from tree where id='$rand_num'");
	$result = mysqli_fetch_array($query);
	$side_value = $result[$side];
	if($side_value==''){
		return true;
	}
	else{
		return false;
	}
}
function income($userid){
	global $con;
	$data = array();
	$query = mysqli_query($con,"select * from income where userid='$userid'");
	$result = mysqli_fetch_array($query);
	$data['day_bal'] = $result['day_bal'];
	$data['current_bal'] = $result['current_bal'];
	$data['total_bal'] = $result['total_bal'];
	
	return $data;
}
function tree($userid){
	global $con;
	$data = array();
	$query = mysqli_query($con,"select * from tree where userid='$userid'");
	$result = mysqli_fetch_array($query);
	$data['left'] = $result['left'];
	$data['right'] = $result['right'];
	$data['leftcount'] = $result['leftcount'];
	$data['rightcount'] = $result['rightcount'];
	
	return $data;
}
function getUnderId($userid){
	global $con;
	$query = mysqli_query($con,"select * from users where email='$userid'");
	$result = mysqli_fetch_array($query);
	return $result['under_userid'];
}
function getUnderIdPlace($userid){
	global $con;
	$query = mysqli_query($con,"select * from users where email='$userid'");
	$result = mysqli_fetch_array($query);
	return $result['side'];
}

?>
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
                                <label>Pin</label>
                                <input type="text" name="pin" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
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
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" required>
							</div>
							<div class="form-group">
                                <label>Surname</label>
                                <input type="text" name="surname" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Mobile</label>
                                <input type="text" name="mobile" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Bank Account Details</label>
                                <textarea name="account" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Referrer</label>
                                <input type="text" name="under_userid" class="form-control" required>
                            </div>
                            <!--
							<div class="form-group">
                                <label>Side</label><br>
                                <input type="radio" name="side" value="left"> Left
                                <input type="radio" name="side" value="right"> Right
                            </div>
                            -->
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
