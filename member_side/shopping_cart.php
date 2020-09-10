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


$username = $_SESSION["username"];
$sql_product_cart = <<<multi
  SELECT
    a.quantity,
    a.product_id,
    b.product_name,
    b.product_price,
    b.product_stocks,
    b.product_images
  FROM
    shopping_cart AS a
  INNER JOIN product_list AS b
  ON
    a.product_id = b.product_id AND a.username = '$username'
multi;
$shopping_cart_data = $db->prepare($sql_product_cart);
$shopping_cart_data->execute();

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

if (isset($_POST["delete_cart_product"])) {
  global $shopping_cart_data;

  $username = $_SESSION["username"];
  $product_id = $_POST["product_id"];

  $sql_delete_cart_product = <<<multi
  DELETE
  FROM
      shopping_cart
  WHERE
      username = '$username' AND product_id = '$product_id'
  multi;
  $db->prepare($sql_delete_cart_product)->execute();

  $sql_product_cart = <<<multi
    SELECT
      a.quantity,
      a.product_id,
      b.product_name,
      b.product_price,
      b.product_stocks,
      b.product_images
    FROM
      shopping_cart AS a
    INNER JOIN product_list AS b
    ON
      a.product_id = b.product_id AND a.username = '$username'
  multi;
  $shopping_cart_data = $db->prepare($sql_product_cart);
  $shopping_cart_data->execute();
}

$username = $_SESSION["username"];

$sql_sum_price = <<<multi
  SELECT
      SUM(a.quantity * b.product_price) AS `sum_price`
  FROM
      shopping_cart AS a
  INNER JOIN product_list AS b
  ON
      a.product_id = b.product_id AND a.username = '$username'
multi;
$query_sum_price = $db->prepare($sql_sum_price);
$query_sum_price->execute();
$sum_price = $query_sum_price->fetch(PDO::FETCH_ASSOC);

$shipping = $sum_price['sum_price'] != null ? 60 : 0;

$error_message = "";

function check_stock_sufficient()
{
  require("../connectconfig.php");
  $username = $_SESSION["username"];
  $sql_product_cart = <<<multi
    SELECT
      a.quantity,
      b.product_stocks
    FROM
      shopping_cart AS a
    INNER JOIN product_list AS b
    ON
      a.product_id = b.product_id AND a.username = '$username'
    multi;
  $product_stocks_data = $db->prepare($sql_product_cart);
  $product_stocks_data->execute();
  while ($stocks_data = $product_stocks_data->fetch(PDO::FETCH_ASSOC)) {
    if ($stocks_data['product_stocks'] < $stocks_data['quantity']) {
      return false;
    }
  }
  return true;
}

if (isset($_POST["checkout_btn"])) {
  if (check_stock_sufficient()) {
    if ($sum_price[0] != null) {

      global $shopping_cart_data;
      global $sum_price;

      $username = $_SESSION["username"];
      $paytype = $_POST["paytype"];

      $sql_add_order = <<<multi
        INSERT INTO orders(
            total_price,
            paytype,
            username
        )
        VALUES(
            {$sum_price[0]} + 60,
            '$paytype',
            '$username'
        );
      multi;
      $db->prepare($sql_add_order)->execute();

      $sql_order_id = <<<multi
        SELECT
          orders_id
        FROM
          `orders`
        ORDER BY
          orders_id
        DESC
        LIMIT 0, 1
      multi;
      $query_order_id_row = $db->prepare($sql_order_id);
      $query_order_id_row->execute();
      $order_id_row = $query_order_id_row->fetch(PDO::FETCH_ASSOC);

      while ($cart_data = $shopping_cart_data->fetch(PDO::FETCH_ASSOC)) {
        $sql_checkout = <<<multi
          INSERT INTO order_detail(
              `orders_id`,
              `product_id`,
              `quantity`,
              `product_name`,
              `product_price`,
              `product_images`
          )
          VALUES(
              '{$order_id_row[0]}',
              '{$cart_data['product_id']}',
              '{$cart_data['quantity']}',
              '{$cart_data['product_name']}',
              '{$cart_data['product_price']}',
              '{$cart_data['product_images']}'
          );
        multi;
        $db->prepare($sql_checkout)->execute();

        // reduce_stocks
        $sql_product_stocks = <<<multi
          SELECT
            product_stocks
          FROM
            product_list
          WHERE
            product_id = '{$cart_data['product_id']}'
        multi;
        $query_product_stocks_row = $db->prepare($sql_product_stocks);
        $query_product_stocks_row->execute();
        $product_stocks_row = $query_product_stocks_row->fetch(PDO::FETCH_ASSOC);

        $sql_quantity = <<<multi
          SELECT
            SUM(quantity) AS `quantity`
          FROM
            shopping_cart
          WHERE
            username = '$username' AND product_id = '{$cart_data['product_id']}'
        multi;
        $query_quantity_row = $db->prepare($sql_quantity);
        $query_quantity_row->execute();
        $quantity_row = $query_quantity_row->fetch(PDO::FETCH_ASSOC);

        $reduced_quantity = $product_stocks_row['product_stocks'] - $quantity_row['quantity'];

        $sql_reduce_stocks = <<<multi
          UPDATE
            product_list
          SET
            `product_stocks` = '$reduced_quantity'
          WHERE
            `product_id` = '{$cart_data['product_id']}'
        multi;
        $db->prepare($sql_reduce_stocks)->execute();
      }

      $sql_delete_cart = <<<multi
        DELETE
        FROM
            shopping_cart
        WHERE
            username = '$username'
      multi;
      $db->prepare($sql_delete_cart)->execute();

      header("Location: checkout_successful.php");
      exit();
    } else {
      $error_message = "購物車裡沒有物品喔！快去買吧！";
    }
  } else {
    header("Location: Insufficient_stock_alert.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>購物網站 - 購物車</title>
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
            <span id="sum_quantity" class="badge badge-danger"><?= Update_purchase_quantity(); ?></span>
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
              <td colspan="7">
                <p class="title">會員系統 － 購物車</p>
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
                <p class="title">庫存</p>
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
              <td>
                <p class="title">操作</p>
              </td>
            </tr>
          </thead>
          <tbody>
            <?php while ($cart_data = $shopping_cart_data->fetch(PDO::FETCH_ASSOC)) { ?>
              <tr class="text-center">
                <td class="align-middle"><?= $cart_data['product_name'] ?></td>
                <td class="align-middle">
                  <img class="product_images" src="../img/<?= $cart_data['product_images'] ?>" alt="" srcset="">
                </td>
                <td class="align-middle"><?= $cart_data['product_stocks'] ?></td>
                <td class="align-middle">$<?= $cart_data['product_price'] ?></td>
                <td class="align-middle">
                  <input type="hidden" class="username" value="<?= $_SESSION["username"] ?>">
                  <input type="hidden" class="product_price" value="<?= $cart_data['product_price'] ?>">
                  <input type="number" class="purchase_quantity" name="purchase_quantity" min="1" max="<?= $cart_data['product_stocks'] ?>" value="<?= $cart_data['quantity'] ?>">
                </td>
                <td class="align-middle item_sum_price">$<?= $cart_data['quantity'] * $cart_data['product_price'] ?></td>
                <td class="align-middle">
                  <form action="" method="post">
                    <input type="hidden" class="product_id" name="product_id" value="<?= $cart_data['product_id'] ?>">
                    <input class="btn btn-outline-danger" type="submit" name="delete_cart_product" value="刪除">
                  </form>
                </td>
              </tr>
            <?php } ?>
            <tr class="table-info">
              <td colspan="7">
                <span>運費：$<?= $shipping ?></span>
                <h3 id="total_amount">總金額：$<?= $sum_price['sum_price'] + $shipping ?></h3>
                <label for="cars">選擇付費方式：</label>
                <select name="paytype" form="checkout">
                  <option value="ATM匯款">ATM匯款</option>
                  <option value="線上刷卡">線上刷卡</option>
                  <option value="貨到付款">貨到付款</option>
                </select>
                <form action="" method="post" id="checkout">
                  <input class="btn btn-success" type="submit" id="checkout_btn" name="checkout_btn" value="結帳">
                </form>
                <p id="error_message"><?= $error_message ?></p>
                <a href="member_side.php" class="btn btn-warning" role="button">回商品列表</a>
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

  <script src="purchase_quantity.js"></script>

</body>

</html>