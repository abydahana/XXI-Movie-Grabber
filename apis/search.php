<?php
	$host						= 'https://indoxxi.cx';
	
	/* get the whole content */
	$content					= (isset($_GET['q']) && !empty($_GET['q']) ? file_get_contents($host . '/s/' . urlencode((isset($_GET['q']) ? $_GET['q'] : 'a')) . (isset($_GET['p']) ? '/' . $_GET['p'] : null)) : file_get_contents($host));
	
	/* get the movie link */
	preg_match_all('/<div data-movie-id=\'.*?\' class=\'ml-item\'>(.*?)<\/div>/', $content, $contents);
	
	/* get the pagination */
	preg_match('/<li class="next"><a href=".*?" data-ci-pagination-page="(.*?)" rel="next"/', $content, $next_page);
	
	$output						= array();
	
	if(isset($contents[1]))
	{
		foreach($contents[1] as $key => $val)
		{
			$val				= str_replace('\'', '"', $val);
			
			/* grep property */
			preg_match('/<a href="(.*?)" class="ml-mask jt" title=".*?"/', $val, $link);
			preg_match('/<a href=".*?" class="ml-mask jt" title="(.*?)"/', $val, $title);
			preg_match('/<img data-original="(.*?)" class="lazy thumb mli-thumb"/', $val, $poster);
			preg_match('/<span class="mli-rating"><i class="fa fa-star mr5"><\/i>(.*?)<\/span>/', $val, $rating);
			preg_match('/<span class="mli-durasi"><i class="fa fa-clock-o mr5"><\/i>(.*?)<\/span>/', $val, $duration);
			preg_match('/<span class="mli-quality trailer">(.*?)<\/span>/', $val, $trailer);
			
			if(!isset($link[1]) || isset($link[1]) && empty($link[1])) continue;
			
			if(strpos($link[1], '/film-seri/') !== false)
			{
				$serial			= true;
			}
			else
			{
				$serial			= false;
			}
			
			$link				= str_replace('/movie/', '', str_replace('/film-seri/', '', $link[1]));
			
			$output[]			= array
			(
				'title'			=> (isset($title[1]) ? $title[1] : 'No Title!'),
				'poster'		=> (isset($poster[1]) ? $poster[1] : ''),
				'slug'			=> $link,
				'serial'		=> (int) $serial,
				'rating'		=> (isset($rating[1]) ? $rating[1] : ''),
				'duration'		=> (isset($duration[1]) ? $duration[1] . 'm' : ''),
				'trailer'		=> (isset($trailer[1]) ? true : false)
			);
		}
	}
	$output						= array
	(
		'status'				=> ($output ? 200 : 404),
		'title'					=> (isset($_GET['q']) ? htmlspecialchars(urldecode($_GET['q'])) : ''),
		'query_string'			=> '?q=' . (isset($_GET['q']) ? $_GET['q'] : '') . (isset($_GET['p']) ? '&p=' . $_GET['p'] : ''),
		'results'				=> $output,
		'next_page'				=> (isset($next_page[1]) ? $next_page[1] : 0)
	);
	
	
	/**
	 * Send output
	 */
	header('Content-Type: application/json');
	echo json_encode($output);