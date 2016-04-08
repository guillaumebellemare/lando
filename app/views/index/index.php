<?php include('app/views/layouts/two-columns.php') ?>
<?php startblock('content') ?>
<h1><?=$navigation["index"]?></h1>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum, suscipit quis ad harum quisquam vitae ipsa perferendis amet magni eum placeat ex laudantium voluptas qui voluptatibus non ratione. Exercitationem, odit?</p>
<p>Quae, error, sequi ea a quo voluptatum at necessitatibus alias veritatis natus voluptate nihil distinctio deleniti doloremque dicta ducimus facilis porro placeat aut officia beatae culpa sapiente doloribus et itaque.</p>
<p>Eius, in qui necessitatibus explicabo unde. <a href="#">Aliquam reprehenderit</a> sapiente porro molestias in. Eos, aut, nihil temporibus velit aperiam incidunt odit doloremque in odio tenetur consequuntur corporis magni perspiciatis qui nostrum.</p>
<p>Blanditiis, natus, debitis, optio culpa voluptatibus nesciunt aperiam obcaecati libero laudantium minus quam velit facilis quae adipisci qui repudiandae veritatis. Ad, quia recusandae vero magni nesciunt? Quas quibusdam veritatis eveniet!</p>
<p>Molestiae, vero, voluptas incidunt dicta reprehenderit porro dolores cum. Animi, vero, suscipit eos id tempora hic voluptatem consectetur quisquam ut nam sint illum a doloribus quasi vitae dolorem ullam reiciendis?</p>
<ul>
	<li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias, ipsum voluptates repellendus ut magnam eos illo doloremque veritatis voluptatibus commodi consectetur unde minima quaerat obcaecati quibusdam mollitia labore error ullam.</li>
	<li>Praesentium, repellat, minus, nobis consequatur vitae voluptas natus neque laboriosam asperiores excepturi dignissimos cum iste quo. Pariatur, id, enim, perferendis, recusandae magni deleniti iusto sed doloremque quis quod minus autem!</li>
	<li>Repellat, aspernatur, sed veniam aliquid omnis atque in voluptate incidunt harum vero consequuntur obcaecati commodi ab deserunt dicta neque voluptatibus alias excepturi autem nostrum vel nobis beatae natus. Quis, repudiandae.</li>
	<li>Deleniti, fugit, nobis, molestias, minima dignissimos a pariatur culpa numquam cum dolorem laudantium nihil laboriosam expedita vitae distinctio temporibus vero quae odit id repellendus accusantium voluptates doloremque officia quis ab!</li>
</ul>
<?php endblock() ?>
<?php startblock('sidebar') ?>
<h1>Sidebar</h1>
<?php endblock() ?>
<?php startblock('extended-scripts') ?>
<?php if(@include(COMPLETE_URL_ROOT . PUBLIC_FOLDER . 'includes/flexslider.php')); ?>
<?php endblock() ?>
