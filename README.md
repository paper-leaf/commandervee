#README

## What is Command-Vee?

Command-Vee is the result of a two-day sprint to pull over 850 blog posts from an old HTML blog into a shiny new Wordpress one.


## What can I do with it?

Whatever you want (no, seriously). Command-Vee is not a polished plug-and-play script, but that doesn't mean you can't use it to inspire one that is!

##How do I do that?

So glad you asked.

###Getting set up
In our case, we needed to pull all of these blog posts off of a site with cross-domain security enabled. While it was a big site, it wasn't *big* big, so we pulled down a local copy of all the posts we needed using
```wget -i urls_to_dl.txt```

###Instructions for crawler.js / crawler.php:

The following assumes that you have a folder on your localhost containing all of the *.html files you wish to scrape.

1. Point line 11 of `./crawler.php` to the folder containing all the `*.html` files.
2. Open `./crawler.php` in your browser and let the script run.
3. Remove the trailing comma and manually add wrapper curly braces to `json.txt`.
4. Enjoy your JSONified site... Or keep going!


###Instructions for custom-post-importer.php:
The following assumes that you have files containing the JSON produced by `./crawler.js` loaded on your localhost at some address.

1. Place this file in the base folder of your Wordpress theme.
2. Modify the 'Load up the JSON' section (line 11) to point at your JSON files. Note that each post category requires a separate JSON file.
3. Modify the calls to `importPages()` on line 82 to match the code you tweaked on step 2. Additionally, provide the category IDs you wish to associate with the posts from each file.
4. Ensure your Wordpress install contains categories that match the IDs you provided in step 3.
5. Run this file (we used Chrome, but experiment away!).
6. Verify that the file ran without PHP errors, and then check out your newly imported posts!

###Crawler Modifications:
To tweak this file to your specific needs, modify the `extract<thing_to_extract>()` functions in `crawler.js` to select the necessary elements.

In order to add fields:
1. Add an `extract<new_field_name>()` method that returns the information you need.
2. Add  a call to your new function to `processData()`.
3. Add a default value for your field to the initial page object (line 211) in the main code block.
4. Add any new logic to `./custom-post-importer.php` to get your field into Wordpress.
