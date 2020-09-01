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

if (isset($_POST["submit_modify"])) {
  $product_id = $_SESSION["product_id"];
  $product_name = $_POST["product_name"];
  $product_price = $_POST["product_price"];
  $product_stocks = $_POST["product_stocks"];
  $product_images = $_POST["product_images"];
  $product_description = $_POST["product_description"];

  $sql_update_product = <<<multi
    UPDATE
      product_list
    SET
      `product_name` = '$product_name',
      `product_price` = '$product_price',
      `product_stocks` = '$product_stocks',
      `product_images` = '$product_images',
      `product_description` = '$product_description'
    WHERE
      `product_id` = '$product_id'
  multi;
  $link->query($sql_update_product);
  header("Location: commodity_management.php");
  exit();
}

function query_product()
{
  $product_id = $_SESSION["product_id"];
  require("connectconfig.php");
  $sql_product = <<<multi
    SELECT
      *
    FROM
      `product_list`
    WHERE
      `product_id` = '$product_id'
  multi;
  return $link->query($sql_product);
}
$query_product = query_product();
$product = $query_product->fetch_assoc();
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

    #title {
      margin-bottom: 20px;
    }

    .container {
      padding: 30px;
    }

    #product_description {
      resize: none;
    }

    .btn {
      font-size: 20px;
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
        <h1 id="title">修改商品</h1>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <form method="POST">
          <div class="form-group">
            <label for="product_name">商品名稱</label>
            <input type="text" class="form-control" id="product_name" aria-describedby="emailHelp" name="product_name" value="<?= $product['product_name'] ?>">
            <small id="emailHelp" class="form-text text-muted">error message</small>
          </div>
          <div class="form-group">
            <label for="product_price">單價</label>
            <input type="number" class="form-control" id="product_price" min="1" max="9999999999" name="product_price" value="<?= $product['product_price'] ?>">
          </div>
          <div class="form-group">
            <label for="product_stocks">庫存</label>
            <input type="number" class="form-control" id="product_stocks" min="0" max="9999999999" name="product_stocks" value="<?= $product['product_stocks'] ?>">
          </div>
          <div class="form-group">
            <label for="product_image">商品圖片</label>
            <input type="file" class="form-control-file" id="product_image" name="product_images" value="./img/<?= $product['product_images'] ?>">
          </div>
          <div class="form-group">
            <label for="product_description">商品描述</label>
            <textarea class="form-control" id="product_description" rows="6" name="product_description"><?= $product['product_description'] ?></textarea>
          </div>
          <a href="commodity_management.php" class="btn btn-danger">取消</a>
          <input type="submit" class="btn btn-primary" name="submit_modify" value="送出修改">
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>