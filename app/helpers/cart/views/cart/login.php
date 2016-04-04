<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["commander"]?></h1>
<div class="l-grid l-row-2">
  <div class="l-grid-50">
    <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/messages.php')); ?>
    <h1><?=$login["existing_user"]?></h1>
    <div class="cart-card">
      <form action="<?=URL_ROOT . $lang2 . "/" . $routes["checkout_login"]?>" method="post" name="fATC">
        <label for="email"><?=$cart["form_email"]?></label>
        <input name="email" id="email" type="text">
        <label for="password"><?=$cart["form_password"]?></label>
        <input name="password" id="password" type="password">
        <button type="submit" class="btn is-full-width" name="connection"><?=$login["login"]?></button>
      </form>
    </div>
    <br>
    <p><a href="<?=URL_ROOT.$lang2."/".$routes["forgot"]?>"><?=$login["forgot-password"]?></a></p>
  </div>
  <div class="l-grid-50">
    <h1><?=$login["new_user"]?></h1>
    <div class="cart-card">
      <p><?=$login["new_user_text"]?></p>
      <a href="<?=URL_ROOT . $lang2 . "/" . $routes["checkout_billing-address"]?>" class="btn is-full-width"><?=$login["continue_invited"]?></a>
    </div>
  </div>
</div>
<?php endblock() ?>
