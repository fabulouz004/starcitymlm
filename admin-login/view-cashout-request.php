<?php
include('php-includes/check-login.php');
include('php-includes/connect.php');
$product_amount = 300;
?>
<?php
//Clicked on send buton
if(isset($_POST['paid'])){
	$userid = mysqli_real_escape_string($con,$_POST['userid']);
	$id = mysqli_real_escape_string($con,$_POST['id']);
    $date = date("y-m-d");
    $new_count = 0;
    
    //update income table
    $upd_inc_q = mysqli_query($con,"select * from income where userid='$userid'");
    $upd_inc_r = mysqli_fetch_array($upd_inc_q);

    $current_earn_income = $upd_inc_r['current_earnings']-60000;

    mysqli_query($con, "update income set `current_earnings` = '$current_earn_income' where userid = '$userid' ");

    //Add to income recieved table 
    mysqli_query($con, "insert into income_received (`userid`,`amount`,`date`) values ('$userid','60000','$date')");

   //add to recycle list
   //$query = mysqli_query($con,"insert into recycle_list (`email`,`date`) values('$userid','$date')");

   //update usercount
   //mysqli_query($con,"update tree set `leftcount`=$new_count,`rightcount`=$new_count  where userid='$userid'");

    //delete or Update from Cashout Eligibility Status
	mysqli_query($con,"update cashout_eligibility_list set status='close' where email='$userid' and status = 'open' limit 1");
    //mysqli_query($con,"delete from cashout_eligibility_list where email = '$userid'" );

	//update request status
	mysqli_query($con,"update cashout_request set status='approved' where id='$id' limit 1");
	
	echo '<script>alert("User has been Paid successfully");window.location.assign("view-cashout-request.php");</script>';	
}

// Click on Decline button
if(isset($_POST['Decline'])){
	$userid = mysqli_real_escape_string($con,$_POST['userid']);
	$id = mysqli_real_escape_string($con,$_POST['id']);
    $date = date("y-m-d");
    mysqli_query($con,"update cashout_request set status='declined' where id='$id' limit 1");
	
	echo '<script>alert("Transaction Declined");window.location.assign("view-cashout-request.php");</script>';	

}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="O.J Technologies LTD">

    <title>StarCity Hub  - View CashOut Request</title>

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
                        <h1 class="page-header">Admin - View Cashout request</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                	<div class="col-lg-12">
                    	<div class="table-responsive">
                        	<table class="table table-striped table-bordered">
                            	<tr>
                                	<th>S.n.</th>
                                    <th>Userid</th>
                                    <!--
                                    <th>leftcount</th>
                                    <th>rightcount</th>
                                    -->
                                    <th>Account Details</th>
                                    <th>Paid</th>
                                    <th>Decline</th>
                                </tr>
                                <?php
									$query = mysqli_query($con,"select * from cashout_request where status='open'");
									if(mysqli_num_rows($query)>0){
										$i=1;
										while($row=mysqli_fetch_array($query)){
											$id = $row['id'];
											$userid = $row['userid'];
											

                                            $query_user = mysqli_query($con,"select * from users where email='$userid'");
											$result = mysqli_fetch_array($query_user);
											$account = $result['account'];
										?>
                                        	<tr>
                                            	<td><?php echo $i; ?></td>
                                                <td><?php echo $userid; ?></td>
                                                <!--
                                                <td><?php //echo $leftcount; ?></td>
                                                <td><?php //echo $rightcount; ?></td>
                                                -->
                                                <td><?php echo $account; ?></td>
                                                <form method="post">
                                                    <input type="hidden" name="userid" value="<?php echo $userid ?>">
                                                    <input type="hidden" name="id" value="<?php echo $id ?>">
                                                 	<td><input type="submit" name="paid" value="Paid" class="btn btn-primary"></td>
                                                </form>

                                                <form method = "post">
                                                    <input type="hidden" name="userid" value="<?php echo $userid ?>">
                                                    <input type="hidden" name="id" value="<?php echo $id ?>">    
                                                    <td><input type="submit" name="Decline" value = "Decline" class = "btn btn-danger"></td>
                                                </form>
                                            </tr>
                                        <?php
											$i++;
										}
									}
									else{
									?>
                                    	<tr>
                                        	<td colspan="5" align="center">no cashout request.</td>
                                        </tr>
                                    <?php
									}
								?>
                            </table>
                        </div><!--/.table-responsive-->
                    </div>
                </div><!--/.row-->
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
