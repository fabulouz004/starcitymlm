<?php
include('php-includes/connect.php');
include('php-includes/check-login.php');
$userid = $_SESSION['userid'];
$capping = 500;
?>
<?php

//User cliced on recycle
if(isset($_GET['recycle_user'])){
	$pin = mysqli_real_escape_string($con,$_GET['pin']);
	$email = $userid;
	$empty = NULL;
	$date = date("y-m-d");
	$flag = 0;
	
	if($pin!='' ){
		//User filled all the fields.
		if(pin_check($pin)){
			//Pin is ok
			if (eligibility_check($email)){
				//Email is eligible for recycle
						$flag=1;
			}else{
				//check email
				echo '<script>alert("You are not eigible for recycling");</script>';
			}
		}else{
			//check pin
            echo '<script>alert("Invalid pin");</script>';
        }
    }else{
		//check all fields are fill
		echo '<script>alert("Please fill in your pin.");</script>';
    }
	
	//Now we are heree
	//It means user is eligible for recycling
	if($flag==1){
		
		//generate random from the available user in tree and check if user has space
		$new_gen=randuser_generate();
		//collect side info
        $decided_side = side_decide($new_gen);
        //get former upline info
        $former_upline = placement($email);
        //get former side from upline info
        $former_side = old_side($email);

        //delete user from tree
        $query = mysqli_query($con,"delete from tree where userid = '$email'" );

        //change user info on former side table to recycled
        $query = mysqli_query($con,"update tree set `$former_side`='recycled' where userid='$former_upline'");

        //Re-Insert into Tree
		$query = mysqli_query($con,"insert into tree(`userid`) values('$email')");

		//re-place user in a side in tree
		$query = mysqli_query($con,"update tree set `$decided_side`='$email' where id='$new_gen'");
		
		//Update pin status to close
        $query = mysqli_query($con,"update pin_list set status='close' where pin='$pin'");
        
        //update recycle list status
		//$query = mysqli_query($con,"update recycle_list set status='close' where email='$email'");
		
		//Add to Recycled list History
		$query = mysqli_query($con,"insert into recycled_list (`email`,`date`) values('$email','$date')");

		//Add to cashout eligiblity list
		$query = mysqli_query($con,"insert into cashout_eligibility_list (`email`,`date`) values('$email','$date')");

		//Update sidecount and Income.
		$temp_under_userid = $new_gen;
		$temp_side_count = $decided_side.'count'; //leftcount or rightcount
		$temp_side = $decided_side;

			$q = mysqli_query($con,"select * from tree where id='$temp_under_userid'");
			$r = mysqli_fetch_array($q);
			$current_temp_side_count = $r[$temp_side_count]+1;
			
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
	
		//Update Recycle Count
		$recyc_q = mysqli_query($con, "select * from users where email = '$email'");
		$recyc_r = mysqli_fetch_array($recyc_q);
		$current_recyc_count = $recyc_r['recycle_count']+1;

		mysqli_query($con, "update users set `recycle_count` = '$current_recyc_count' where `email` = '$email'");

		//Update User Level
		$userlvl_q = mysqli_query($con, "select * from users where email = '$email'");
		$userlvl_r = mysqli_fetch_array($userlvl_q);
		$current_userlvl_count = $userlvl_r['recycle_count'];
		if ($current_userlvl_count == 50){

			mysqli_query($con, "update users set `user_level` = 'Level 2' where `email` = '$email'");

		}elseif($current_userlvl_count == 250){

			mysqli_query($con, "update users set `user_level` = 'Level 3' where `email` = '$email'");

		}elseif($current_userlvl_count == 1250){

			mysqli_query($con, "update users set `user_level` = 'Level 4' where `email` = '$email'");

		}
		
		echo mysqli_error($con);
		
		echo '<script>alert("Recycle Successful.");</script>';
    };
};

?><!--/Recycle user-->
<?php 
//functions

//get the selected random user upline
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

//get the selected random user upline side 
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

//get former upline
function placement($email){
	global $con;

	$query =mysqli_query($con,"select * from `tree` where `left`='$email'");
		if(mysqli_num_rows($query)>0){
			$result = mysqli_fetch_array ($query);
				return $result['userid'];
		  }else{
			$query2 =mysqli_query($con,"select * from `tree` where `right`='$email'");
		    if(mysqli_num_rows($query2)>0){
				$result2 = mysqli_fetch_array ($query2);
					return $result2['userid'];
			}else{
				return false;
			}
		}


}

//get former side
function old_side($email){
    global $con;

	$query =mysqli_query($con,"select * from `tree` where `left`='$email'");
		if(mysqli_num_rows($query)>0){
			$result = mysqli_fetch_array ($query);
				return "left";
		  }else{
			$query2 =mysqli_query($con,"select * from `tree` where `right`='$email'");
		    if(mysqli_num_rows($query2)>0){
				$result2 = mysqli_fetch_array ($query2);
					return "right";
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
	global $con,$userid;
	
	$query =mysqli_query($con,"select * from pin_list where pin='$pin' and userid='$userid' and status='open'");
	if(mysqli_num_rows($query)>0){
		return true;
	}
	else{
		return false;
	}
}

//eligibility check
function eligibility_check($email){
	global $con;

	$query = mysqli_query($con,"select * from `tree` where `userid` = '$email'");
	$result = mysqli_fetch_array($query);
	$left_side_count = $result["leftcount"];
	$right_side_count = $result["rightcount"];	
	if($left_side_count==3 && $right_side_count==3){
			return true;	
	}else{
		return false;
	}

}


//check recycle list to confirm email
function email_check($email){
	global $con;
	
	$query =mysqli_query($con,"select * from recycle_list where email='$email'and status='open'");
	if(mysqli_num_rows($query)>0){
		return true;
	}
	else{
		return false;
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

    <title>Mlml Website  - Recycle</title>

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

    <div id="wrapper">

        <!-- Navigation -->
        <?php include('php-includes/menu.php'); ?>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Recycle</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                	<div class="col-lg-4">
                    	<form method="get">
                            <div class="form-group">
                                <label>Pin</label>
                                <input type="text" name="pin" class="form-control" required>
                            </div>
                            <div class="form-group">
                        	<input type="submit" name="recycle_user" class="btn btn-primary" value="Recycle">
                        </div>
                        </form>
                    </div>
				</div><!--/.row-->
				
				<div class="row">
                	<div class="col-lg-6">
                    	<br><br>
                    	<table class="table table-bordered table-striped">
                        	<tr>
                            	<th>S.n.</th>
                                <th>Recycled</th>
                                <th>Date</th>
                            </tr>
                            <?php 
							$i=1;
							$query = mysqli_query($con,"select * from recycled_list where `email`='$userid' order by `id` desc");
							if(mysqli_num_rows($query)>0){
								while($row=mysqli_fetch_array($query)){
									$d_date = $row['date'];
								?>
                                	<tr>
                                    	<td><?php echo $i; ?></td>
                                        <td>Successful Recycled</td>
                                        <td><?php echo $d_date; ?></td>
                                    </tr>
                                <?php
									$i++;
								}
							}
							else{
							?>
                            	<tr>
                                	<td colspan="3">You have not done any recycle yet.</td>
                                </tr>
                            <?php
							}
							?>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

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