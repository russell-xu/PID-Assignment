<?php
session_start();
require_once("../connectconfig.php");

if (!isset($_SESSION["username"]) || $_SESSION["username"] == "Guest") {
  header("Location: ../index.php");
  exit();
}

if (isset($_POST["btnSignOut"])) {
  $_SESSION["username"] = "Guest";
  header("Location: ../index.php");
  exit();
}


require("../connectconfig.php");
$username = $_SESSION["username"];
$sql_product_cart = <<<multi
    SELECT
      *
    FROM
      `orders`
    WHERE
      `username` = '$username'
    ORDER BY
      `orders_id`
    DESC
  multi;
$query_orders = $db->prepare($sql_product_cart);
$query_orders->execute();


function Update_purchase_quantity()
{
  require("../connectconfig.php");
  $username = $_SESSION["username"];
  $sql_quantity = <<<multi
    SELECT
        SUM(`quantity`) AS `quantity`
    FROM
        shopping_cart
    WHERE
        username = '$username'
    multi;
  $query_quantity = $db->prepare($sql_quantity);
  $query_quantity->execute();
  $quantity_row = $query_quantity->fetch(PDO::FETCH_ASSOC);
  return $quantity_row['quantity'];
}

if (isset($_POST["view_order_details"])) {
  $_SESSION["orders_id"] = $_POST["orders_id"];
  header("Location: client_view_order_detail.php");
  exit();
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>購物網站 - 查詢訂單</title>
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
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="member_side.php">我是購物網站</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="#">你好，<?= $_SESSION["username"] ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="member_side.php">商品列表</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="client_query_order.php">查詢訂單</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="shopping_cart.php">購物車
            <span class="badge badge-danger"><?= Update_purchase_quantity(); ?></span>
          </a>
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
              <td colspan="6">
                <p class="title">會員系統 － 查詢訂單</p>
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
                <p class="title">總金額</p>
              </td>
              <td>
                <p class="title">付款方式</p>
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
            <?php while ($query_orders_data = $query_orders->fetch(PDO::FETCH_ASSOC)) { ?>
              <tr class="text-center">
                <td class="align-middle"><?= $query_orders_data['orders_id'] ?></td>
                <td class="align-middle"><?= $query_orders_data['date'] ?>
                </td>
                <td class="align-middle">$<?= $query_orders_data['total_price'] ?></td>
                <td class="align-middle"><?= $query_orders_data['paytype'] ?></td>
                <td class="align-middle"><?= $query_orders_data['status'] ?></td>
                <td class="align-middle">
                  <form action="" method="post">
                    <input type="hidden" name="orders_id" value="<?= $query_orders_data['orders_id'] ?>">
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