<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
    <?if(!empty($arResult["TAGS"])) { ?>
    <div class="catalog-tags">
        <div class="collapse-panel">
            <div class="collapse-panel__text">Часто ищут:</div>
                <div class="favorite-tags">
                <?
                $index = 0;
                foreach ($arResult["TAGS"] as $tag) { ?>
                    <a href="<?=$tag["LINK"]?>" class="tags-link__item"><?=$tag["NAME"]?></a>
                    <?
                    $index++;
                    if($index >=3)
                        break;
                    ?>
                <? }
                ?>
                </div>
            <div class="all-request__text">Все запросы</div>
        </div>
        <div class="catalog-seek__collapse-wrap">
             <? foreach ($arResult["TAGS"] as $tag) { ?>
                <a href="<?=$tag["LINK"]?>" class="tags-link__item"><?=$tag["NAME"]?></a>
            <? } ?>
        </div>
    </div>
    <? } ?>
