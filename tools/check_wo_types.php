<?php
$db = new mysqli('127.0.0.1', 'root', '', 'optima_ci');
$r = $db->query("SELECT order_type, COUNT(*) as cnt FROM work_orders GROUP BY order_type");
while ($row = $r->fetch_row()) echo $row[0] . ': ' . $row[1] . PHP_EOL;
