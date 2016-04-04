<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$login['recover.title']?></h1>
<p><?=$login['recover.not_secure']?></p>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
<form action="" method="POST" name="login">
    <label for="password"><?=$login['password']?></label>
    <input name="password" type="password">
    <label for="password_confirm"><?=$login['password_confirm']?></label>
    <input name="password_confirm" type="password">
    <input name="action" type="hidden" value="reset">
    <button class="btn" name="btSubmit" type="submit"><?=$login['recover.btn']?></button>
</form>
<?php endblock() ?>
