<!DOCTYPE html>
<html>
	<head>
		<title>
			NoAd! Streaming
		</title>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
		<link rel="stylesheet" href="assets/css/bootstrap.min.css?<?php echo time(); ?>" />
		<link rel="stylesheet" href="assets/js/mediaelement/mediaelement.min.css?<?php echo time(); ?>" />
		<link rel="stylesheet" href="assets/css/style.min.css?<?php echo time(); ?>" />
		<link rel="icon" type="image/x-icon" href="favicon.ico?<?php echo time(); ?>" />
	</head>
	<body class="bg-dark">
	
		<!-- top bar -->
		<nav class="navbar navbar-dark bg-dark">
			<div class="navbar-nav">
				<form action="" method="GET" class="ajax-form">
					<input type="text" name="q" value="<?php echo (isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''); ?>" class="form-control form-control-lg font-weight-bold border-0 focusable keyword" placeholder="Search the movie title" autocomplete="off" />
				</form>
			</div>
			<a class="navbar-brand d-none d-md-block focusable" href="/">
				<img src="assets/img/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
				NoAd! Streaming
			</a>
		</nav>
		
		<div class="container-fluid pt-4 pb-4 content-wrapper">
			<div class="row listing-placeholder">
				<!-- listing placeholder -->
			</div>
		</div>
		
		<script type="text/javascript" src="assets/js/jquery.min.js?<?php echo time(); ?>"></script>
		<script type="text/javascript" src="assets/js/visible.min.js?<?php echo time(); ?>"></script>
		<script type="text/javascript" src="assets/js/spatial-navigation.min.js?<?php echo time(); ?>"></script>
		<script type="text/javascript" src="assets/js/mediaelement/mediaelement.min.js?<?php echo time(); ?>"></script>
		<script type="text/javascript" src="assets/js/application.min.js?<?php echo time(); ?>"></script>
	</body>
</html>