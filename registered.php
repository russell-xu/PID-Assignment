<?php
session_start();

if (isset($_POST["btnHome"])) {
  header("Location: index.php");
  exit();
}


$ErrorMessage = "";

if (isset($_POST["btnOK"])) {
  $sIdNumber = $_POST["txtIdNumber"];
  $sUserName = $_POST["txtUserName"];
  $sPassword = $_POST["txtPassword"];

  require_once("connectconfig.php");
  $sql = "select * from userlist where Idnumber='$sIdNumber'";
  $result = $link->query($sql);
  $num = $result->num_rows;

  if (preg_match("/^[A-Z]{1}[12ABCD]{1}[0-9]{8}$/", $sIdNumber) && trim($sUserName) != "" && preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8}/', $sPassword) && $num == 0) {
    $_SESSION["userName"] = $sUserName;
    $sql = <<<multi
  INSERT INTO userlist(IdNumber, UserName, Password) VALUES('$sIdNumber', '$sUserName', '$sPassword');
  multi;
    $link->query($sql);
    header("Location: registration_success.php");
    exit();
  } else {
    $ErrorMessage = "身分證字號、使用者代號或網銀密碼有誤";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>Lab - Registered</title>
  <style>
    body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-family: Microsoft JhengHei;
    }

    .title {
      margin: 0;
    }

    .table td {
      text-align: center;
      padding: 20px;
    }

    .register {
      font-size: 20px;
      padding: 6px 60px;
    }
  </style>
</head>

<body>
  <form id="form2" name="form2" method="post" action="registered.php">
    <table class="table table-bordered">
      <thead>
        <tr class="bg-primary text-light">
          <td colspan="2">
            <p class="title">會員系統 - 註冊</p>
          </td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="align-middle">身分證字號</td>
          <td>
            <input type="text" name="txtIdNumber" id="txtIdNumber" />
          </td>
        </tr>
        <tr>
          <td class="align-middle">使用者代號</td>
          <td>
            <input type="text" name="txtUserName" id="txtUserName" />
          </td>
        </tr>
        <tr>
          <td class="align-middle">網銀密碼</td>
          <td>
            <input type="password" name="txtPassword" id="txtPassword" />
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input class="btn btn-success register" type="submit" name="btnOK" id="btnOK" value="註冊" />
        </tr>
        <tr class="bg-primary text-light">
          <td colspan="2">
            <input class="btn btn-warning" type="reset" name="btnReset" id="btnReset" value="重設" />
            <input class="btn btn-warning" type="submit" name="btnHome" id="btnHome" value="回登入頁面" />
          </td>
        </tr>
      </tbody>
    </table>
    <p class="text-danger"><?= $ErrorMessage ?></p>
  </form>


  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>