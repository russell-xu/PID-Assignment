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

$member_name = $_SESSION["member_name"];
$orders_id = $_SESSION["orders_id"];

$sql_sum_price = <<<multi
  SELECT
      `total_price`
  FROM
      `orders`
  WHERE
      `orders_id` = '$orders_id'
multi;
$sum_price = $link->query($sql_sum_price)->fetch_row();

$shipping = $sum_price[0] != null ? 60 : 0;

function query_order_detail()
{
  require("connectconfig.php");
  $orders_id = $_SESSION["orders_id"];
  $sql_product_cart = <<<multi
    SELECT
      *
    FROM
      `order_detail`
    WHERE
      `orders_id` = $orders_id
  multi;
  return $link->query($sql_product_cart);
}
$order_detail = query_order_detail();
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>管理員 - 查看訂單細節</title>
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
              <td colspan="5">
                <p class="title">管理系統 － 查詢訂單細節 - 會員：<?= $member_name ?>&nbsp 訂單編號：<?= $orders_id ?></p>
              </td>
            </tr>
            <tr class="bg-success text-light">
              <td>
                <p class="title">商品名字</p>
              </td>
              <td>
                <p class="title">商品圖片</p>
              </td>
              <td>
                <p class="title">單價</p>
              </td>
              <td>
                <p class="title">購買數量</p>
              </td>
              <td>
                <p class="title">總計</p>
              </td>
            </tr>
          </thead>
          <tbody>
            <?php while ($order_detail_data = $order_detail->fetch_assoc()) { ?>
              <tr class="text-center">
                <td class="align-middle"><?= $order_detail_data['product_name'] ?></td>
                <td class="align-middle">
                  <img class="product_images" src="./img/<?= $order_detail_data['product_images'] ?>" alt="" srcset="">
                </td>
                <td class="align-middle">$<?= $order_detail_data['product_price'] ?></td>
                <td class="align-middle"><?= $order_detail_data['quantity'] ?></td>
                <td class="align-middle">$<?= $order_detail_data['quantity'] * $order_detail_data['product_price'] ?></td>
              </tr>
            <?php } ?>
            <tr class="table-info">
              <td colspan="5">
                <span>運費：$<?= $shipping ?></span>
                <h3 id="total_amount">總金額：$<?= $sum_price[0] ?></h3>
              </td>
            </tr>
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