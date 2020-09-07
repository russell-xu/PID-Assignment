<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["username"] == "Guest") {
  header("Location: index.php");
  exit();
}

if (isset($_POST["btnSignOut"])) {
  $_SESSION["username"] = "Guest";
  header("Location: index.php");
  exit();
}

require_once("connectconfig.php");

if (isset($_POST["add_cart_input"])) {
  $username = $_SESSION["username"];
  $product_id = $_POST["product_id"];
  $purchase_quantity = $_POST["purchase_quantity"];
  $product_stocks = $_POST["product_stocks"];

  $sql_cart_has_product = <<<multi
    SELECT
      quantity
    FROM
      shopping_cart
    WHERE
      username = '$username' AND product_id = '$product_id'
  multi;
  $cart_has_product = $link->query($sql_cart_has_product)->fetch_row();

  if ($purchase_quantity > 0) {
    if ($cart_has_product == null) {
      $sql_add_cart = <<<multi
        INSERT INTO shopping_cart(
            quantity,
            username,
            product_id
        )
        VALUES('$purchase_quantity', '$username', '$product_id');
      multi;
      $link->query($sql_add_cart);
    } else {
      $sql_update_add_cart = <<<multi
        UPDATE
            shopping_cart
        SET
            quantity = '$cart_has_product[0]' + '$purchase_quantity'
        WHERE
            username = '$username' AND product_id = '$product_id'
      multi;
      $link->query($sql_update_add_cart);
    }
  }
}

function Update_purchase_quantity()
{
  require("connectconfig.php");
  $username = $_SESSION["username"];
  $sql_quantity = <<<multi
    SELECT
        SUM(`quantity`)
    FROM
        shopping_cart
    WHERE
        username = '$username'
    multi;
  $quantity_row = $link->query($sql_quantity)->fetch_row();
  return $quantity_row[0];
}

$sql_product_list = <<<multi
  SELECT
      *
  FROM
      `product_list`
multi;
$result = $link->query($sql_product_list);
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>購物網站 - 商品列表</title>
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

    .product_list_title {
      margin: 20px 0;
      text-align: center;
    }

    .product_list {
      display: flex;
      justify-content: center;
    }

    .product {
      display: flex;
      align-items: center;
      padding: 20px;
      margin: 10px;
      background-color: wheat;
    }

    .product .img_box {
      width: 200px;
      height: 200px;
    }

    .product .img_box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product .product_content {
      flex: 2;
      width: auto;
      height: 200px;
      padding: 0 20px;
    }

    .product .price_addcart_box {
      flex: 1;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
    }

    .product .price_addcart_box h3 {
      width: 100%;
      text-align: center;
    }

    .product .price_addcart_box form .add_cart_input {
      background-color: yellowgreen;
    }

    .product .price_addcart_box form .purchase_quantity {
      width: 50px;
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
          <a class="nav-link" href="member_side.php">商品列表</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="client_query_order.php">查詢訂單</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="shopping_cart.php">購物車
            <span id="update_purchase_quantity" class="badge badge-danger"><?= Update_purchase_quantity() ?></span>
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
        <h1 class="product_list_title">商品列表</h1>
      </div>
    </div>
    <?php while ($row = $result->fetch_assoc()) { ?>
      <div class="row product_list">
        <div class="col-10">
          <div class="product">
            <div class="img_box">
              <img src="./img/<?= $row['product_images'] ?>" alt="">
            </div>
            <div class="product_content">
              <h3><?= $row['product_name'] ?></h3>
              <p><?= $row['product_description'] ?></p>
            </div>
            <div class="price_addcart_box">
              <h3>$<?= $row['product_price'] ?></h3>
              <h6>庫存：<?= $row['product_stocks'] ?></h6>
              <form class="add_cart" name="add_cart" action="" method="post" target="the_iframe">
                <input type="hidden" name="product_stocks" value="<?= $row['product_stocks'] ?>">
                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                <input class="purchase_quantity" type="number" name="purchase_quantity" class="purchase_quantity" min="0" max="<?= $row['product_stocks'] ?>" value="0">
                <input class="add_cart_input" type="submit" name="add_cart_input" value="加入購物車">
              </form>
              <iframe id="is_iframe" name="the_iframe" style="display:none;"></iframe>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script>
    let add_cart = document.querySelectorAll('.add_cart')
    let update_purchase_quantity = document.querySelector('#update_purchase_quantity')
    let purchase_quantity = document.querySelectorAll('.purchase_quantity')
    let cart_quantity = 0
    for (let i = 0; i < add_cart.length; i++) {
      add_cart[i].addEventListener("submit", () => {
        if (update_purchase_quantity.innerHTML === '' && parseInt(purchase_quantity[i].value) === 0) {
          return false
        } else if (update_purchase_quantity.innerHTML === '' && parseInt(purchase_quantity[i].value) !== 0) {
          update_purchase_quantity.innerHTML = cart_quantity + parseInt(purchase_quantity[i].value)
        } else {
          cart_quantity = parseInt(update_purchase_quantity.innerHTML)
          update_purchase_quantity.innerHTML = cart_quantity + parseInt(purchase_quantity[i].value)
        }
      })
    }
  </script>
</body>

</html>