<html>
	<head>
		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script src="crawler.js"></script>
	</head>
	<body>
		<div id="imported-content"></div>
	</body>
	<div id="file-collection">
		<?php
			$filedir = 'site-pages/in-depth';
			$files = scandir($filedir);
			foreach ($files as $key=>$file) {
				if (substr($file, -5) === '.html') {
					echo '<div class="file" id="http://localhost/devon/labs/crawler/' . $filedir . '/' . rawurlencode($file) . '"></div>';
				}
			}
		?>
	</div>
</html>