<?php
	$host											= (isset($_GET['serial']) && 1 == $_GET['serial'] ? 'https://playdrv.akubebas.com/tv/?' : 'https://playdrv.akubebas.com/mv/?');
	$trailer_host									= 'https://playmv.akubebas.com/?';
	$origin											= 'http://103.194.171.75/';
	$referer										= $origin . (isset($_GET['serial']) && 1 == $_GET['serial'] ? 'film-seri' : 'movie') . '/';
	$params											= array
	(
		'dv'										=> '1ledJsEj7Kxv_2wsJLYNFzYPb_3lWU5cL/',
		'ts'										=> 1557701821,
		'token'										=> 5907036960,
		'hs'										=> 1, //hardsub
		'epi'										=> (isset($_GET['serial']) && 1 == $_GET['serial'] ? 1 : 0)
	);
	
	$trailer_params									= array
	(
		'token'										=> '15V58Z279FT362',
		't'											=> 1558279302,
		'k'											=> 86656752,
		'v'											=> 'static8.js'
	);
	
	$output											= array();
	$source											= array();
	$title											= '';
	$description									= '';
	$image											= '';
	
	if(isset($_GET['slug']))
	{
		/**
		 * Get the movie properties
		 */
		$page										= file_get_contents($referer . $_GET['slug'] . '/play' . (isset($_GET['serial']) && 1 == $_GET['serial'] ? 'tv' : ''));
		preg_match('/<meta property="og:title" content="(.*?)"/', $page, $title);
		preg_match('/<meta property="og:description" content="(.*?)"/', $page, $description);
		preg_match('/<meta property="og:image" content="(.*?)"/', $page, $poster);
		preg_match('/<input class="movie-title" style=".*?" id="linkSubs"\s[^>]*value="(.*?)"/', $page, $subtitle);
		
		
		$ch											= curl_init();
		curl_setopt_array
		(
			$ch,
			array
			(
				CURLOPT_RETURNTRANSFER				=> 1,
				CURLOPT_URL							=> $host . http_build_query($params),
				CURLOPT_HTTPHEADER					=> array
				(
					'Referer: ' . $referer . $_GET['slug']
				)
			)
		);
		$output										= json_decode(curl_exec($ch), true);
		curl_close($ch);
		
		if(!$output)
		{
			$params['hs']							= 0;
			$ch										= curl_init();
			curl_setopt_array
			(
				$ch,
				array
				(
					CURLOPT_RETURNTRANSFER			=> 1,
					CURLOPT_URL						=> $host . http_build_query($params),
					CURLOPT_HTTPHEADER				=> array
					(
						'Referer: ' . $referer . $_GET['slug']
					)
				)
			);
			$output									= json_decode(curl_exec($ch), true);
			curl_close($ch);
		}
		
		if(!$output)
		{
			$ch										= curl_init();
			curl_setopt_array
			(
				$ch,
				array
				(
					CURLOPT_RETURNTRANSFER			=> 1,
					CURLOPT_URL						=> $trailer_host . http_build_query($trailer_params),
					CURLOPT_HTTPHEADER				=> array
					(
						'Referer: ' . $referer . $_GET['slug'],
						'Origin: ' . $origin
					)
				)
			);
			$output									= curl_exec($ch);
			curl_close($ch);
			
			if($output)
			{
				require_once('includes/youtube.php');
				$handler							= new YouTubeDownloader();
				$youtubeURL							= 'https://www.youtube.com/watch?v=' . str_replace('trailer:', '', $output);
				$downloader							= $handler->getDownloader($youtubeURL);
				$downloader->setUrl($youtubeURL);
				if($downloader->hasVideo())
				{
					$source							= $downloader->getVideoDownloadLink();
				}
			}
		}
		
		if(isset($output[0]['sources']))
		{
			foreach($output[0]['sources'] as $key => $val)
			{
				$source[]							= $val['file'];
			}
		}
		elseif(isset($output[0]['file']))
		{
			$source[]								= $output[0]['file'];
		}
	}
	
	$output											= array
	(
		'status'									=> ($source ? 200 : 404),
		'query_string'								=> '?slug=' . (isset($_GET['slug']) ? $_GET['slug'] : '') . (isset($_GET['serial']) && 1 == $_GET['serial'] ? '&s=1' : ''),
		'title'										=> (isset($title[1]) ? str_ireplace('Nonton ', null, $title[1]) : ''),
		'description'								=> (isset($description[1]) ? $description[1] : ''),
		'poster'									=> (isset($poster[1]) ? $poster[1] : ''),
		'subtitle'									=> (isset($subtitle[1]) ? str_replace('https://subscene.com/subtitles/', null, $subtitle[1]) : ''),
		'slug'										=> (isset($_GET['slug']) ? $_GET['slug'] : ''),
		'source'									=> $source
	);
	
	/**
	 * Send output
	 */
	header('Content-Type: application/json');
	echo json_encode($output);
