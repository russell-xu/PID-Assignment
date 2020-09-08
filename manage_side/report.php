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

require_once("../connectconfig.php"); ?>

<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>管理員 - 會員管理</title>
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

    #myChart {
      height: 600px;
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
          <a class="nav-link" href="#">會員列表</a>
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

  <div class="container pt-3">
    <div class="row">
      <div class="col">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">查詢日營收</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">查詢月營收</a>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <form class="p-3">
              <div class="form-group">
                <label for="start_date">選擇起始日</label>
                <input type="date" class="form-control" id="start_date" name="start_date">
              </div>
              <div class="form-group">
                <label for="end_date">選擇結束日</label>
                <input type="date" class="form-control" id="end_date" name="end_date">
              </div>
              <button type="button" id="day_submit" class="btn btn-primary">提交</button>
            </form>
            <div id="single_day_canvas_box"></div>
          </div>
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <form class="p-3">
              <div class="form-group">
                <label for="start_month">選擇起始月</label>
                <input type="month" class="form-control" id="start_month" name="start_month">
              </div>
              <div class="form-group">
                <label for="end_month">選擇結束月</label>
                <input type="month" class="form-control" id="end_month" name="end_month">
              </div>
              <button type="button" id="month_submit" class="btn btn-primary">提交</button>
            </form>
            <div id="month_canvas_box"></div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>

  <script src="single_day_report.js"></script>
  <script src="month_report.js"></script>

</body>

</html>