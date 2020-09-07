<?php

// MAMP uses the following scheme
$link = @new mysqli("localhost", "root", "root", "shopping_site_data", 8889);
$link->query("set names utf-8");

// XAMPP uses the following scheme
// $link = @new mysqli("localhost", "root", "", "shopping_site_data", 3306);
// $link->query("set names utf-8");
