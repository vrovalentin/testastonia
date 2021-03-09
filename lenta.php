<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

//второе тестовое задание. просто юзаем битриксовый изкоробочный компонент.
//немного правим под условия задания шаблон

$APPLICATION->IncludeComponent("bitrix:rss.show","testtemplate",Array(
    "URL" => "https://lenta.ru/rss",
    "OUT_CHANNEL" => "N",
    "PROCESS" => "NONE",
    "NUM_NEWS" => "5",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "3600"
));



