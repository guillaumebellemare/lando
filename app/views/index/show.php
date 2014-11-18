<?php include('app/views/layouts/two-columns.php') ?>
<?php startblock('content') ?>
<section>
  <div class="l-grid l-row-1">
    <div class="l-grid-100">
      <h1>SHOW</h1>
		<?php 
            foreach($activities AS $activity) : ?>
           		<h1><?=$activity["category_name"]?></h1>
                <h2><?=$activity["activity_name"]?></h2>
                <?=$activity["activity_description"]?> <?=$activity["activity_test"]?>
                <p>Slug: <a href="<?=URL_ROOT.$lang2?>/index/<?=$activity["slug_$lang3"]?>"><?=$activity["slug_$lang3"]?></a></p>
        <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endblock() ?>
<?php startblock('sidebar') ?>
Sidebar
<?php endblock() ?>
