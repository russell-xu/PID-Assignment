<?php
// MAMP uses the following scheme
// $link = mysqli_connect("localhost", "root", "root", "shopping_site_data", 8889);
// mysqli_query($link, "set names utf-8");

// XAMPP uses the following scheme
// $link = mysqli_connect("localhost", "root", "", "shopping_site_data", 3306);
// mysqli_query($link, "set names utf-8");

$link = @new mysqli("localhost", "root", "root", "shopping_site_data", 8889);
$link->query("set names utf-8");