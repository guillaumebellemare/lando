<?php include('app/views/layouts/two-columns.php') ?>
<?php startblock('content') ?>
<section>
  <div class="l-grid l-row-1">
    <div class="l-grid-100">
      <h1><?=$global["title"]?></h1>
      <?php if(@include('includes/messages.inc.php')); ?>
      <a href="<?=URL_ROOT.$lang2;?>/manage/form.html">Ajouter</a>
      	<?php $currentCat = ""; ?>
		<?php foreach($activities AS $activity) : ?>
            <?php if(isset($currentCat) && $currentCat!=$activity['catacts.catacts_name_fre']) : ?>
                <hr>
                <h1><?=$activity['catacts.catacts_name_fre']?></h1>
            <?php endif; ?>
            
            <?php $currentCat = $activity['catacts.catacts_name_fre']; ?>
            
            <?php if($activity["catacts.catacts_id"]==$activity["activities.catact_id"]) : ?>
                <h2><?=$activity["activities.name_$lang3"]?></h2>
                <?=$activity["activities.description_fre"]?> <?=$activity["activities.test"]?>
                <p>Slug: <a href="<?=URL_ROOT.$lang2?>/index/<?=$activity["activities.slug_$lang3"]?>"><?=$activity["activities.slug_$lang3"]?></a></p>
                <a href="<?php echo URL_ROOT.$lang2; ?>/manage/remove/<?=$activity["activities.id"]?>" onclick="return confirm('Voulez-vous vraiment supprimer <?=$activity["activities.name_$lang3"]?> ?')">[Supprimer]</a>
            <?php endif;
        endforeach; ?>
    </div>
  </div>
</section>
<?php endblock() ?>
<?php startblock('sidebar') ?>
Sidebar
<?php endblock() ?>
