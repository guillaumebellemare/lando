<?php if(@include(COMPLETE_URL_ROOT . 'app/core/app_messages.php')); ?>
<!doctype html>
<html>
<head>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/meta.php')) ?>
<title><?php startblock('title') ?><?php endblock() ?><?php if(isset($_GET['param3']) && isset($meta["{$_GET['param3']}.title"])) echo $meta["{$_GET['param3']}.title"]." | "; ?><?php if(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.title"])) echo $meta["{$_GET['param2']}.title"]." | "; ?><?php if(isset($_GET['param1']) && isset($meta["{$_GET['param1']}.title"])) echo $meta["{$_GET['param1']}.title"]." | "; ?><?=Meta::getMetaFromPageParam("title"); ?><?=Meta::getMetaFromPage("title"); ?><?php if(isset($meta["site.title"])) echo $meta["site.title"]; ?></title>
<meta name="keywords" content="<?php if(isset($_GET['param3']) && isset($meta["{$_GET['param3']}.keywords"])) echo $meta["{$_GET['param3']}.keywords"]; elseif(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.keywords"])) echo $meta["{$_GET['param2']}.keywords"]; elseif(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.keywords"])) echo $meta["{$_GET['param2']}.keywords"]; elseif(isset($_GET['param1']) && isset($meta["{$_GET['param1']}.keywords"])) echo $meta["{$_GET['param1']}.keywords"]; ?><?=Meta::getMetaFromPageParam("keywords"); ?><?=Meta::getMetaFromPage("keywords"); ?>">
<meta name="description" content="<?php startblock('description') ?><?php endblock() ?><?php if(isset($_GET['param3']) && isset($meta["{$_GET['param3']}.description"])) echo $meta["{$_GET['param3']}.description"]; elseif(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.description"])) echo $meta["{$_GET['param2']}.description"]; elseif(isset($_GET['param1']) && isset($meta["{$_GET['param1']}.description"])) echo $meta["{$_GET['param1']}.description"]; ?><?=Meta::getMetaFromPageParam("description"); ?><?=Meta::getMetaFromPage("description"); ?>">
<meta property="og:title" content="<?php startblock('og:title') ?><?php endblock() ?><?php if(isset($_GET['param3']) && isset($meta["{$_GET['param3']}.title"])) echo $meta["{$_GET['param3']}.title"]." | "; ?><?php if(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.title"])) echo $meta["{$_GET['param2']}.title"]." | "; ?><?php if(isset($_GET['param1']) && isset($meta["{$_GET['param1']}.title"])) echo $meta["{$_GET['param1']}.title"]." | "; ?><?=Meta::getMetaFromPageParam("title"); ?><?=Meta::getMetaFromPage("title"); ?><?=$meta["site.title"]?>" />
<meta property="og:description" content="<?php startblock('og:description') ?><?php endblock() ?><?php if(isset($_GET['param3']) && isset($meta["{$_GET['param3']}.description"])) echo $meta["{$_GET['param3']}.description"]; elseif(isset($_GET['param2']) && isset($meta["{$_GET['param2']}.description"])) echo $meta["{$_GET['param2']}.description"]; elseif(isset($_GET['param1']) && isset($meta["{$_GET['param1']}.description"])) echo $meta["{$_GET['param1']}.description"]; ?><?=Meta::getMetaFromPageParam("description"); ?><?=Meta::getMetaFromPage("description"); ?>" />
<meta property="og:type" content="<?php startblock('og:type') ?>website<?php endblock() ?>" />
<meta property="og:image" content="<?php startblock('og:image') ?><?php endblock() ?>" />
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/header.php')) ?>
<?php startblock('extended-styles') ?><?php endblock() ?>
</head>
<body>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/analyticstracking.php')); ?>
<div id="l-wrap">
  <?php startblock('header') ?>
  <header>
    <section>
      <a href="<?=URL_ROOT.$lang2_trans.Translate::translateFromPage()?>"><?=$global["lang"]?></a>
      <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/nav.php')); ?>
      <div class="l-grid">
        <div class="l-grid-100">
          <?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/slider.php')); ?>
        </div>
      </div>
    </section>
  </header>
  <?php endblock() ?>
  <div class="wrapper">
    <section>
      <div class="l-grid l-row-2">
        <div class="l-grid-65">
          <?php startblock('content') ?>
          <?php endblock() ?>
        </div>
        <div class="l-grid-35">
          <?php startblock('sidebar') ?>
          <?php endblock() ?>
        </div>
      </div>
    </section>
  </div>
</div>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/footer.php')); ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/scripts.php')); ?>
<?php startblock('extended-scripts') ?>
<?php endblock() ?>
</body>
</html>