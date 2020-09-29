<?php
include('php-includes/check-login.php');
include('php-includes/connect.php');
require('PHPMailer/class.phpmailer.php'); // path to the PHPMailer class
require('PHPMailer/class.smtp.php');
$product_amount = 300;
?>
<?php
//Clicked on send buton
if(isset($_POST['send'])){
	$userid = mysqli_real_escape_string($con,$_POST['userid']);
	$amount = mysqli_real_escape_string($con,$_POST['amount']);
	$id = mysqli_real_escape_string($con,$_POST['id']);
	
    /*
    $no_of_pin = $amount/$product_amount;
	//Insert pin
	$i=1;
	while($i<=$no_of_pin){
		$new_pin = pin_generate();
		mysqli_query($con,"insert into pin_list (`userid`,`pin`) values('$userid','$new_pin')");
		$i++;	
    }
    */

    $no_of_pin = $amount;
	//Insert pin
	
    $new_pin = pin_generate();
    mysqli_query($con,"insert into pin_list (`userid`,`pin`) values('$userid','$new_pin')");
    
    //send pin to user email
    
    $subject = "STARCITY HUB";
    $body ='<p>Congratulations!</p>';
    $body .='<p>Your request for a pin have been reviewed and been sucessfully processed. Your pin is '.$new_pin.'
    <a href="https://www.starcityhub.com/">starcityhub.com</a>.</p>';
    // Enter Your Email Address Here To Receive Email
    $email_to = "fabulousjoeboy@gmail.com";
     
    $email_from = "support@starcityhub.com"; // Enter Sender Email
    $sender_name = "StarcityHub"; // Enter Sender Name
    require("PHPMailer/PHPMailerAutoload.php");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "mail.starcityhub.com "; // Enter Your Host/Mail Server
    $mail->SMTPAuth = true;
    $mail->Username = "support@starcityhub.com"; // Enter Sender Email
    $mail->Password = "k+eAyWb$v!ZG";
    //If SMTP requires TLS encryption then remove comment from below
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->From = $email_from;
    $mail->FromName = $sender_name;
    $mail->Sender = $email_from; // indicates ReturnPath header
    $mail->AddReplyTo($email_from, "StarCity Hub"); // indicates ReplyTo headers
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($email_to);
    // If you know receiver name use following
    $mail->AddAddress($email_to, "Joseph");
    // To send CC remove comment from below
    //$mail->AddCC('username@email.com', "Recepient Name");
    // To send attachment remove comment from below
    //$mail->AddAttachment('files/readme.txt');
    /*
    Please note file must be available on your
    host to be attached with this email.
    */
     
    if (!$mail->Send()){
        echo "Mailer Error: " . $mail->ErrorInfo;
        }else{
        echo "<div style='color:#FF0000; font-size:20px; font-weight:bold;'>
        An email has been sent to your email address.</div>";
    }
		
	//updae pin request status
	mysqli_query($con,"update pin_request set status='approved' where id='$id' limit 1");
	
}

if(isset($_POST['Cancel'])){
	$userid = mysqli_real_escape_string($con,$_POST['userid']);
	$amount = mysqli_real_escape_string($con,$_POST['amount']);
	$id = mysqli_real_escape_string($con,$_POST['id']);
    
 	//updae pin request status
     mysqli_query($con,"update pin_request set status='declined' where id='$id' limit 1");
	
     echo '<script>alert("Pin Request Declined");window.location.assign("view-pin-request.php");</script>';	
}   


//Pin generate
function pin_generate(){
	global $con;
	$generated_pin = rand(100000,999999);
	
	$query = mysqli_query($con,"select * from pin_list where pin = '$generated_pin'");
	if(mysqli_num_rows($query)>0){
		pin_generate();
	}
	else{
		return $generated_pin;
	}
	
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

    <title>StarCity Hub  - View Pin Request</title>

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
                        <h1 class="page-header">Admin - View pin request</h1>
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
                                    <th>Evidence of Payment</th>
                                    <th>Date</th>
                                    <th>Send</th>
                                    <th>Cancel</th>
                                </tr>
                                <?php
									$query = mysqli_query($con,"select * from pin_request where status='open'");
									if(mysqli_num_rows($query)>0){
										$i=1;
										while($row=mysqli_fetch_array($query)){
											$id = $row['id'];
											$email = $row['email'];
											$amount = '../uploads/'.$row['file_name'];
											$date = $row['date'];
										?>
                                        	<tr>
                                            	<td><?php echo $i; ?></td>
                                                <td><?php echo $email; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-info" data-toggle="modal"  data-target="#myModal-<?php echo $i;?>">
                                                        View Evidence of Payment
                                                    </button>

                                                    <!-- The Modal -->
                                                    <div class="modal" id="myModal-<?php echo $i;?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">

                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Evidence of Payment</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <!-- Modal body -->
                                                        <div class="modal-body">
                                                            <img src="<?php echo $amount;?>" class="img-thumbnail">
                                                        </div>

                                                        <!-- Modal footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>

                                                        </div>
                                                    </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $date; ?></td>
                                                <form method="post">
                                                	<input type="hidden" name="userid" value="<?php echo $email ?>">
                                                    <input type="hidden" name="amount" value="<?php echo $amount ?>">
                                                    <input type="hidden" name="id" value="<?php echo $id ?>">
                                                	<td><input type="submit" name="send" value="Send" class="btn btn-primary"></td>
                                                </form>

                                                <form method="post">
                                                	<input type="hidden" name="userid" value="<?php echo $email ?>">
                                                    <input type="hidden" name="amount" value="<?php echo $amount ?>">
                                                    <input type="hidden" name="id" value="<?php echo $id ?>">
                                                    <td><input type="submit" name = "Cancel" value = "Cancel" class = "btn btn-danger"></td>
                                                </form>
                                            </tr>
                                        <?php
											$i++;
										}
									}
									else{
									?>
                                    	<tr>
                                        	<td colspan="6" align="center">You have no pin request.</td>
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
