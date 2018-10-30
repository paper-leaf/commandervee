<?php

	$my_JSON = $_POST['json'];

	$fp = fopen('results.json', 'a'); // Open file in append mode

	if (flock($fp, LOCK_EX)) { // Wait to get file lock
		ob_start();
		print($my_JSON);
		$var = ob_get_contents();
		ob_end_clean();

		fwrite($fp, $var); // Write to file

		flock($fp, LOCK_UN); // unlock file
	}

	fclose($fp); // close file
