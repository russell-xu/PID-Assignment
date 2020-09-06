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

$error_message = "";

if (isset($_POST["submit_modify"])) {
  $product_id = $_SESSION["product_id"];
  $product_name = $_POST["product_name"];
  $product_price = $_POST["product_price"];
  $product_stocks = $_POST["product_stocks"];
  $product_images = $_POST["product_images"];
  $product_description = $_POST["product_description"];

  if ($product_name !== "" && $product_price !== "" && $product_stocks !== "" && $product_images !== "" && $product_description !== "") {

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
  } else {
    $error_message = "每個欄位必須都有填寫！";
  }
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
  <title>管理員 - 修改商品</title>
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

    .operating_btn {
      font-size: 20px;
    }

    #upload_img_box {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
    }

    #product_image {
      width: auto;
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
        <h1 id="title">修改商品</h1>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <form method="POST" id="add_form">
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
          <input type="hidden" id="image_sync" name="product_images" value="<?= $product['product_images'] ?>">
        </form>
        <form action="upload_images.php" method="post" enctype="multipart/form-data" target="the_iframe">
          <div class="form-group">
            <label for="product_image">商品圖片</label>
            <p>目前選擇：<span id="selected_image"><?= $product['product_images'] ?></span></p>
            <div id="upload_img_box">
              <input type="file" class="form-control-file" id="product_image" name="product_images">
              <input type="submit" class="btn btn-warning" id="upload_btn" name="upload_image" value="上傳圖片">
            </div>
          </div>
        </form>
        <iframe id="is_iframe" name="the_iframe" style="display:none;"></iframe>
        <div class="form-group">
          <label for="product_description">商品描述</label>
          <textarea class="form-control" id="product_description" rows="6" name="product_description" form="add_form"><?= $product['product_description'] ?></textarea>
        </div>
        <p id="error_message"><?= $error_message ?></p>
        <a href="commodity_management.php" class="btn btn-danger operating_btn">取消</a>
        <input type="submit" class="btn btn-primary operating_btn" name="submit_modify" value="修改商品" form="add_form">
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script>
    let product_image = document.querySelector('#product_image')
    let image_sync = document.querySelector('#image_sync')
    let upload_btn = document.querySelector('#upload_btn')
    let selected_image = document.querySelector('#selected_image')
    upload_btn.addEventListener('click', () => {
      let path = product_image.value
      image_sync.value = path.substr(12)
      selected_image.innerHTML = path.substr(12)
      console.log(selected_image.innerHTML);
    })
  </script>
</body>

</html>