<?php include('app/views/layouts/two-columns.php') ?>
<?php startblock('content') ?>
<section>
  <div class="l-grid l-row-1">
    <div class="l-grid-100">
      <h1>ADD</h1>
      <?php if(isset($activities)) var_dump($activities); ?>
      <?php if(@include('includes/messages.inc.php')); ?>
        <form action="<?php echo URL_ROOT.$lang2; ?>/index/add.html" method="post" name="fAdd">
        	<input name="name_fre" type="text">
            <textarea name="description_fre"></textarea>
            <select name="catact_id">
            	<option value="1">Test</option>
             	<option value="3">Test 2</option>
            	<option value="3">Test 3</option>
           </select>
           <input name="action" type="hidden" value="save">
           <input name="btSubmit" type="submit" value="Ajouter">
        </form>
    </div>
  </div>
</section>
<?php endblock() ?>
<?php startblock('sidebar') ?>
Sidebar
<?php endblock() ?>
