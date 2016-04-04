<?php include('app/views/layouts/two-columns.php') ?>
<?php startblock('content') ?>
<h1>Home</h1>
<?php endblock() ?>
<?php startblock('sidebar') ?>
<h1>Sidebar</h1>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/flexslider.php')); ?>
<?php endblock() ?>
