<?php
$dbms = 'mysql';
$host = 'localhost';
$dbName = 'shopping_site_data';
$user = 'root';
$pass = 'root'; // MAMP:root
$dsn = "$dbms:host=$host;dbname=$dbName";

try {
    $dbh = new PDO($dsn, $user, $pass);
    // echo "連接成功<br/>";
    $dbh = null;
} catch (PDOException $e) {
    die("Error!: " . $e->getMessage() . "<br/>");
}
$db = new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => true));
