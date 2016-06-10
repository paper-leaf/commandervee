##Instructions for crawler.js / crawler.php:
	NOTE: These instructions assume that you have a folder on your localhost
	containing all of the *.html files you wish to scrape.


	2. Point line 11 of ./crawler.php to the folder containing all the *.html
		files.
	3. Open ./crawler.php in your browser and let the script run.
	4. Remove the trailing comma and manually add wrapper curly braces to
		json.txt.
	5. Enjoy your JSONified site. May be used in conjunction with 
		./custom-post-importer.php


###Crawler Modifications:
	To tweak this file to your specific needs, modify the extract*() functions
	to select the necessary elements.


	In order to add fields, additional logic would also be required to 
	./custom-post-importer.




##Instructions for custom-post-importer.php:
	NOTE: These instructions assume that you have files containing the JSON 
	produced by ./crawler.js loaded on your localhost at some address.


	1. Place this file in the base folder of your WP theme.
	2. Modify the 'Load up the JSON' section (line 11) to point at your JSON 
		files. Note that each post category requires a separate JSON file.
	3. Modify the calls to importPages() on line 82 to match the code you
		tweaked on step 2. Additionally, provide the category IDs you wish to
		associate with the posts from each file.
	4. Ensure your Wordpress install contains categories that match the IDs
		you provided in step 3.
	5. Run this file using Chrome.
	6. Verify that the file ran without PHP errors, and then check out your
		newly imported posts!
