<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CIblockExtWork extends CBitrixComponent
{

    /**
     * подготавливаем параметры. т.к. у нас не стоит задачи соорудить компонент,
     * стандартный метод onPrepareComponentParams не используем
     *
     * @param $arFilter
     * @return mixed
     */
    private function prepareParams($arFilter)
    {
        if(!empty($arFilter["ORDER"]))
            $arParams["ORDER"] = $arFilter["ORDER"];
        else
            $arParams["ORDER"] = [];

        if(!empty($arFilter["SELECT"]))
            $arParams["SELECT"] = $arFilter["SELECT"];
        else
            $arParams["SELECT"] = ["*"];

        if(!empty($arFilter["FILTER"]))
            $arParams["FILTER"] = $arFilter["FILTER"];
        else
            $arParams["FILTER"] = [];

        if(!empty($arFilter["LIMIT"]))
            $arParams["LIMIT"] = $arFilter["LIMIT"];
        else
            $arParams["LIMIT"] = 1000;

        return $arParams;
    }

    /**
     * передаем в метод необходимые параметры: сортировка, список полей,фильтр,ограничение
     * на выходе получаем список элементов инфоблока
     *
     * @param $arFilter
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getIblockList($arFilter)
    {

        $arFilters = $this->prepareParams($arFilter);

        $arIbItems = \Bitrix\Iblock\ElementTable::getList(array(
            'order'  => $arFilters["ORDER"],
            'select' => $arFilters["SELECT"],
            'filter' => $arFilters["FILTER"],
            'limit'  => $arFilters["LIMIT"],
            'cache' => array( //кэшируем запрос на этом уровне. дополнительно устраивать кэширование прямо в методе нет смысла
                'ttl' => 3600,
                'cache_joins' => true
            ),
        ));

        while($arItem = $arIbItems->fetch()) {
            $arItems[] = $arItem;
        }

        return $arItems;
    }

}