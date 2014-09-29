<a href="#" class="navigation-trigger"></a>
<nav class="navigation">
  <ul>
    <li<?php if($routes['index']==$_GET['page']) echo " class='selected'"; ?>><a href="<?php echo URL_ROOT.$lang2.'/'.$routes['index']; ?>.html"><?php if(isset($navigation['index'])) echo $navigation['index']; ?></a></li>
    <li><a href="<?php echo URL_ROOT.$lang2; ?>.html">Navigation 2</a>
      <ul>
        <li><a href="<?php echo URL_ROOT.$lang2; ?>.html">SubNav 1</a></li>
        <li><a href="<?php echo URL_ROOT.$lang2; ?>.html">SubNav 2</a></li>
        <li><a href="<?php echo URL_ROOT.$lang2; ?>.html">SubNav 3</a></li>
      </ul>
    </li>
    <li><a href="<?php echo URL_ROOT.$lang2; ?>.html">Navigation 3</a></li>
    <li><a href="<?php echo URL_ROOT.$lang2; ?>.html">Navigation 4</a></li>
  </ul>
</nav>
