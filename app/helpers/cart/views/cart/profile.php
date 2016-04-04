<?php include('app/views/layouts/one-column.php') ?>
<?php startblock('content') ?>
<h1><?=$cart["profile"]?></h1>
<h2><?=$_SESSION['customer']['complete_name']?></h2>
<?php $current_order = NULL; ?>
<?php foreach($orders as $order) : ?>
<?php if($current_order) : ?></table><?php endif; ?>
<h3><?=$order["cartorders.invoice_no"]?></h3>
<table class="shopping-cart">
    <tr>
        <th><?=$cart["product"]?></th>
        <th><?=$cart["price"]?></th>
        <th><?=$cart["qty"]?></th>
        <th></th>
        <th>Total</th>
    </tr>
	<?php foreach($order["products"] as $products) : ?>
    <tr>
    	<td><a href="<?=URL_ROOT.$lang2."/".$routes["product"]."/".$products["products.slug_$lang3"]?>"><?=$products["products.name_$lang3"]?></a></td>
        <td><?=$products["cartorder_products.unit_cost"]?> $</td>
        <td><?=$products["cartorder_products.qty"]?></td>
        <td></td>
        <td><?=number_format($products["cartorder_products.unit_cost"]*$products["cartorder_products.qty"], 2)?> $</td>
	</tr>
    <?php endforeach; ?>
    <?php if($order["cartorders.shipping_fees"]) : ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td><?=$cart["delivery_cost"]?></td>
        <td><?=number_format($order["cartorders.shipping_fees"], 2)?> $</td>
    </tr>
    <?php endif; ?>
    <?php if($order["cartorders.tps"]) : ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>TPS</td>
        <td><?=number_format($order["cartorders.tps"], 2)?> $</td>
    </tr>
    <?php endif; ?>
    <?php if($order["cartorders.tvq"]) : ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>TVQ</td>
        <td><?=number_format($order["cartorders.tvq"], 2)?> $</td>
   </tr>
   <?php endif; ?>
   <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Total</td>
        <td><?=number_format($order["cartorders.total"], 2)?> $</td>
    </tr>
    <?php $current_order = $order["cartorders.id"]; ?>
<?php endforeach; ?>
</table>
<?php endblock() ?>
