<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["thanks"]?></h1>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
<?php endblock() ?>
