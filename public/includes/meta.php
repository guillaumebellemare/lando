<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="apple-mobile-web-app-title" content="<?php if(isset($meta["site.title"])) echo $meta["site.title"]?>">
<meta name="keywords" content="<?=$app_controller->getMeta("keywords")?>">
<meta name="description" content="<?=$app_controller->getMeta("description")?>">
<meta property="og:title" content="<?=$app_controller->getMeta("title")?>" />
<meta property="og:url" content="<?=$app_controller->getMetaURL()?>" />
<meta property="og:description" content="<?=$app_controller->getMeta("description")?>" />
<meta property="og:site_name" content="<?php if(isset($meta["site.title"])) echo $meta["site.title"]?>" />
<meta property="og:locale" content="<?=$lang2?>_CA" />
<meta property="og:type" content="<?=$app_controller->getPageType()?>" />
<meta property="og:image" content="<?=$app_controller->getImage()?>" />
<title><?=$app_controller->getMeta("title")?></title>
