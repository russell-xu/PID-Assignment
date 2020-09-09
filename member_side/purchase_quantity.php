<?php
require_once("../connectconfig.php");

$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

$quantity = $decoded['quantity'];
$name = $decoded['name'];
$id = $decoded['id'];

$sql_update_quantity = <<<multi
UPDATE
    `shopping_cart`
SET
    `quantity` = '$quantity'
WHERE
    `username` = '$name' AND `product_id` = '$id'
multi;
$db->prepare($sql_update_quantity)->execute();
