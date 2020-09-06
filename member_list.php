<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "admin") {
  header("Location: index.php");
  exit();
}

if (isset($_POST["btnSignOut"])) {
  $_SESSION["username"] = "Guest";
  header("Location: index.php");
  exit();
}

require_once("connectconfig.php");

if (isset($_POST["normal"])) {
  $member_name = $_POST["member_name"];
  $sql_member_status = <<<multi
    UPDATE
      member
    SET
      `status` = '正常'
    WHERE
      `username` = '$member_name'
  multi;
  $link->query($sql_member_status);
}

if (isset($_POST["suspension"])) {
  $member_name = $_POST["member_name"];
  $sql_member_status = <<<multi
    UPDATE
      member
    SET
      `status` = '停權'
    WHERE
      `username` = '$member_name'
  multi;
  $link->query($sql_member_status);
}

if (isset($_POST["view_order_history"])) {
  $_SESSION["member_name"] = $_POST["member_name"];
  header("Location: manage_member_order.php");
  exit();
}

function query_members()
{
  require("connectconfig.php");
  $sql_member = <<<multi
    SELECT
      *
    FROM
      `member`
  multi;
  return $link->query($sql_member);
}
$query_members = query_members();

$query_members_data = $query_members->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>管理員 - 會員管理</title>
  <style>
    body {
      font-size: 20px;
      font-family: Microsoft JhengHei;
      padding-top: 62px;
    }

    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 100;
    }

    .nav_item_form {
      display: flex;
      align-items: center;
    }

    .title {
      margin: 0;
    }

    .table td {
      text-align: center;
      padding: 20px;
    }

    .product_images {
      width: 150px;
      height: 150px;
      object-fit: cover;
    }

    #checkout_btn {
      font-size: 30px;
      margin-top: 10px;
      margin-bottom: 20px;
    }

    #total_amount {
      margin: 10px 0;
    }

    #error_message {
      color: red;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">我是購物網站</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="#">你好，<?= $_SESSION["username"] ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="management_side.php">訂單管理</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">會員列表</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="commodity_management.php">商品管理</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="report.php">報表</a>
        </li>
        <li class="nav-item nav_item_form">
          <form action="" method="post">
            <input class="btn btn-outline-light" type="submit" name="btnSignOut" id="btnSignOut" value="登出" />
          </form>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <div class="row">
      <div class="col">
        <table class="table table-bordered">
          <thead>
            <tr class="bg-primary text-light">
              <td colspan="8">
                <p class="title">管理系統 － 會員列表</p>
              </td>
            </tr>
            <tr class="bg-success text-light">
              <td>
                <p class="title">會員名稱</p>
              </td>
              <td>
                <p class="title">電子信箱</p>
              </td>
              <td>
                <p class="title">手機號碼</p>
              </td>
              <td>
                <p class="title">密碼</p>
              </td>
              <td>
                <p class="title">狀態</p>
              </td>
              <td>
                <p class="title">操作</p>
              </td>
            </tr>
          </thead>
          <tbody>
            <?php while ($query_members_data = $query_members->fetch_assoc()) { ?>
              <tr class="text-center">
                <td class="align-middle"><?= $query_members_data['username'] ?></td>
                <td class="align-middle"><?= $query_members_data['email'] ?>
                </td>
                <td class="align-middle"><?= $query_members_data['cellphone'] ?></td>
                <td class="align-middle"><?= $query_members_data['password'] ?></td>
                <td class="align-middle"><?= $query_members_data['status'] ?></td>
                <td class="align-middle">
                  <form action="" method="post">
                    <input type="hidden" name="member_name" value="<?= $query_members_data['username'] ?>">
                    <input class="btn btn-outline-success" type="submit" name="normal" value="正常">
                    <input class="btn btn-outline-danger" type="submit" name="suspension" value="停權">
                    <input class="btn btn-outline-info" type="submit" name="view_order_history" value="查看歷史訂單">
                  </form>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>