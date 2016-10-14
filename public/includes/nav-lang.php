<nav class="nav-lang">
    <ul class="nav-lang-list">
      <?php foreach($this->possible_languages as $language => $code) : ?>
          <?php if($language!=$this->lang2) : ?>
          <li class="nav-lang-el"><a href="<?=URL_ROOT.$language.$this->translateFromPage()?>" title="<?=strtoupper($language)?>" class="nav-lang-el-link"><?=strtoupper($language)?></a></li>
          <?php endif; ?>
      <?php endforeach; ?>
    </ul>
</nav>
