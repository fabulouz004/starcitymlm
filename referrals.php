<?php
include('php-includes/check-login.php');
include('php-includes/connect.php');
$userid = $_SESSION['userid'];
?>

<?php
$email=$userid;

//update user request to bonus table
function bonus_request($email,$amnt){
    global $con;
    $date = date("y-m-d");

    //get overallcount and referral bonus
    $query =mysqli_query($con,"select * from `users` where `email`='$email'");
	$result = mysqli_fetch_array ($query);
    $gotten_overallcount = $result['count'];
    $gotten_referral_bonus = $result['referral_bonus'];


    //add info to table
    $query = mysqli_query($con,"insert into bonus_request(`userid`, `date`,`amount`,`overall_count`,`current_bal`) values('$email', '$date','$amnt','$gotten_overallcount','$gotten_referral_bonus')");


    echo '<script>alert("Bonus Payment Request Sent Successfully.");</script>';

}


if (isset($_GET['pay'])){
    $amnt = mysqli_real_escape_string($con,$_GET['amnt']);
    
    bonus_request($email,$amnt);
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

    <title>StarCity Hub  - Referrals</title>

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
                        <h1 class="page-header">User Referrals</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <button type="button" class="btn btn-info" data-toggle="modal"  data-target="#myModal">
                        View Referral list
                    </button>

                    <!-- The Modal -->
                    <div class="modal" id="myModal">
                    <div class="modal-dialog">
                        <div class="modal-content">


                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Referral List</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>S.n.</th>
                                        <th>Referrals</th>
                                    </tr>
                                    <?php
                                        $i=1;
                                        $query = mysqli_query($con,"select * from users where under_userid='$email'order by id desc");
                                        if(mysqli_num_rows($query)>0){
                                            while($row=mysqli_fetch_array($query)){
                                                $downline = $row['email'];
                                                
                                            ?>
                                                <tr>
                                                    <td><?php echo $i ?></td>
                                                    <td><?php echo $downline ?></td>
                                                </tr>
                                            <?php
                                                $i++;
                                            }
                                        }
                                        else{
                                        ?>
                                            <tr>
                                                <td colspan="2" style = "text-align:center;">No Referrals Yet.</td>
                                            </tr>
                                        <?php
                                        }
                                    ?>
                                </table>
                            </div>
                            </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>

                        </div>
                    </div>
                    </div>
                	
                    <?php
                    $query =mysqli_query($con,"select * from users where email='$userid' ");
                    $result = mysqli_fetch_array($query);
                    $bon = $result['referral_bonus'];
                    if($bon>0){
                        ?>
                        <br/>
                        <br/>
                        <form method="get" class="form-inline">
                        <td><input type="text" name="amnt" placeholder="Enter amount here" class="form-control"></td>
                        <td><input type="submit" name="pay" value="Request Bonus Payment" class="btn btn-primary"></td>
                        </form>
                        <?php
                    } 
                    ?>
                    
                </div>
            </div>
            <!-- /.container-fluid -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Bonus Payment Request</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                	<div class="col-lg-12">
                    	<div class="table-responsive">
                        	<table class="table table-bordered table-striped">
                            	<tr>
                                	<th>S.n.</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Request Status</th>
                                </tr>
                                <?php
									$i=1;
									$query = mysqli_query($con,"select * from bonus_request where userid='$email'order by id desc");
									if(mysqli_num_rows($query)>0){
										while($row=mysqli_fetch_array($query)){
                                            $statz = $row['status'];
                                            $the_date = $row['date'];
                                            $amount = $row['amount']
										?>
                                        	<tr>
                                            	<td><?php echo $i ?></td>
                                                <td><?php echo $the_date ?></td>
                                                <td><?php echo $amount?></td>
                                                <td><?php echo $statz ?></td>
                                            </tr>
                                        <?php
											$i++;
										}
									}
									else{
									?>
                                    	<tr>
                                        	<td colspan="4" style = "text-align:center;">No Bonus Payment Request Yet.</td>
                                        </tr>
                                    <?php
									}
								?>
                            </table>
                        </div>
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
