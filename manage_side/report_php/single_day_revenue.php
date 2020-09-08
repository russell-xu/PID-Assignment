<?php
require_once("../../connectconfig.php");

$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

$start_date = $decoded['start_date'];
$end_date = $decoded['end_date'];

$sql_single_day_revenue = <<<multi
SELECT
    DATE_FORMAT(`date`, '%Y-%m-%d') AS `datetime`,
    SUM(`total_price`) AS `total_price`
FROM
    orders
WHERE `date` BETWEEN '$start_date' AND '$end_date'
GROUP BY
    `datetime`
multi;
$rows = $link->query($sql_single_day_revenue)->fetch_all(MYSQLI_ASSOC);
echo json_encode($rows, true);
