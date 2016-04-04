<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$login["forgot.title"]?></h1>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
<form action="" method="POST" name="login">
    <label for="email"><?=$login["email"]?></label>
    <input name="email" type="text">
    <input name="action" type="hidden" value="send">
    <button class="btn" name="btSubmit" type="submit"><?=$login["forgot.title"]?></button>
</form>
<?php endblock() ?>
