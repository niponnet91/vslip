<?php
function connect_api($url){
	
	return file_get_contents($url);
}

function alert_content($content,$type){
	switch($type){
		case "alert-danger":
			$type_alert="error";
		 break;
		case "alert-warning":
			$type_alert="warning";
		 break;
		case "alert-success":
			$type_alert="success";
			
		 break;
	}
	$content_box=preg_replace('/"/','\"',$content);
	$script_alert='<script type="text/javascript">
	function JSalert(){
	swal({   title: "System Message",   
    text: "'.$content_box.'",   
    type: "'.$type_alert.'",//error success warning   
    showCancelButton: 0,   
    confirmButtonText: "",   
    cancelButtonText: "ปิด",   
    closeOnConfirm: true,   
    closeOnCancel: true }, 
    function(isConfirm){   
        if (isConfirm) 
		{   
			false  
        } 
         });
}
JSalert();
</script>';
	echo $script_alert;
	$_SESSION[alert_content]="";
}
function my_ip(){
 if ($_SERVER['HTTP_CLIENT_IP']) { 
$IP = $_SERVER['HTTP_CLIENT_IP'];
} elseif (preg_match("[0-9]",$_SERVER["HTTP_X_FORWARDED_FOR"] )) { 
$IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else { 
$IP = $_SERVER["REMOTE_ADDR"];
}
return $IP;
}
?>