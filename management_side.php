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

if (isset($_POST["complete_order"])) {
  $orders_id = $_POST["orders_id"];
  $sql_order_status = <<<multi
    UPDATE
      orders
    SET
      `status` = '已完成'
    WHERE
      `orders_id` = '$orders_id'
  multi;
  $link->query($sql_order_status);
}

if (isset($_POST["cancel_order"])) {
  $orders_id = $_POST["orders_id"];
  $sql_order_status = <<<multi
    UPDATE
      orders
    SET
      `status` = '已取消'
    WHERE
      `orders_id` = '$orders_id'
  multi;
  $link->query($sql_order_status);
}

if (isset($_POST["view_order_details"])) {
  $_SESSION["orders_id"] = $_POST["orders_id"];
  header("Location: manage_view_order_detail.php");
  exit();
}

function query_orders()
{
  require("connectconfig.php");
  $sql_product_cart = <<<multi
    SELECT
      *
    FROM
      `orders`
    ORDER BY
      `orders_id`
    DESC
  multi;
  return $link->query($sql_product_cart);
}
$query_orders = query_orders();
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>Lag - Member Page</title>
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
          <a class="nav-link" href="#">訂單管理</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="member_list.php">會員列表</a>
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
              <td colspan="7">
                <p class="title">管理系統 － 訂單管理</p>
              </td>
            </tr>
            <tr class="bg-success text-light">
              <td>
                <p class="title">訂單編號</p>
              </td>
              <td>
                <p class="title">訂單時間</p>
              </td>
              <td>
                <p class="title">訂單金額</p>
              </td>
              <td>
                <p class="title">會員名稱</p>
              </td>
              <td>
                <p class="title">訂單狀態</p>
              </td>
              <td>
                <p class="title">操作</p>
              </td>
            </tr>
          </thead>
          <tbody>
            <?php while ($query_orders_data = $query_orders->fetch_assoc()) { ?>
              <tr class="text-center">
                <td class="align-middle"><?= $query_orders_data['orders_id'] ?></td>
                <td class="align-middle"><?= $query_orders_data['date'] ?>
                </td>
                <td class="align-middle">$<?= $query_orders_data['total_price'] ?></td>
                <td class="align-middle"><?= $query_orders_data['username'] ?></td>
                <td class="align-middle"><?= $query_orders_data['status'] ?></td>
                <td class="align-middle">
                  <form action="" method="post">
                    <input type="hidden" name="orders_id" value="<?= $query_orders_data['orders_id'] ?>">
                    <input class="btn btn-outline-success" type="submit" name="complete_order" value="完成訂單">
                    <input class="btn btn-outline-danger" type="submit" name="cancel_order" value="取消訂單">
                    <input class="btn btn-outline-info" type="submit" name="view_order_details" value="查看訂單細節">
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