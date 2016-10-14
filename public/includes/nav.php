<div class="nav-trigger"><span></span><span></span><span></span><span></span></div>
<div class="clear"></div>
<nav class="nav">
  <ul class="nav-list-1">
    <li class="nav-el-1<?php if($this->routes['index']==$_GET['page']) echo " is-selected"; ?>">
      <a href="<?=URL_ROOT.$this->lang2.'/'.$this->routes['index']; ?>" class="nav-el-1-link"><?php if(isset($navigation['index'])) echo $navigation['index']; ?></a>
      <ul class="nav-list-2">
        <li class="nav-el-2"><a href="<?=URL_ROOT.$this->lang2; ?>" class="nav-el-2-link">SubNav 1</a></li>
        <li class="nav-el-2"><a href="<?=URL_ROOT.$this->lang2; ?>" class="nav-el-2-link">SubNav 2</a></li>
        <li class="nav-el-2"><a href="<?=URL_ROOT.$this->lang2; ?>" class="nav-el-2-link">SubNav 3</a></li>
      </ul>
    </li>
  </ul>
</nav>
<?php if(@include(COMPLETE_URL_ROOT . 'app/helpers/cart/includes/cart-nav.php')); ?>
