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
WHERE `date` BETWEEN '$start_date' AND '$end_date 23:59:59'
GROUP BY
    `datetime`
multi;
$rows = $db->prepare($sql_single_day_revenue);
$rows->execute();
$result = $rows->fetchALL(PDO::FETCH_ASSOC);
echo json_encode($result, true);