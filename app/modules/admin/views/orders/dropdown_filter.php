<?php
/* @var $title string */
/* @var $allLinkHtml string */
/* @var $itemsHtml string */
?>

<th class="dropdown-th">
    <div class="dropdown">
        <button class="btn btn-th btn-default dropdown-toggle" type="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <?= $title ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            <?= $allLinkHtml ?>
            <?= $itemsHtml ?>
        </ul>
    </div>
</th>