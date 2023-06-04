<?php
namespace modules\style;

function loadStyle($name): void
{
    ?>
    <link rel="stylesheet" href="/assets/sass/<?= $name ?>.css">
    <?php
}
