<?php
include('php-includes/connect.php');
include('php-includes/check-login.php');
$userid = $_SESSION['userid'];
$capping = 500;
?>

<?php
$email=$userid;



if (isset($_POST['paid'])){
	//mysqli_query($con,"delete from cashout_eligibility_list where email = '$userid'" );
	//$recv= eligibility_check('user1@gmail.com');
	
		//echo $recv ;
	
	//update Income Table

    $uppp_under_userid = "support@starcityhub.com";

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

}	

?>

<form method="post">
	<td><input type="submit" name="paid" value="Paid" class="btn btn-primary"></td>
</form>



<td><a href="<?php echo $amount;?>"><?php echo $amount;?></a></td>

<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                                        View Evidence of Payment
                                                    </button>

                                                    <!-- The Modal -->
                                                    <div class="modal" id="myModal">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">

                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Modal Heading</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <!-- Modal body -->
                                                        <div class="modal-body">
                                                            <img src="<?php echo $amount;?>">
                                                        </div>

                                                        <!-- Modal footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>

                                                        </div>
                                                    </div>
                                                    </div>

