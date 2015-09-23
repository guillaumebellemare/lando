<meta charset="utf-8">
<title><?=$app_controller->getMeta("title")?></title>
<meta name="keywords" content="<?=$app_controller->getMeta("keywords")?>">
<meta name="description" content="<?=$app_controller->getMeta("description")?>">
<meta property="og:title" content="<?=$app_controller->getMeta("title")?>" />
<meta property="og:description" content="<?=$app_controller->getMeta("description")?>" />
<meta property="og:image" content="<?=$app_controller->getMeta("image")?>" />
<meta property="og:type" content="<?=$app_controller->getMeta("type")?>" />
<meta property="og:url" content="http://<?=$_SERVER[HTTP_HOST] . URL_ROOT . $lang2; ?><?php if(isset($_GET['page']) && $_GET['page']!='index') echo '/'.$_GET['page']; ?><?php if(isset($_GET['param1'])) echo '/'.$_GET['param1']; ?><?php if(isset($_GET['param2'])) echo '/'.$_GET['param2']; ?><?php if(isset($_GET['param3'])) echo '/'.$_GET['param3']; ?><?php if(isset($_GET['param4'])) echo '/'.$_GET['param4']; ?>" />
<meta property="og:site_name" content="<?php if(isset($meta["site.title"])) echo $meta["site.title"]?>" />
<meta property="og:locale" content="<?php echo $lang2; ?>_CA" />
<meta property="og:image:type" content="image/jpg">
<meta property="og:image:width" content="1500">
<meta property="og:image:height" content="1500">
