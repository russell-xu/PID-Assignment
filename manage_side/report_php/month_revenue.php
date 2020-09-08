<?php
require_once("../../connectconfig.php");

$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

$start_month = $decoded['start_month'];
$end_month = $decoded['end_month'];

$sql_single_day_revenue = <<<multi
SELECT
    DATE_FORMAT(`date`, '%Y-%m') AS `monthtime`,
    SUM(`total_price`) AS `total_price`
FROM
    orders
WHERE `date` BETWEEN '$start_month-00' AND '$end_month-31 23:59:59'
GROUP BY
    `monthtime`
multi;
$rows = $link->query($sql_single_day_revenue)->fetch_all(MYSQLI_ASSOC);
echo json_encode($rows, true);
