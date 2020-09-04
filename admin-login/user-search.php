<?php
include('php-includes/connect.php');
include('php-includes/check-login.php');
$userid = $_SESSION['userid'];
?>
<?php 

$search = "";
if(isset($_GET['admin-search'])){
    $theuser = mysqli_real_escape_string($con,$_GET['theuser']);
    if (cross_check_mail($theuser)){
        $search = $theuser;
    }else{
        echo '<script>alert("Invalid User Email");window.location.assign("user-search.php");</script>';

    }
}

    $query = mysqli_query($con, "select * from users where email = '$search'");
    $result = mysqli_fetch_array($query);

    $the_user = $result ['email'];
    $firstname = $result['firstname'];
    $surname = $result ['surname'];
    $picture = $result['display_pic'];
    $mobile = $result ['mobile'];
    $address = $result ['address'];
    $account = $result['account'];
    $upline = $result['under_userid'];
    $referrals = $result['count'];
    $bonus = $result['referral_bonus'];
    $recycle_count = $result['recycle_count'];
    $user_level = $result['user_level'];
?>

<?php

function cross_check_mail($theuser){
    global $con;
    $query = mysqli_query($con,"select * from tree where userid = '$theuser'");
    $result = mysqli_fetch_array($query);
    if ($result['userid'] != ""){
        return true;
    }
    else {
        return false;
    }
}


?>

<!--
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
-->
<!------ Include the above in your HEAD tag ---------->
<!--
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
-->
<style>
    

.card {
    padding-top: 20px;
    margin: 10px 0 20px 0;
    background-color: rgba(214, 224, 226, 0.2);
    border-top-width: 0;
    border-bottom-width: 2px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.card .card-heading {
    padding: 0 20px;
    margin: 0;
}

.card .card-heading.simple {
    font-size: 20px;
    font-weight: 300;
    color: #777;
    border-bottom: 1px solid #e5e5e5;
}

.card .card-heading.image img {
    display: inline-block;
    width: 46px;
    height: 46px;
    margin-right: 15px;
    vertical-align: top;
    border: 0;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
}

.card .card-heading.image .card-heading-header {
    display: inline-block;
    vertical-align: top;
}

.card .card-heading.image .card-heading-header h3 {
    margin: 0;
    font-size: 14px;
    line-height: 16px;
    color: #262626;
}

.card .card-heading.image .card-heading-header span {
    font-size: 12px;
    color: #999999;
}

.card .card-body {
    padding: 0 20px;
    margin-top: 20px;
}

.card .card-media {
    padding: 0 20px;
    margin: 0 -14px;
}

.card .card-media img {
    max-width: 100%;
    max-height: 100%;
}

.card .card-actions {
    min-height: 30px;
    padding: 0 20px 20px 20px;
    margin: 20px 0 0 0;
}

.card .card-comments {
    padding: 20px;
    margin: 0;
    background-color: #f8f8f8;
}

.card .card-comments .comments-collapse-toggle {
    padding: 0;
    margin: 0 20px 12px 20px;
}

.card .card-comments .comments-collapse-toggle a,
.card .card-comments .comments-collapse-toggle span {
    padding-right: 5px;
    overflow: hidden;
    font-size: 12px;
    color: #999;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.card-comments .media-heading {
    font-size: 13px;
    font-weight: bold;
}

.card.people {
    position: relative;
    display: inline-block;
    width: 170px;
    height: 300px;
    padding-top: 0;
    margin-left: 20px;
    overflow: hidden;
    vertical-align: top;
}

.card.people:first-child {
    margin-left: 0;
}

.card.people .card-top {
    position: absolute;
    top: 0;
    left: 0;
    display: inline-block;
    width: 170px;
    height: 150px;
    background-color: #ffffff;
}

.card.people .card-top.green {
    background-color: #53a93f;
}

.card.people .card-top.blue {
    background-color: #427fed;
}

.card.people .card-info {
    position: absolute;
    top: 150px;
    display: inline-block;
    width: 100%;
    height: 101px;
    overflow: hidden;
    background: #ffffff;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.card.people .card-info .title {
    display: block;
    margin: 8px 14px 0 14px;
    overflow: hidden;
    font-size: 16px;
    font-weight: bold;
    line-height: 18px;
    color: #404040;
}

.card.people .card-info .desc {
    display: block;
    margin: 8px 14px 0 14px;
    overflow: hidden;
    font-size: 12px;
    line-height: 16px;
    color: #737373;
    text-overflow: ellipsis;
}

.card.people .card-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    display: inline-block;
    width: 100%;
    padding: 10px 20px;
    line-height: 29px;
    text-align: center;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.card.hovercard {
    position: relative;
    padding-top: 0;
    overflow: hidden;
    text-align: center;
    background-color: rgba(214, 224, 226, 0.2);
}

.card.hovercard .cardheader {
    background: url("../img/bg2.jpg");
    background-size: cover;
    height: 135px;
}

.card.hovercard .avatar {
    position: relative;
    top: -50px;
    margin-bottom: -50px;
}

.card.hovercard .avatar img {
    width: 100px;
    height: 100px;
    max-width: 100px;
    max-height: 100px;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.5);
}

.card.hovercard .info {
    padding: 4px 8px 10px;
}

.card.hovercard .info .title {
    margin-bottom: 4px;
    font-size: 24px;
    line-height: 1;
    color: #262626;
    vertical-align: middle;
}

.card.hovercard .info .desc {
    overflow: hidden;
    font-size: 12px;
    line-height: 20px;
    color: #737373;
    text-overflow: ellipsis;
}

.card.hovercard .bottom {
    padding: 0 20px;
    margin-bottom: 17px;
}

.btn{ border-radius: 50%;  line-height:18px;  }

</style>

<?php
//update dp request 
if(isset($_POST['submit'])){
    // File upload path
    $randnum = rand(100,999);
    $targetDir = "dp/";
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
                $query = mysqli_query($con,"update users set `display_pic` ='".$fileName."' where `email` = '$userid' limit 1");
                //$query = mysqli_query($con,"update pin_request set status='declined' where id='$id' limit 1");
                if($query){
                    echo '<script>alert("Upated Successfully");window.location.assign("user-profile.php");</script>';
                }
                else{
                    //echo mysqli_error($con);
                    echo '<script>alert("File upload failed, please try again.");window.location.assign("user-profile.php");</script>';
                }
            }
            else{
                echo '<script>alert("Sorry, there was an error uploading your file.");window.location.assign("user-profile.php");</script>';
            }
        }else{
            echo '<script>alert("Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.");window.location.assign("user-profile.php");</script>';

        }
    }else{
            echo '<script>alert("Please select a file to upload.");window.location.assign("user-profile.php");</script>';
    }
}


?>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="O.J Technologies LTD">

    <title>StarCity Hub  - User Profile</title>

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
        <div id="page-wrapper" > 
        <div class="container-fluid ">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">User Profile</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div class="row">
            <div class="col-lg-4">
                <form method="get">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="theuser" class="form-control" required>
                    </div>
                    <div class="form-group">
                    <input type="submit" name="admin-search" class="btn btn-primary" value="Search">
                </div>
                </form>
            </div>
        </div><!--/.row-->
        <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                </div>
                <div class="col-lg-6 col-sm-6">

                <div class="card hovercard">
                        <div class="cardheader">

                        </div>
                        <div class="avatar">
                            <?php 
                            if ($picture == "") {?>
                                <img alt="" src="../img/user_accept.png">
                            <?php
                            } else {?>
                                <img alt="" src=<?php echo '../dp/'.$picture ?>>
                            <?php
                            }
                            ?>

                        </div>
                        <div class="info">
                            <div class="title">
                                <a target="_blank" href="#"><?php echo $firstname.' '.$surname ?></a>
                            </div>
                            <div class="desc"><?php echo $the_user ?></div>
                            <div class="desc"><?php echo $mobile ?></div>
                            <div class="desc"><?php echo $address ?></div>
                            <div class="desc"><?php echo $account ?></div>

                        </div>
                        <div class="bottom">
                        <!--
                            <a class="btn btn-primary btn-twitter btn-sm" href="https://twitter.com/">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a class="btn btn-danger btn-sm" rel="publisher"
                            href="https://plus.google.com/">
                                <i class="fa fa-google-plus"></i>
                            </a>
                            <a class="btn btn-primary btn-sm" rel="publisher"
                            href="https://plus.google.com/">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a class="btn btn-warning btn-sm" rel="publisher" href="https://plus.google.com/">
                                <i class="fa fa-behance"></i>
                            </a>
                        -->

                        

                        <!-- The Modal -->
                        <div class="modal" id="myModal">
                        <div class="modal-dialog">
                            <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Update Display Picture</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                
                            <form  method="post" enctype="multipart/form-data">
                                <label>Select a picture from your device:</label>
                                <div class="custom-file">
                                <input type="file" name="file" id="fileToUpload " class="custom-file-upload">
                                </div>
                                <br>
                                <input type="submit" value="Upload Image" name="submit" class="btn btn-primary">
                            </form>

                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>

                            </div>
                        </div>
                        </div>

                        </div>
                        
                    </div>

                </div>

            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-users fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $referrals ?></div>
                                    <div>Total Referrals</div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-recycle fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $recycle_count ?></div>
                                    <div>Number of Recycle</div>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-star fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $user_level ?></div>
                                    <div>User Level</div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-money fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $bonus ?></div>
                                    <div>Refferal Bonus</div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!-- /.row -->
            
        </div>
        </div>
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
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    