<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<?=$paypal_info; ?>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<script type="text/javascript">
	$(".fPayment").submit();
</script>
<?php endblock() ?>