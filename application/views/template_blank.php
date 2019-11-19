<!DOCTYPE html>
<html lang="<?php echo substr(I18n::$lang, 0, 2); ?>">
	<meta charset="utf-8">
	<title><?= $title; ?></title>
	<meta name="description" content="<?= $description; ?>">
	<meta name="keywords" content="<?= $keywords; ?>">
	<meta name="author" content="<?= $author; ?>">
	<link rel="icon" href="<?= URL::base(); ?>media/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="<?= URL::base(); ?>media/favicon.ico" type="image/x-icon">
	<head>
	<? foreach ($styles as $style) : ?>
		<link rel="stylesheet" href="<?= URL::base(); ?>media/css/<?= $style; ?>.css" />
	<? endforeach; ?>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-24410081-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
	
	<script src="https://calc.vuso.ua/partner/vusopart.js" type="text/javascript"></script>
	<link media="screen" type="text/css" href="https://calc.vuso.ua/partner/vusopart.css" rel="stylesheet">

	<title><?= $title; ?></title>
	</head>
	<body>
		<?= $content; ?>
		
		<? foreach ($scripts as $script) : ?>
			<script src="<?= URL::base(); ?>media/js/<?= $script; ?>.js" /></script>
		<? endforeach; ?>
	</body>

</html>
