<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="keywords" content="">
<meta name="description" content="">
<meta property="og:title" content="" />
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www..com/" />
<meta property="og:description" content="" />
<meta property="og:site_name" content="" />
<meta property="og:image" content="" />
<meta property="og:locale" content="<?php echo $lang2; ?>_CA" />
<title>Base Propaganda Design</title>
<?php include('includes/header.inc.php'); ?>
</head>
<body>
<?php include('includes/analyticstracking.inc.php'); ?>
<div id="l-wrap">
  <header>
    <section>
      <h1>Base Propaganda Design</h1>
      <?php include('includes/nav.inc.php'); ?>
      <div class="l-grid">
        <div class="l-grid-100">
          <div id="map_canvas"></div>
          <?php include('includes/slider.inc.php'); ?>
        </div>
      </div>
    </section>
  </header>
  <div class="wrapper">
    <section>
      <h1><?php echo $global['intro']; ?></h1>
      <div class="l-grid l-row-1">
        <div class="l-grid-100">
			l-grid-100
        </div>
      </div>
    </section>
    <section>
      <h1>2 colonnes</h1>
      <div class="l-grid l-row-2">
        <div class="l-grid-90">.l-grid-90</div>
        <div class="l-grid-10">.l-grid-10</div>
        <div class="l-grid-80">.l-grid-80</div>
        <div class="l-grid-20">.l-grid-20</div>
        <div class="l-grid-70">.l-grid-70</div>
        <div class="l-grid-30">.l-grid-30</div>
        <div class="l-grid-60">.l-grid-60</div>
        <div class="l-grid-40">.l-grid-40</div>
        <div class="l-grid-50">.l-grid-50</div>
        <div class="l-grid-50">.l-grid-50</div>
        <div class="l-grid-40">.l-grid-40</div>
        <div class="l-grid-50">.l-grid-50</div>
      </div>
    </section>
    <section>
      <h1>3 colonnes</h1>
      <div class="l-grid l-row-3">
        <div class="l-grid-80">.l-grid-80</div>
        <div class="l-grid-10">.l-grid-10</div>
        <div class="l-grid-10">.l-grid-10</div>
        <div class="l-grid-40">.l-grid-40</div>
        <div class="l-grid-20">.l-grid-20</div>
        <div class="l-grid-40">.l-grid-40</div>
      </div>
    </section>
    <section>
      <h1>4 colonnes</h1>
      <div class="l-grid l-row-4">
        <div class="l-grid-50">.l-grid-50</div>
        <div class="l-grid-20">.l-grid-20</div>
        <div class="l-grid-20">.l-grid-20</div>
        <div class="l-grid-10">.l-grid-10</div>
        <div class="l-grid-40">.l-grid-40</div>
        <div class="l-grid-20">.l-grid-20</div>
        <div class="l-grid-20">.l-grid-20</div>
        <div class="l-grid-20">.l-grid-20</div>
      </div>
    </section>
    <section>
      <h1>5 colonnes</h1>
      <div class="l-grid l-row-5">
      <div class="l-grid-20">.l-grid-20</div>
      <div class="l-grid-20">.l-grid-20</div>
      <div class="l-grid-20">.l-grid-20</div>
      <div class="l-grid-20">.l-grid-20</div>
      <div class="l-grid-20">.l-grid-20</div>
      <div class="l-grid-40">.l-grid-20</div>
      <div class="l-grid-10">.l-grid-20</div>
      <div class="l-grid-10">.l-grid-20</div>
      <div class="l-grid-10">.l-grid-10</div>
      <div class="l-grid-30">.l-grid-30</div>
    </section>
  </div>
</div>
<?php include('includes/footer.inc.php'); ?>
<?php include('includes/scripts.inc.php'); ?>
<script src="http://maps.googleapis.com/maps/api/js?sensor=true&amp;language=fr" type="text/javascript"></script> 
<script src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js" type="text/javascript"></script> 
<?php echo $compressor->load('js', array('?base='.URL_ROOT => 'scripts/jquery.google.map.js'), null, false); ?> 
<script type="text/javascript">
	var locations = new Array();
	locations[0] = ["-1","", "Propaganda Design", "1433 4e avenue, Québec","","","G1L 2L1", "Québec", "Québec", "CA", false];
</script> 
<script>initializeMap();</script>
</body>
</html>