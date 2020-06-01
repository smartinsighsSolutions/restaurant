<!doctype html>
<html>
	<head>
		<title>Restaurant Menu Maker by PHPJabbers.com</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].$css['file'].'" />';
		}
		
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].$js['file'].'"></script>';
		}
		?>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="logo">
					<a href="https://www.phpjabbers.com/restaurant-menu-maker/" target="_blank">Restaurant Menu Maker</a>
					<span>v<?php echo PJ_SCRIPT_VERSION;?></span>
				</div>
			</div>
			<div id="middle">
				<div id="login-content">
				<?php require $content_tpl; ?>
				</div>
			</div> <!-- middle -->
		</div> <!-- container -->
		<div id="footer-wrap">
			<div id="footer">
			   	<p><a href="https://www.phpjabbers.com/" target="_blank">PHP Scripts</a> Copyright &copy; <?php echo date("Y"); ?> <a href="https://www.stivasoft.com" target="_blank">StivaSoft Ltd</a></p>
	        </div>
        </div>
	</body>
</html>