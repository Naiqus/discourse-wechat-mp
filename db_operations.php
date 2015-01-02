<?php
// usage: db_operation(flag,params,...)
// db_operation("getNotification",$fromUsername)
// db_operation("bind",$userkey,$fromUsername);
// db_operation("unbind",$fromUsername);

function db_operation(){
	
	$db_servername = "YourMySQLHost";
	$db_username = "YourUsername";
	$db_password = "YourPassword";


	// Create connection
	$connection = mysql_connect($db_servername, $db_username, $db_password) OR DIE ("Unable to connect to database! Please try again later.");
	if (!$connection){
		$output = "服务器异常，请稍后重试。";
	}else{
		mysql_select_db("discourseUsers");
		// select operations

			//--------------------------binding account
			if (func_get_arg(0) == "bind") {
				$apikey = func_get_arg(1);
				$fromUsername = func_get_arg(2);

				//check if this user has already binded to an account
				$sql = "SELECT `openID`,`username` FROM `account_binding` WHERE `openID` = '".$fromUsername."';";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0) {  
					$record = mysql_fetch_array($result);
					$output = "对不起，您已经与东一区帐号”".$record["username"]."“绑定。如果您要与其他用户绑定，请先”解除绑定“";
					mysql_close($connection);
					return $output;
				}
				//find the users using apikey
				$sql = "SELECT `openID`,`username` FROM `account_binding` WHERE `index` = '".$apikey."';";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0) {  //the user found! waiting for openID
					$record = mysql_fetch_array($result);
				    // output data of each row
				    if ($record["openID"] != NULL) { // used
				    	$output = "对不起，该东一区用户已经被微信绑定。请与管理员联系。";
				    }else{		//writing to the DB
				    	$sql = "UPDATE `account_binding` SET `openID` = '".$fromUsername."' WHERE `index` = '".$apikey."';";
						if (mysql_query($sql)) {
				    		$output = "您与您的东一区帐号“".$record["username"]."” 绑定成功！";
						} else {
				    		$output = "由于技术原因，本次绑定失败，请与管理员联系";
						}
				    }
				   
				} else {
				    $output = "微信key不存在，请向东一区管理员申请微信key。";
				    // $conn->close();
				}
			//------------------------------ unbind user
			}elseif (func_get_arg(0) == "unbind") {
				$fromUsername = func_get_arg(1);
				$sql = "SELECT `openID`,`username` FROM `account_binding` WHERE `openID` = '".$fromUsername."';";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0) {
					$record = mysql_fetch_array($result);
					$sql = "UPDATE `account_binding` SET `openID` = NULL WHERE `openID` = '".$fromUsername."';";
						if (mysql_query($sql) == TRUE) {
				    		$output = "您与您的东一区帐号“".$record["username"]."” 解除绑定！\n 如需重新绑定，请回复“绑定”";
						} else {
				    		$output = "由于技术原因，本次解除绑定失败，请与管理员联系";
						}
				}else{
					$output = "miss";
				}
			//------------------------------- checking notification
			}elseif (func_get_arg(0) == "getNotification") {
				$fromUsername = func_get_arg(1);
				$sql = "SELECT `API_key`,`username` FROM `account_binding` WHERE `openID` = '".$fromUsername."';";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0) {
					$record = mysql_fetch_array($result);
				    $output = "api_key="
				    		  .$record["API_key"]."&api_username="
				    		  .$record["username"];
				}else{
					$output = "miss";
				}
			}

			return $output;
			mysql_close($connection);

	}

}

?>