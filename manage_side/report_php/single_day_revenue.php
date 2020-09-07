<?php
require_once("../connectconfig.php");

$sql_single_day_revenue = <<<multi
SELECT
    DATE_FORMAT(`date`, '%y-%m-%d') AS `datetime`,
    SUM(`total_price`)
FROM
    orders
GROUP BY
    `datetime`
multi;
$rows = $link->query($sql_single_day_revenue)->fetch_row();
echo json_encode($rows, true);
