<?php

require_once(dirname(__FILE__) . '/utils.php');

function GetMovieInfoDouban($movie_data, $data)
{
    /**
    参考 https://developers.douban.com/wiki/?title=movie_v2#subject
    */
	$data['title']				 	= $movie_data->title;
	$data['original_title']			= $movie_data->original_title;
	$data['tagline'] 				= implode(',', $movie_data->aka);
	$data['original_available'] 	= $movie_data->original_available; // add-on
	$data['summary'] 				= $movie_data->summary;
	
	//extra
	$data['extra'] = array();
	$data['extra'][PLUGINID] = array('reference' => array());
	$data['extra'][PLUGINID]['reference']['themoviedb'] = $movie_data->id;
	$data['doubandb'] = true;
	
	if (isset($movie_data->imdb)) {
		 $data['extra'][PLUGINID]['reference']['imdb'] = $movie_data->imdb; // add-on
	}
	if ((float)$movie_data->rating) {
		$data['extra'][PLUGINID]['rating'] = array('themoviedb' => $movie_data->rating->average);
	}
	if (isset($movie_data->images)) {
		 $data['extra'][PLUGINID]['poster'] = array($movie_data->images->large);
	}
	if (isset($movie_data->backdrop)) {
		 $data['extra'][PLUGINID]['backdrop'] = array($movie_data->backdrop); // add-on
	}
	if (isset($movie_data->belongs_to_collection)) {
		 $data['extra'][PLUGINID]['collection_id'] = array('themoviedb' => $movie_data->belongs_to_collection->id);
	}
	
	// genre
	if( isset($movie_data->genres) ){ // add-on
		foreach ($movie_data->genres as $item) {
			if (!in_array($item, $data['genre'])) {
				array_push($data['genre'], $item);
			}
		}
	}
	// actor
	if( isset($movie_data->casts) ){ // add-on
		foreach ($movie_data->casts as $item) {
			if (!in_array($item, $data['actor'])) {
				array_push($data['actor'], $item);
			}
		}
	}
	
	// director
	if( isset($movie_data->directors) ){
		foreach ($movie_data->directors as $item) {
			if (!in_array($item->name, $data['director'])) {
				array_push($data['director'], $item->name);
			}
		}
	}
	
	// writer
	if( isset($movie_data->writers) ){ // add-on
		foreach ($movie_data->writers as $item) {
			if (!in_array($item, $data['writer'])) {
				array_push($data['writer'], $item);
			}
		}
	}
	//error_log(print_r( $movie_data, true), 3, "/var/packages/VideoStation/target/plugins/syno_themoviedb/my-errors.log");
	//error_log(print_r( $data, true), 3, "/var/packages/VideoStation/target/plugins/syno_themoviedb/my-errors.log");
    return $data;
}

function GetMetadataDouban($query_data)
{
	global $DATA_TEMPLATE;

	//Foreach query result
	$result = array();

	foreach($query_data as $item) {
        //Filter the content
        if($item['subtype'] != 'movie') {
          continue;
        }
        //Copy template
		$data = $DATA_TEMPLATE;
		
		//Get movie
        $movie_data = getDoubanMovieData($item['id']);

		if (!$movie_data) {
			continue;
		}
		$data = GetMovieInfoDouban($movie_data, $data);
		
		//Append to result
		$result[] = $data;
	}

	return $result;
}

function ProcessDouban($input, $lang, $type, $limit, $search_properties, $allowguess, $id)
{
	$title 	= $input['title'];
	if (!$lang) {
		return array();
	}
    
    if (0 < $id) {
		// if haved id, output metadata directly.
		return GetMetadataDouban(array(array('id' => $id)));
	}
    
	//Search
	$query_data = array();
	$query_data = getDoubanRawData($title, $limit);

	//Get metadata
	return GetMetadataDouban($query_data['subjects']);
}

?>
