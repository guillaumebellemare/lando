<div class="navigation-trigger"><span></span><span></span><span></span><span></span></div>
<div class="clear"></div>
<nav class="navigation">
  <ul>
    <li<?php if($routes['index']==$_GET['page']) echo " class='is-selected'"; ?>><a href="<?=URL_ROOT.$lang2.'/'.$routes['index']; ?>">
      <?php if(isset($navigation['index'])) echo $navigation['index']; ?>
      </a>
      <ul>
        <li><a href="<?=URL_ROOT.$lang2; ?>">SubNav 1</a></li>
        <li><a href="<?=URL_ROOT.$lang2; ?>">SubNav 2</a></li>
        <li><a href="<?=URL_ROOT.$lang2; ?>">SubNav 3</a></li>
      </ul>
    </li>
  </ul>
</nav>
