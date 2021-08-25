<?php

$inixmodule = file_get_contents(dirname(__FILE__) . '/InixModule.php');
$inixmodule = str_replace('<?php', '', $inixmodule);
$inixmodule = str_replace(
    'class Inix2Module extends Module',
    'class Inix2PaymentModule extends PaymentModule',
    $inixmodule
);

eval($inixmodule);
