<?php
	$subtitle									= null;
	if(isset($_GET['slug']))
	{
		$caption								= '../subtitles/' . sha1($_GET['slug']) . '.vtt';
		if(!file_exists($caption))
		{
			require_once __DIR__.'/includes/subscene.php';
			
			$uri								= 'https://subscene.com/subtitles/' . $_GET['slug'];
			$folder								= '../subtitles/' . sha1($_GET['slug']);
			$filename							= '../subtitles/' . sha1($_GET['slug']) . '.zip';
			
			$subtitles							= SubScene::getSubtitles($uri);
			if(isset($subtitles[0]['url']))
			{
				$download						= Subscene::getDownloadUrl($subtitles[0]['url']);
			
				$ch								= curl_init();
				curl_setopt_array
				(
					$ch,
					array
					(
						CURLOPT_RETURNTRANSFER	=> 1,
						CURLOPT_URL				=> $download
					)
				);
				$data							= curl_exec($ch);
				curl_close($ch);
				
				$file							= @fopen($filename, "w+");
				fputs($file, $data);
				fclose($file);
				
				$zip							= new ZipArchive;
				$res							= $zip->open($filename);
				if($res === TRUE)
				{
					$zip->extractTo($folder);
					$zip->close();
					@unlink($filename);
					
					$files						= preg_grep('~\.(srt)$~', scandir($folder));
					if(is_array($files) && sizeof($files) > 0)
					{
						$subtitle				= @file_get_contents($folder . '/' . array_values($files)[0]);
						$subtitle				= preg_replace('/(<font[^>]*>)|(<\/font>)/', '', $subtitle);
						$handle					= @fopen($caption, 'w');
						fwrite($handle, "WEBVTT\n\n" . $subtitle);
					}
					
					array_map('unlink', glob($folder . '/*.*'));
					rmdir($folder);
				}
			}
		}
		
		$subtitle								= @file_get_contents($caption);
	}
	
	/**
	 * Send output
	 */
	header('Content-Type: text/vtt');
	echo $subtitle;