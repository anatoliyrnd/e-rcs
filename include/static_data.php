<?php
 $repair_time=[
    0=>"не указан",
    1=> "30 мин",
    2=> "Сегодня",
    3=> "Завтра",
    4=> "Три дня",
    5=> "7 дней",
    6=> "10 дней",
    7=> "15 дней",
    8=> "1 месяц",
    9=> "3 месяца",
];
$repair_time_unix=[
    0=>0,
    1=>strtotime("+30 minutes"),
    2=>strtotime(date("Y-m-d 23:59:59 ")),
    3=>strtotime(date("Y-m-d 23:59:59 ")."+1 day"),
    4=>strtotime(date("Y-m-d 23:59:59 ")."+3 day"),
    5=>strtotime(date("Y-m-d 23:59:59 ")."+7 day"),
    6=>strtotime(date("Y-m-d 23:59:59 ")."+10 day"),
    7=>strtotime(date("Y-m-d 23:59:59 ")."+15 day"),
    8=>strtotime(date("Y-m-d 23:59:59 ")."+1 month"),
    9=>strtotime(date("Y-m-d 23:59:59 ")."+3 month"),
]



?>