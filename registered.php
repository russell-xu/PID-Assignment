<?php
session_start();
require_once("connectconfig.php");

if (isset($_POST["btnHome"])) {
  header("Location: index.php");
  exit();
}


$ErrorMessage = "";

if (isset($_POST["btnOK"])) {
  $username = $_POST["registered_username"];
  $email = $_POST["registered_email"];
  $cellphone = $_POST["registered_cellphone"];
  $address = $_POST["registered_address"];
  $password = $_POST["registered_password"];

  $sql_user_data = <<<multi
  SELECT
    *
  FROM
    member
  WHERE
    username = '$username'
  multi;
  $user_data = $db->prepare($sql_user_data);
  $user_data->execute();
  $user_data_row_count = $user_data->rowCount();

  $sql_email_data = <<<multi
  SELECT
    *
  FROM
    member
  WHERE
    email = '$email'
  multi;
  $email_data = $db->prepare($sql_email_data);
  $email_data->execute();
  $email_data_row_count = $email_data->rowCount();

  $sql_cellphone_data = <<<multi
  SELECT
    *
  FROM
    member
  WHERE
    cellphone = '$cellphone'
  multi;
  $cellphone_data = $db->prepare($sql_cellphone_data);
  $cellphone_data->execute();
  $cellphone_data_row_count = $cellphone_data->rowCount();

  $username_verification = trim($username) != "";
  $email_verification = filter_var($email, FILTER_VALIDATE_EMAIL);
  $cellphone_verification = preg_match('/^[0][0-9]{9}$/', $cellphone);
  $address_verification = trim($address) != "";
  $password_verification = preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8}/', $password);

  if ($username_verification && $email_verification &&  $cellphone_verification && $address_verification && $password_verification) {
    if ($user_data_row_count == 0 && $email_data_row_count == 0 && $cellphone_data_row_count == 0) {
      $_SESSION["userName"] = $username;
      $sql_registered = <<<multi
        INSERT INTO member(
          username,
          email,
          cellphone,
          address,
          PASSWORD
        )
        VALUES(
            '$username',
            '$email',
            '$cellphone',
            '$address',
            '$password'
        );
      multi;
      $registered = $db->prepare($sql_registered);
      $registered->execute();
      header("Location: registration_success.php");
      exit();
    } else {
      $ErrorMessage = "使用者代號、電子信箱或手機號碼已被使用！";
    }
  } else {
    $ErrorMessage = "使用者代號、電子信箱、手機號碼、住址或密碼有誤！
    ";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>購物網站 - 註冊</title>
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
  <form method="post" action="registered.php">
    <table class="table table-bordered">
      <thead>
        <tr class="bg-dark text-light">
          <td colspan="2">
            <p class="title">會員系統 - 註冊</p>
          </td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="align-middle">使用者代號</td>
          <td>
            <input type="text" name="registered_username" id="registered_username" />
          </td>
        </tr>
        <tr>
          <td class="align-middle">電子信箱</td>
          <td>
            <input type="email" name="registered_email" id="registered_email" />
          </td>
        </tr>
        <tr>
          <td class="align-middle">手機號碼</td>
          <td>
            <input type="tel" name="registered_cellphone" id="registered_cellphone" />
          </td>
        </tr>
        <tr>
          <td class="align-middle">住址</td>
          <td>
            <input type="text" name="registered_address" id="registered_address" />
          </td>
        </tr>
        <tr>
          <td class="align-middle">密碼</td>
          <td>
            <input type="password" name="registered_password" id="registered_password" />
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input class="btn btn-success register" type="submit" name="btnOK" id="btnOK" value="註冊" />
        </tr>
        <tr class="bg-dark text-light">
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