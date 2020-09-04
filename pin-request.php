<?php
require('php-includes/connect.php');
include('php-includes/check-login.php');
$email = $_SESSION['userid'];
$statusMsg = '';
$randnum = rand(100,999);
$date = date("y-m-d");


?>
<?php
//pin request 
if(isset($_POST['submit'])){
    // File upload path
    $targetDir = "uploads/";
    $temp_fileName = basename($_FILES["file"]["name"]);
    $fileName = $randnum . $temp_fileName;
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
    //Not Empty
    if (!empty ($_FILES["file"]["name"])){
        // Allow certain file formats
        $allowTypes = array('jpg','png','jpeg','gif','pdf');
        if(in_array($fileType, $allowTypes)){
            // Upload file to server
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){
                // Insert image file name into database
                $query = mysqli_query($con,"insert into pin_request(`email`,`file_name`,`date`) values('$email','".$fileName."','$date')");
                
                // $insert = query($con, "INSERT into images (file_name, uploaded_on) VALUES ('".$fileName."', NOW())");
                if($query){
                    echo '<script>alert("Pin request sent successfully");window.location.assign("pin-request.php");</script>';
                }
                else{
                    //echo mysqli_error($con);
                    echo '<script>alert("File upload failed, please try again.");window.location.assign("pin-request.php");</script>';
                }
            }
            else{
                echo '<script>alert("Sorry, there was an error uploading your file.");window.location.assign("pin-request.php");</script>';
            }
        }else{
            echo '<script>alert("Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.");window.location.assign("pin-request.php");</script>';

        }
    }else{
            echo '<script>alert("Please select a file to upload.");window.location.assign("pin-request.php");</script>';
    }



	//$amount = mysqli_real_escape_string($con,$_GET['amount']);
	
	
	/*
	if($amount!=''){
		//Inset the value to pin request
		$query = mysqli_query($con,"insert into pin_request(`email`,`amount`,`date`) values('$email','$amount','$date')");
		if($query){
			echo '<script>alert("Pin request sent successfully");window.location.assign("pin-request.php");</script>';
		}
		else{
			//echo mysqli_error($con);
			echo '<script>alert("Unknown error occure.");window.location.assign("pin-request.php");</script>';
		}
	}
	else{
		echo '<script>alert("Please fill all the fields");</script>';
	}
	*/
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

    <title>StarCity Hub  - Pin Request</title>

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
                        <h1 class="page-header">Pin Request</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                	<div class="col-lg-4">
                        <!--
                    	<form method="get">	
                        	<div class="form-group">
                            	<label>Amount</label>
                                <input type="text" name="amount" class="form-control" required>
                            </div>
                            <div class="form-group">
                            	<input type="submit" name="pin_request" class="btn btn-primary" value="Pin Request">
                            </div>
                        </form>
                        -->

                        <form  method="post" enctype="multipart/form-data">
                            <label>Upload Evidence of Payment:</label>
                            <div class="custom-file">
                            <input type="file" name="file" id="fileToUpload " class="custom-file-upload">
                            </div>
                            <br>
                            <input type="submit" value="Upload Image" name="submit" class="btn btn-primary">
                        </form>
                    </div>
                </div>
                <div class="row">
                	<div class="col-lg-6">
                    	<br><br>
                    	<table class="table table-bordered table-striped">
                        	<tr>
                            	<th>S.n.</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                            <?php 
							$i=1;
							$query = mysqli_query($con,"select * from pin_request where email='$email' order by id desc");
							if(mysqli_num_rows($query)>0){
								while($row=mysqli_fetch_array($query)){
									$date = $row['date'];
									$status = $row['status'];
								?>
                                	<tr>
                                    	<td><?php echo $i; ?></td>
                                        <td><?php echo $date; ?></td>
                                        <td><?php echo $status; ?></td>
                                    </tr>
                                <?php
									$i++;
								}
							}
							else{
							?>
                            	<tr>
                                	<td colspan="3">You have no pin request yet.</td>
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
