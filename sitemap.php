<?php header('Content-Type: application/xml; charset=utf-8'); ?>
<?php require_once("lang/sitemap.php"); ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <url>
    <loc>http://<?=$sitemap["site.url"]?>/</loc>
    <changefreq>monthly</changefreq>
    <priority>1.00</priority>
  </url>
  <?php
  
  //=> A remplacer par $app->addToSitemap("activities");
    require_once("includes/conn.inc.php");
	
	$q = "SELECT activities.id AS activity_id, activities.slug_fre AS activity_slug FROM activities";
	$q .= " WHERE activities.active = 1";
	$rsList = $db->Execute($q);
  ?>
  <?php while(!$rsList->EOF){ ?>
  <url>
    <loc>http://<?=$sitemap["site.url"]?>/fr/index/<?=$rsList->fields["activity_slug"]?>.html</loc>
    <changefreq>monthly</changefreq>
    <priority>1.00</priority>
  </url>
  <?php $rsList->MoveNext();
	    }
		$rsList->Close();
  ?>
</urlset>
