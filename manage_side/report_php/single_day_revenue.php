<?php
require_once("../../connectconfig.php");

$sql_single_day_revenue = <<<multi
SELECT
    DATE_FORMAT(`date`, '%Y-%m-%d') AS `datetime`,
    SUM(`total_price`) AS `total_price`
FROM
    orders
GROUP BY
    `datetime`
multi;
$rows = $link->query($sql_single_day_revenue)->fetch_all(MYSQLI_ASSOC);
echo json_encode($rows, true);
