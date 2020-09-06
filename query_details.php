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

$id = $_SESSION["id"];

require_once("connectconfig.php");
$sql = <<<multi
SELECT
    *
FROM
    `detail`
WHERE
    `Idnumber` = '$id'
multi;
$result = $link->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>購物網站 - 查詢明細</title>
    <style>
        body {
            padding: 30px 0;
            display: flex;
            justify-content: center;
            font-size: 20px;
            font-family: Microsoft JhengHei;
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
    <div>
        <table class="table table-bordered">
            <thead>
                <tr class="bg-primary text-light">
                    <td colspan="4">
                        <p class="title">會員系統 － 查詢明細</p>
                    </td>
                </tr>
                <tr class="bg-success text-light">
                    <td>
                        <p class="title">日期時間</p>
                    </td>
                    <td>
                        <p class="title">提款</p>
                    </td>
                    <td>
                        <p class="title">存款</p>
                    </td>
                    <td>
                        <p class="title">餘額</p>
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="text-center">
                        <td class="align-middle"><?= $row['DATE'] ?></td>
                        <td class="align-middle"><?= $row['Withdrawal'] ?></td>
                        <td class="align-middle"><?= $row['Deposit'] ?></td>
                        <td class="align-middle"><?= $row['Balance'] ?></td>
                    </tr>
                <?php } ?>
                <tr class="bg-primary text-light">
                    <td colspan="4">
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