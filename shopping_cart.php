<?php
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["username"] == "Guest") {
    header("Location: index.php");
    exit();
}

$username = $_SESSION["username"];

require_once("connectconfig.php");
$sql = <<<multi
    SELECT
        SUM(a.quantity),
        a.username,
        a.product_id,
        b.product_name,
        b.product_price,
        b.product_images
    FROM
        shopping_cart AS a
    INNER JOIN product_list AS b
    ON
        a.product_id = b.product_id
    WHERE
        a.username = '$username'
    GROUP BY
        a.username,
        a.product_id,
        b.product_name,
        b.product_price,
        b.product_images
    multi;
$result = $link->query($sql);
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
                    <a class="nav-link" href="shopping_cart.php">購物車</a>
                </li>
                <li class="nav-item nav_item_form">
                    <form action="" method="post">
                        <input class="" type="submit" name="btnSignOut" id="btnSignOut" value="登出" />
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <div>
        <table class="table table-bordered">
            <thead>
                <tr class="bg-primary text-light">
                    <td colspan="6">
                        <p class="title">會員系統 － 查詢明細</p>
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
                        <p class="title">數量</p>
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
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="text-center">
                        <td class="align-middle"><?= $row['product_name'] ?></td>
                        <td class="align-middle">
                            <img class="product_images" src="./img/<?= $row['product_images'] ?>" alt="" srcset="">
                        </td>
                        <td class="align-middle"><?= $row['product_price'] ?></td>
                        <td class="align-middle"><?= $row['SUM(a.quantity)'] ?></td>
                        <td class="align-middle"><?= $row['SUM(a.quantity)'] * $row['product_price'] ?></td>
                        <td class="align-middle">
                            <input type="submit" value="刪除">
                        </td>
                    </tr>
                <?php } ?>
                <tr class="bg-primary text-light">
                    <td colspan="6">
                        <a href="secret.php" class="btn btn-warning" role="button">回到首頁</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>