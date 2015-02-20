<?php

//set_time_limit (0);
function crawl_page($url, $depth = 5){
	
	$seen = array();
	if(($depth == 0) or (in_array($url, $seen))){
		return;
	}   
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$result = curl_exec ($ch);
	curl_close ($ch);
	
	if( $result ){
		$stripped_file = strip_tags($result, "<a>");
		preg_match_all("/<a[\s]+[^>]*?href[\s]?=[\s\"\']+"."(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/", $stripped_file, $matches, PREG_SET_ORDER ); 
		foreach($matches as $match){
			$href = $match[1];
				if (0 !== strpos($href, 'http')) {
					$path = '/' . ltrim($href, '/');
					if (extension_loaded('http')) {
						$href = http_build_url($href , array('path' => $path));
					} else {
						$parts = parse_url($href);
						$href = $parts['scheme'] . '://';
						if (isset($parts['user']) && isset($parts['pass'])) {
							$href .= $parts['user'] . ':' . $parts['pass'] . '@';
						}
						$href .= $parts['host'];
						if (isset($parts['port'])) {
							$href .= ':' . $parts['port'];
						}
						$href .= $path;
					}
				}
				crawl_page($href, $depth - 1);
			}
	}   
	
	echo "Crawled {$href}";
}   
crawl_page("http://www.espacedev2.com/windu/fr",5);
