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
			// === LOCAL HTML FILES ============================================
			// $filedir = 'site-pages';
			// $files = scandir($filedir);
			// foreach ($files as $key=>$file) {
			// 	if (substr($file, -5) === '.html') {
			// 		echo '<div class="file" id="http://localhost/devon/labs/crawler/' . $filedir . '/' . rawurlencode($file) . '"></div>';
			// 	}
			// }

			// === EXTERNAL URLS ===============================================
			$contents = file('site-urls/to-crawl.txt');
			foreach($contents as $line) {
				echo '<div class="file" id="' . $line . '"></div>';
			}
		?>
	</div>
</html>