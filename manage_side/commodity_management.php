<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "admin") {
  header("Location: ../index.php");
  exit();
}

if (isset($_POST["btnSignOut"])) {
  $_SESSION["username"] = "Guest";
  header("Location: ../index.php");
  exit();
}

require_once("../connectconfig.php");

if (isset($_POST["modify"])) {
  $_SESSION["product_id"] = $_POST["product_id"];
  header("Location: manage_modify_product.php");
  exit();
}

if (isset($_POST["delete"])) {
  $product_id = $_POST["product_id"];
  $sql_product = <<<multi
    DELETE
    FROM
        product_list
    WHERE
        `product_id` = '$product_id'
  multi;
  $link->query($sql_product);
}

function query_products()
{
  require("../connectconfig.php");
  $sql_product = <<<multi
    SELECT
      *
    FROM
      `product_list`
  multi;
  return $link->query($sql_product);
}
$query_products = query_products();
?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>管理員 - 商品管理</title>
  <style>
    body {
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
      word-wrap: break-word;
    }

    #product_description {
      width: 20vw;
    }

    .table #product_description .describe_box {
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      white-space: normal;
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

    .product_image {
      width: 100px;
      height: 100px;
      object-fit: cover;
    }

    #add_product_box {
      width: 100%;
      padding: 30px 0;
    }

    #add_product_btn {
      display: block;
      width: 200px;
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
        <div id="add_product_box">
          <a href="add_product.php" id="add_product_btn" class="btn btn-warning mx-auto">新增商品</a>
        </div>
        <table class="table table-bordered">
          <thead>
            <tr class="bg-primary text-light">
              <td colspan="6">
                <p class="title">管理系統 － 商品管理</p>
              </td>
            </tr>
            <tr class="bg-success text-light">
              <td>
                <p class="title">商品名稱</p>
              </td>
              <td>
                <p class="title">單價</p>
              </td>
              <td>
                <p class="title">庫存</p>
              </td>
              <td>
                <p class="title">商品圖片</p>
              </td>
              <td>
                <p class="title">商品描述</p>
              </td>
              <td>
                <p class="title">操作</p>
              </td>
            </tr>
          </thead>
          <tbody>
            <?php while ($query_products_data = $query_products->fetch_assoc()) { ?>
              <tr class="text-center">
                <td class="align-middle"><?= $query_products_data['product_name'] ?></td>
                <td class="align-middle">$<?= $query_products_data['product_price'] ?>
                </td>
                <td class="align-middle"><?= $query_products_data['product_stocks'] ?></td>
                <td class="align-middle">
                  <img class="product_image" src="../img/<?= $query_products_data['product_images'] ?>" alt="">
                </td>
                <td id="product_description" class="align-middle">
                  <div class="describe_box"><?= $query_products_data['product_description'] ?></div>
                </td>
                <td class="align-middle">
                  <form action="" method="post">
                    <input type="hidden" name="product_id" value="<?= $query_products_data['product_id'] ?>">
                    <input class="btn btn-outline-info" type="submit" name="modify" value="修改">
                    <input class="btn btn-outline-danger" type="submit" name="delete" value="刪除">
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