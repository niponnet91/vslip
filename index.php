<?php
session_start();
error_reporting(0);
include("config.php");
include("function.php");
$connect_db=array(
'1'=>'$conn=mysql_connect($database_host,$database_user,$database_password) or die("connect Mysql database error!");
	mysql_select_db($database_db_name) or die("Select database error!");',
	
'2'=>'$conn=mysqli_connect($database_host,$database_user,$database_password,$database_db_name) or die("Error Mysqli Database is not connect!");',

'3'=>'mssql_connect($database_host,$database_user,$database_password) or die("Mssql Database not Connect.. Please Check config");
	mssql_select_db ($database_db_name) or die("Mssql Select database error!");',

'4'=>'$conn=odbc_connect(\'Driver={SQL Server};Server=\' .$database_host. \';Database=\' . $database_db_name. \';\' ,$database_user, $database_password) or die(\'Error Odbc Mssql Database is not connect!\');',
);


eval($connect_db[$database_type]);

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if(isset($_POST[ref1])){
	if($_POST[ref1]==""){
		$_SESSION[alert_content]="กรุณาระบุ Username";
		$_SESSION[alert_type]="alert-danger";
		header( "location:index.php" );
		die();
	}else{
		$_SESSION[ref1]=$_POST[ref1];
		if($_POST[qrcode]){
			header( "location:?qrcode=".$_POST[qrcode]);
			
		}else{
			
			header( "location:https://iotsoft.online/vslip/scan.php?return=".urlencode($actual_link) );
		}
	}
}

if($_GET[qrcode]&&$_SESSION[ref1]){
	$connect_api=connect_api($api_url."?username=$tmweasy_user&password=$tmweasy_password&device_id=$device_id&qrcode=$_GET[qrcode]&focus_no=$account_no&focus_bankcode=$bank_code_set&ref1=$_SESSION[ref1]&ip=".my_ip());
	$connect_api=json_decode($connect_api,true);
	if($connect_api[status]!="1"){
		
		$_SESSION[alert_content]="Error : ".$connect_api[msg];
		$_SESSION[alert_type]="alert-danger";
		header( "location:index.php" );
		die();
	}else{//เมื่อโอนสำเร็จ----------------------------------
	//-----------------------------------------------------------------------------------------------------------------
		
		$point=$connect_api[amount]*$mul_credit;
		$ref1=$_SESSION[ref1];
		$database_update=array(
			'1'=>'mysql_query("update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
			'2'=>'mysqli_query($conn,"update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
			'3'=>'mssql_query("update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
			'4'=>'odbc_exec($conn,"update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
		);
		if($connect_api[request_one]==1){
			eval($database_update[$database_type]);
			
		}
		
		
		$_SESSION[alert_content]="การโอนเงิน สำเร็จแล้ว   คุณได้รับ ".$point." เครดิตร ขอบคุณครับ";
		$_SESSION[alert_type]="alert-success";
		header( "location:index.php?action=success" );
		die();
	//-----------------------------------------------------------------------------------------------------------------
	}
	
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>Verify Slip</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/main1.css" rel="stylesheet" media="all">
	<script src="alert_box/sweetalert.min.js"></script>
	<link rel="stylesheet" href="alert_box/sweetalert.css">
	<script>
		
		function time_display(id_tag,time_s){
			min=pad(Math.floor(time_s/60),2,0);
			sec=pad(Math.abs((min*60) - time_s),2,0);
			if(time_s<=0){
				document.getElementById(id_tag).innerHTML="หมดเวลาโอนเงิน <br> <button onclick=\"window.location.href='?action=cancel'\" class='btn btn-default has-spinner btn--radius-2 btn--red' type='submit'>ยกเลิก - เริ่มโอนใหม่</button>";
				document.getElementById("pay1").innerHTML="";
				document.getElementById("pay2").innerHTML="";
				document.getElementById("pay3").innerHTML="";
				document.getElementById("pay4").innerHTML="";
				document.getElementById("pay5").innerHTML="";
				document.getElementById("pay6").innerHTML="";
			}else{
				document.getElementById(id_tag).innerHTML=min+" : "+sec;
			}
			
		}
		
		function time_down(){
			sec_start=sec_start-1;
			time_display("time_count_down",sec_start);
			if(sec_start>0){
				setTimeout(time_down, 1000);
			}
			
			
		}
		function pad(n, width , fill) {
			n = n + '';
			return n.length >= width ? n : new Array(width - n.length + 1).join(fill) + n;
		}
		
	</script>
</head>

<body>
    <div class="page-wrapper bg-gra-02 p-t-30 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body"><img src="https://iotsoft.online/vslip/b1.jpg" width="20%"><img src="scan.png" width="20%">  
					
                    <h2 class="title">ชำระด้วย โอนเงินธนาคาร สแกนสลิป อัตโนมัติ</h2>
						<?php
					if($_GET[action]=="success"){
						?>
						<div align="center"><img src="check_green.png" width="30%"></div>
						<h2 class="title" align="center">ทำรายการสำเร็จแล้ว</h2>
						<p class="label" align="center">ตรวจสอบเครดิตรของคุณ หากพบปัญหากรุณาติดต่อ Admin ขอบคุณครับ</p>
						<p class="label" align="center">[ ! ปิดหน้านี้ได้เลยครับ ]</p>
					 <?php
					}else{
						$api_url."?username=$tmweasy_user&password=$tmweasy_password";
						$connect_api=connect_api($api_url."?username=$tmweasy_user&password=$tmweasy_password");
						$connect_api=json_decode($connect_api,true);
						if($connect_api[status]!="1"){
							
							$_SESSION[alert_content]="Error : ".$connect_api[msg];
							$_SESSION[alert_type]="alert-danger";
						}
			
					?>
					<script>
					function input_qrcode(){
						let person = prompt("Please enter Qrcode");

						if (person != null&&person !="") {
						
						  document.getElementById("qrcode").value=person;
						   var btn = $( document.getElementById("scan_bin"));
							$(btn).buttonLoader('start');
						  document.getElementById("userform").submit(); 
						}
						
					}
					</script>
					<div class="label" style="color:red">**โปรดอ่าน ทำการโอนเงินผ่านแอปธนาคาร ที่มี Qr code บนสลิป เท่านั้น โอนแล้วให้เก็บสลิปไว้เพื่อใช้ยืนยันกับระบบ หากโอนผ่านช่องทางอื่นที่ไม่มี Qr code หรือไม่ได้เก็บสลิป ระบบจะตรวจสอบไม่ได้ให้ติดต่อแอดมินเท่านั้น</div>
				
                    <form method="POST" id="userform" >

                       
						<div class="input-group">
						
                            <div class="rs-select2 js-select-simple select--no-search">
								<label class="label"><b>โอนเงินมายังบัญชี</b></label>
								<div class="card card-4">
									<table width="95%" >
									<tr >
										<td width="20%"><br><img src="bank/<?=$bank_code_set?>.png" width="100%"><br></td>
										<td width="15"></td>
										<td > 
											
											<div class="label"><b>ชื่อบัญชี : </b><?=$account_name?></div>
											<div class="label"><b>เลขบัญชี : </b><?=$account_no?></div>
											<div class="label"><b>ธนาคาร : </b><?=$bank_code[$bank_code_set]?></div>
											<input type="hidden" name="qrcode" id="qrcode">
										</td>
									</tr>
									</table>
									</div>
									<br>
                                 <p class="label">* Ref1 ID / Username</p>
								<input class="input--style-4" value="<?=$_GET[ref1]?>" type="text" name="ref1">
							
								
						   </div>
                       
                       </div>
                        <div class="p-t-15">
							
                            <p><button id="scan_bin" class="btn btn--radius-2 btn--green btn-default has-spinner" onclick="this.form.submit();" >ชำระแล้ว กดเพื่อ Scan สลิป</button></p>
							<br>
							
						</div>
                    </form><p><button class="btn btn--radius-2 btn--green btn-default" onclick="input_qrcode()"  >กรอก QR Code เอง</button></p>
					<?php
					
					}
					?>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="js/global.js"></script>
	
	<?php
	if($_SESSION[alert_content]){
		alert_content($_SESSION[alert_content],$_SESSION[alert_type]);
	}
	?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://www.jqueryscript.net/demo/jQuery-Plugin-For-Built-In-Loading-Indicator-In-Buttons-Button-Loader/jquery.buttonLoader.js"></script>
<script>
$(document).ready(function () {
    
    $('.has-spinner').click(function () {
        var btn = $(this);
        $(btn).buttonLoader('start');
        
    });
});
</script>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>