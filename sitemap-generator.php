<?
/*************************************************************

 Simple site crawler to create a search engine XML sitemap
 Version 0.2
 Free to use, without any warranty
 Written by Elmar Hanlhofer http://www.plop.at 01/Feb/2012

 ChangeLog:
 ----------
 Version 0.2 2013-01-16  

     * curl support - by Emanuel Ulses
     * write url, then scan url - by Elmar Hanlhofer

*************************************************************/

    $file = "sitemap.xml";		// output file
    $url = "http://www.espacedev2.com/";	// url to scan
	$url_specific = "lando/fr/";
    // ignore urls starting with
    $skip[0] = "http://www.espacedev2.com/zap"; 
    
    $extension = ".html";		// scan files with extension

    $freq = "daily";			// scan frequency
    $priority = "0.5";			// site priority
    

function Path ($p)
{
    $a = explode ("/", $p);
    $len = strlen ($a[count ($a) - 1]);
    return (substr ($p, 0, strlen ($p) - $len));
}

function GetUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function Scan($url, $url_specific)
{
    global $scanned, $pf, $extension, $skip, $freq, $priority;
    
    echo "scan url $url\n";

    array_push ($scanned, $url);
    $html = GetUrl ($url.$url_specific);
    $a1 = explode ("<a", $html);

    foreach ($a1 as $key => $val)
    {
	$parts = explode (">", $val);
	$a = $parts[0];
	
	$aparts = explode ("href=", $a);

	$hrefparts = explode (" ", $aparts[1]);
	$hrefparts2 = explode ("#", $hrefparts[0]);

	$href = str_replace ("\"", "", $hrefparts2[0]);
	if ((substr ($href, 0, 7) != "http://") && 
	   (substr ($href, 0, 8) != "https://") &&
	   (substr ($href, 0, 6) != "ftp://"))
	{
	    if ($href[0] == '/')
		$href = "$scanned[0]$href";
	    else
		$href = Path ($url) . $href;
	}
	
	if (substr ($href, 0, strlen ($scanned[0])) == $scanned[0])
	{
	    $ignore = false;
	    if (isset ($skip))
		foreach ($skip as $k => $v)
		    if (substr ($href, 0, strlen ($v)) == $v)
			$ignore = true;
	    
	    if ((!$ignore) &&
		(!in_array ($href, $scanned)) && 
		(strpos ($href, $extension) > 0)		
		)
	    {
			$href = str_replace("//", "/", $href);
		fwrite ($pf, "<url>\n  <loc>$href</loc>\n" .
			     "  <changefreq>$freq</changefreq>\n" .
			     "  <priority>$priority</priority>\n</url>\n");
		echo $href. "\n";
		Scan ($href);
	    }
	}
    }
}

						


    $pf = fopen ($file, "w");
    if (!$pf)
    {
	echo "cannot create $file\n";
	return;
    }

    fwrite ($pf,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset
      xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
      xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
      xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">
<!-- created with Plop PHP XML Sitemap Generator 0.2 www.plop.at -->

<url>
  <loc>$url/</loc>
  <changefreq>daily</changefreq>
</url>
");

    $scanned = array();
    Scan ($url, $url_specific);
    
    fwrite ($pf, "</urlset>\n");
    fclose ($pf);
?>