<?php
/* @var $actionUrl string */
/* @var $searchValue string */
/* @var $optionsHtml string */
/* @var $statusInputHtml string */
/* @var $searchPlaceholder string */
?>

<li class="pull-right custom-search">
    <form class="form-inline" action="<?= $actionUrl ?>" method="get">
        <div class="input-group">
            <?= $statusInputHtml ?>

            <input type="text" name="search" class="form-control" value="<?= $searchValue ?>"
                   placeholder="<?= $searchPlaceholder ?>">

            <span class="input-group-btn search-select-wrap">
                <select class="form-control search-select" name="search_type">
                    <?= $optionsHtml ?>
                </select>
                <button type="submit" class="btn btn-default">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                </button>
            </span>
        </div>
    </form>
</li>