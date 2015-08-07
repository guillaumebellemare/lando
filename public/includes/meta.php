<meta charset="utf-8">
<meta property="og:url" content="http://<?php if(isset($meta["site.url"])) echo $meta["site.url"]; ?><?php echo '/'.$lang2; ?><?php if(isset($_GET['page']) && $_GET['page']!='index') echo '/'.$_GET['page']; ?><?php if(isset($_GET['param1'])) echo '/'.$_GET['param1']; ?><?php if(isset($_GET['param2'])) echo '/'.$_GET['param2']; ?><?php if(isset($_GET['param3'])) echo '/'.$_GET['param3']; ?><?php if(isset($_GET['param4'])) echo '/'.$_GET['param4']; ?>" />
<meta property="og:site_name" content="<?php if(isset($meta["site.title"])) echo $meta["site.title"]?>" />
<meta property="og:locale" content="<?php echo $lang2; ?>_CA" />
<meta property="og:image:type" content="image/jpg">
<meta property="og:image:width" content="1500">
<meta property="og:image:height" content="1500">
