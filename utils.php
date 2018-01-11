<?php

require_once(dirname(__FILE__) . '/../constant.php');

define('DOUBAN_PLUGINID', 'com.synology.TheMovieDb');
define('API_URL', 'https://api.douban.com/v2/movie/');
define('DEFAULT_EXPIRED_TIME', 86400);
define('DEFAULT_LONG_EXPIRED_TIME', 30*86400);

function getImdbID($input) {
    preg_match_all('/<([^\s\/]+)[^>]*imdb\.com[^>]*(rel|property)="nofollow"[^>]*>([^<]*?)<\/\1>/', $input, $matches);
    return implode("", $matches[3]);
}

function RegexByRel($rel, $input) {
    preg_match_all('/<([^\s\/]+)(?=[^>]*>)[^>]*(rel|property)="' . $rel . '"[^>]*>([^<]*?)<\/\1>/', $input, $matches);
    return $matches[3];
}

function getWriter($input) {
    preg_match_all('/<([^\s\/]+)(?=[^>]*>)[^>]*>[\s]*<([^\s\/]+)(?=[^>]*>)[^>]*>编剧<\/\2>[\s\S]*?<([^\s\/]+)(?=[^>]*>)[^>]*>([\s\S]*?)<\/\3><\/\1>/', $input, $target);
    $target = implode("", $target[4]);
    preg_match_all('/<([^\s\/]+)(?=[^>]*>)[^>]*>([\s\S]*?)<\/\1>/', $target, $matches);
    return $matches[2];
}

function getBackdrop($input) {
    preg_match_all('/<([^\s\/]+)(?=[^>]*>)[^>]*class="related-pic-bd[^>]*"[^>]*>[\s\S]*?\/photos\/photo\/(\d+)\/[\s\S]*?<\/\1>/', $input, $matches);
    return implode("", $matches[2]);
}

function getRegexDate($input) {
    if( is_array($input) ) {
        $input = implode(";", $input);
    }
    preg_match('/\d{4}-\d{2}-\d{2}/', $input, $matches);
    if(empty($matches)) {
      preg_match('/\d{4}-\d{2}/', $input, $matches);
    }
    if(empty($matches)) {
      preg_match('/\d{4}/', $input, $matches);
    }
    return $matches[0];
}

function getDoubanRawData($title, $limit = 20) {
    $title = urlencode($title);
    return json_decode( HTTPGETRequest( API_URL . "search?q={$title}&count={$limit}" ) , true);
}

function getDoubanMovieData($id) {
    $cache_path = GetPluginDataDirectory(PLUGINID) . "/{$id}/movieInfo.json";
    $url = API_URL . "subject/{$id}";
    $ret = DownloadMovieData($url, $cache_path);

    // add-on info
    $cache_path = GetPluginDataDirectory(PLUGINID) . "/{$id}/addon.json";
    $url = "https://movie.douban.com/subject/{$id}/";
    return DownloadAddOnInfo($url, $cache_path, $ret);
}

function getDataFromCache($cache_path) {
	$json = FALSE;

	//Whether cache file already exist or not
	if (file_exists($cache_path)) {
		$lastupdated = filemtime($cache_path);
		if (DEFAULT_EXPIRED_TIME >= (time() - $lastupdated)) {
			$json = json_decode(@file_get_contents($cache_path));
			if (NULL !== $json) {
				return $json;
			}
		}
    }
    
    return FALSE;
}

function refreshCache ($data, $cache_path) {
    //create dir
    $path_parts = pathinfo($cache_path);
    if (!file_exists($path_parts['dirname'])) {
        mkdir($path_parts['dirname']);
    }

    //write
    @file_put_contents($cache_path, json_encode($data));

    if (FALSE === $data || NULL === $data) {
        @unlink($cache_path);
    }
    return $data;
}

function DownloadMovieData($url, $cache_path) {
	$json = getDataFromCache($cache_path);

	//If we need refresh cache file, grab rawdata from url website
	if (FALSE === $json) {
        $json = json_decode(HTTPGETRequest($url));
        refreshCache($json, $cache_path);
    }

	return $json;
}

function DownloadAddOnInfo ($url, $cache_path, $ret) {
    $json = getDataFromCache($cache_path);

    //If we need refresh cache file, grab rawdata from url website
	if (FALSE === $json) {
        $html = HTTPGETRequest($url);
        $json = array();
        $json['original_available'] = getRegexDate(RegexByRel('v:initialReleaseDate', $html));
        $json['imdb'] = getImdbID($html);
        $json['backdrop'] = 'https://img3.doubanio.com/view/photo/photo/public/p' . getBackdrop($html) . '.jpg';
        $json['genres'] = RegexByRel('v:genre', $html);
        $json['casts'] = RegexByRel('v:starring', $html);
        $json['writers'] = getWriter($html);
        refreshCache($json, $cache_path);
    }

    foreach($json as $key => $val) {
        $ret->$key = $val;
    }

    return $ret;
}
