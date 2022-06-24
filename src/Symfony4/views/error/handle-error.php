<?php

/**
 * @var string $title
 * @var string $message
 * @var View $this
 * @var Exception $exception
 */

use ZnLib\Web\View\View;

?>

<div class="alert alert-danger" role="alert">
    <h4 class="alert-heading"><?= $title ?></h4>
    <p><?= $message ?></p>
    <?php if(isset($exception)): ?>
        <hr>
        <p>Class: <?= get_class($exception) ?></p>
        <p>File: <?= $exception->getFile() ?>:<?= $exception->getLine() ?></p>
        <pre><p style="font-size: 75% !important;"><?= $exception->getTraceAsString() ?></p></pre>
    <?php endif; ?>
</div>
