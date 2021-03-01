<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
		die();
	}

	use	Bitrix\Main\Loader,
		Bitrix\Iblock;

	$arParams["MAX_VISIBLE_TAGS"] = intval(!empty($arParams["MAX_VISIBLE_TAGS"]) ? $arParams["MAX_VISIBLE_TAGS"] : "10");
	$arParams["SECTION_DEPTH_LEVEL"] = intval(!empty($arParams["SECTION_DEPTH_LEVEL"]) ? $arParams["SECTION_DEPTH_LEVEL"] : 0);
	$arParams["INCLUDE_SUBSECTIONS"] = !empty($arParams["INCLUDE_SUBSECTIONS"]) ? $arParams["INCLUDE_SUBSECTIONS"] : "Y";
	$arParams["HIDE_NOT_AVAILABLE"] = !empty($arParams["HIDE_NOT_AVAILABLE"]) ? $arParams["HIDE_NOT_AVAILABLE"] : "N";
	$arParams["CURRENT_TAG"] = !empty($arParams["CURRENT_TAG"]) ? $arParams["CURRENT_TAG"] : "";
	$arParams["SORT_FIELD"] = !empty($arParams["SORT_FIELD"]) ? $arParams["SORT_FIELD"] : "COUNTER";
	$arParams["SORT_TYPE"] = !empty($arParams["SORT_TYPE"]) ? $arParams["SORT_TYPE"] : "DESC";
	$arParams["MAX_TAGS"] = intval(!empty($arParams["MAX_TAGS"]) ? $arParams["MAX_TAGS"] : "30");

	global $APPLICATION, $USER, $arrFilter;

    //собираем параметры для кэширования
	$cacheID = array(
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"SECTION_CODE" => $arParams["SECTION_CODE"],
		"CURRENT_TAG" => $arParams["CURRENT_TAG"],
		"SECTION_ID" => $arParams["SECTION_ID"],
		"SEF_FOLDER" => $arParams["SEF_FOLDER"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"MAX_TAGS" => $arParams["MAX_TAGS"],
		"USER_GROUPS" => $USER->GetGroups(),
		"SITE_ID" => SITE_ID
	);

	if($this->StartResultCache($arParams["CACHE_TIME"], serialize($cacheID))) {

		$arFilter = [
            "IBLOCK_ID"             => $arParams["IBLOCK_ID"],
            "!TAGS"                 => false,
            "ACTIVE_DATE"           => "Y",
            "ACTIVE"                => "Y",
            "INCLUDE_SUBSECTIONS"   => $arParams["INCLUDE_SUBSECTIONS"],
            "IBLOCK_LID"            => SITE_ID
        ];

		if(!empty($arParams["SECTION_ID"])) {
			$arFilter["=SECTION_ID"] = $arParams["SECTION_ID"];
		}

		else{
			$arFilter["=SECTION_CODE"] = $arParams["SECTION_CODE"];
		}

		if($arParams["HIDE_NOT_AVAILABLE"] == "Y") {
			$arFilter["CATALOG_AVAILABLE"] = "Y";
		}
		$sectionPath = (!empty($arParams["SECTION_CODE_PATH"]) ? $arParams["SECTION_CODE_PATH"] : $arParams["SECTION_CODE"]);

		if(empty($sectionPath) && !empty($arParams["SECTION_ID"])) {
			$sectionPath = $arParams["SECTION_ID"];
		}

		$tagPath = $arParams["SEF_FOLDER"] . $sectionPath . "/";

		//выбираем все теги из категории
		$rsProducts = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "TAGS", "IBLOCK_SECTION_ID"));
		while($obNextProduct = $rsProducts->GetNextElement()) {
			$arProduct = $obNextProduct->GetFields();

			if(!empty($arProduct["TAGS"])) {
                //проверяем тэги
				$arTags = explode(",", $arProduct["TAGS"]);
				foreach($arTags as $inx => $tagName) {
					$tagCode = Cutil::translit($tagName, LANGUAGE_ID, array("change_case" => "L", "replace_space" => "-", "replace_other" => "-"));
					$arTag = array("LINK" => $tagPath.$tagCode, "NAME" => $tagName, "CODE" => $tagCode, "COUNTER" => 1);

					//помечаем выбранный тег
					if($arParams["CURRENT_TAG"] == $tagCode) {
						$arTag["LINK"] = $tagPath;
						$arTag["SELECTED"] = "Y";
					}

					if(!empty($arResult["TAGS"][$tagCode])) {
						$arTag["COUNTER"] = $arResult["TAGS"][$tagCode]["COUNTER"] + 1;
					}
					$arResult["TAGS"][$tagCode] = $arTag;
				}
			}
		}

		//делаем сортировку тегов, если надо
		if(!empty($arResult["TAGS"])) {
			uasort($arResult["TAGS"], function($a, $b) use($arParams) {
			    if($a[$arParams["SORT_FIELD"]] == $b[$arParams["SORT_FIELD"]]) {
			        return false;
			    }
			    if($arParams["SORT_TYPE"] == "DESC"){
			    	return ($a[$arParams["SORT_FIELD"]] > $b[$arParams["SORT_FIELD"]]) ? -1 : 1;
				} else {
				    if($arParams["SORT_TYPE"] == "ASC") {
				    	return ($a[$arParams["SORT_FIELD"]] < $b[$arParams["SORT_FIELD"]]) ? -1 : 1;
					}
				}
			});
		}
		$this->IncludeComponentTemplate();
	}

    //проверяем на наличие, либо выкидываем 404, добавляем в фильтр
	if($arParams["CURRENT_TAG"] != "index.php") {
        if(!empty($arResult["TAGS"][$arParams["CURRENT_TAG"]])) {
            $arResult["CURRENT_TAG"] = $arResult["TAGS"][$arParams["CURRENT_TAG"]];
            $arrFilter["?TAGS"] = $arResult["CURRENT_TAG"]["NAME"];
            return $arResult;

        } else {
            if(!empty($arParams["CURRENT_TAG"])) {
                if(Loader::includeModule("iblock")) {
                    Iblock\Component\Tools::process404(
                        trim($arParams["MESSAGE_404"]) ?: GetMessage("CATALOG_TAGS_MESSAGE_404")
                        ,true
                        ,$arParams["SET_STATUS_404"] === "Y"
                        ,$arParams["SHOW_404"] === "Y"
                        ,$arParams["FILE_404"]
                    );
                }
            }
            return false;
        }
    }

?>