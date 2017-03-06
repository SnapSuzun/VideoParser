<?php

/* @var $this core\View */
/* @var $code integer */
/* @var $message string */

?>

<div class="site-error" style="text-align: center;">

    <h1><?= $code ?></h1>

    <div class="alert alert-danger">
        <?= nl2br($message) ?>
    </div>

</div>

