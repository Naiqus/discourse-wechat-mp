<!DOCTYPE HTML>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <title>E1zone WX Manage</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
  </head>
  <body>
    <?php
      // define variables and set to empty values
      $username = $api = $userbindingkey = "";
      $usernameErr = $apiErr = $userbindingkeyErr = "";

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["username"])) {
        $usernameErr = "Username 是必填的";
        } else {
          $username = test_input($_POST["username"]);
        }
        
        if (empty($_POST["api"])) {
          $apiErr = "API 是必填的";
        } else {
          $api = test_input($_POST["api"]);
        }
          
        if (empty($_POST["userbindingkey"])) {
          $userbindingkeyErr = "用户绑定Key 是必填的";
        } else {
          $userbindingkey = test_input($_POST["userbindingkey"]);
        }
      }
    ?>
  <script type="text/javascript">
    function keyupFunction(){
    var api = document.getElementById("api").value;
    var apilast8 = api.slice(-8);  //last 8 letters
    document.getElementById("userbindingkey").value = apilast8;
    }
  </script>
  <div class="container">
    <div class="row">
      <div class="center-block col-md-5">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="form-horizontal">
        <fieldset>

        <!-- Form Name -->
        <legend>东一区微信绑定管理</legend>

        <!-- Text input-->
        <div class="control-group">
          <label class="control-label" for="username">用户名</label>
          <div class="controls">
            <input id="username" name="username" type="text" placeholder="输入用户名" class="input-xlarge">
            <span class="error">* <?php echo $usernameErr;?></span>
          </div>
        </div>

        <!-- Text input-->
        <div class="control-group">
          <label class="control-label" for="api">输入API_key</label>
          <div class="controls">
            <input id="api" name="api" type="text" placeholder="API_key" class="input-xlarge" onkeyup="keyupFunction()">
            <span class="error">* <?php echo $apiErr;?></span>
          </div>
        </div>

        <!-- Text input-->
        <div class="control-group">
          <label class="control-label" for="userbindingkey">用户绑定Key</label>
          <div class="controls">
            <input id="userbindingkey" name="userbindingkey" type="text" placeholder="八位绑定key" class="input-xlarge">
            <span class="error">* <?php echo $userbindingkeyErr;?></span>
            <p class="help-block">API_key的最后8位</p>
          </div>
        </div>

        <!-- Button (Double) -->
        <div class="control-group">
          <label class="control-label" for="confirm"></label>
          <div class="controls">
            <button id="confirm" name="confirm" class="btn btn-success" type="submit">确定</button>
            <button id="cancel" name="cancel" class="btn btn-danger">取消</button>
          </div>
        </div>

        </fieldset>
        </form>
      </div>
    </div>
  </div>
  

 <?php
      function test_input($data) {    //preprocess input
         $data = trim($data);
         $data = stripslashes($data);
         $data = htmlspecialchars($data);
         return $data;
      }

        $db_servername = "YourMySQLHost";
        $db_username = "YourUsername";
        $db_password = "YourPassword";

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(!empty($username)&&!empty($api)&&!empty($userbindingkey)){
          $connection = mysql_connect($db_servername, $db_username, $db_password) OR DIE ("Unable to connect to database! Please try again later.");
        if (!$connection){
          if (!$connection){
            echo "<div class=\"alert alert-danger\">服务器异常，请稍后重试。</div>";
          }else{
                mysql_select_db("discourseUsers");
                $sql = "INSERT INTO `account_binding` (`username`, `API_key`, `index`, `openID`) VALUES ('".$username."', '".$api."', '".$userbindingkey."', NULL);";            
                if (mysql_query($sql)) {
                    echo "<div class=\"alert alert-success\">用户:".$username."添加成功\n 请将用户绑定key：<b>".$userbindingkey."</b> 私信给用户</div>";
                } else {
                    echo "<div class=\"alert alert-danger\">用户添加失败，请稍后重试</div>";
                }
                mysql_close($connection);
          }
        }else{
          echo "<div class=\"alert alert-danger\">有未填项，请仔细检查下</div>";
        }
      }
  ?>

  </body>
</html>