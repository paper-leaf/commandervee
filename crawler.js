/**
 * INSTRUCTIONS:
 *   1. Run a site scrape (using wget or equivalent) to load all the pages you 
 *      wish to JSON-ify onto the localhost.
 *   2. Point line 11 of ./crawler.php to the folder containing all the *.html
 *      files.
 *   3. Open ./crawler.php in your browser and let the script run.
 *   4. Remove the trailing comma and manually add wrapper curly braces to
 *      json.txt.
 *   5. Enjoy your JSONified site. May be used in conjunction with 
 *      ./custom-post-importer.php
 *
 *
 * MODIFICATIONS:
 *   To tweak this file to your specific needs, modify the extract*() functions
 *   to select the necessary elements.
 *
 *   In order to add fields, additional logic would also be required to 
 *   ./custom-post-importer.
 */

$(document).ready(function() {

	var content_selector = "#content";


	/**
	 * MAIN FUNCTIONS
	 */

	var JSONify = function(url, page) {

		try {
			result = $('#imported-content').load(url, function(responseText, textStatus, jqXHR){

				if (textStatus==='success') {
					processData(page);

					try {
						var slug = url.split(' ')[0].split('/');
						$('#imported-content').attr('data-slug', slug[slug.length-1].split('.')[0]);

					} catch(e) {
						console.log('Issue getting slug for ' + url);
						error.log(e)

					}

				} else {
					console.log('Could not load ' + url);
					files.eq(page.page_num+1).trigger('JSONify');

				};

			});

		} catch(e) {
			console.log('Issue loading ' + url);
			error.log(e)

		}

	};

	
	var processData = function(page) {

		page.page_slug = $('#imported-content').attr('data-slug');
		page.title = extractTitle();
		page.author = extractAuthor();
		page.author_slug = extractAuthorSlug();
		page.publish_date = extractPublishDate();
		page.featured_image = extractFeaturedImage();
		page.content = extractContent();

		exportData(page);
	
	};


	var exportData = function(page) {

		var my_JSON = JSON.stringify(page);

		var my_AJAX = $.ajax({
			type: 'POST',
			url: 'json-to-file.php',
			data: {json: '"' + page.page_num + '"' + ':' + my_JSON + ','},
			dataType: 'json',
		});

		var files = $('#file-collection .file');
		if (page.page_num+1 < files.length) {
			files.eq(page.page_num+1).trigger('JSONify');
		};

	};



	/**
	 * EXTRACTION FUNCTIONS
	 */
	
	var extractTitle = function() {
		var title = '';

		try {
			title = $('#parent-fieldname-title').text().trim();

		} finally {
			return title;

		}
	};

	var extractAuthor = function() {
		var author = '';

		try {
			author = $('#content .documentByLine a').text().trim() || $('#content .documentByLine').text().split('by')[1].trim();

		} finally {
			return author;

		}
	};
	
	var extractAuthorSlug = function() {
		var author_slug = '';

		try {
			author_url_bits = $('#content .documentByLine a').attr('href').split('/');
			author_slug = author_url_bits[author_url_bits.length-1];
		
		} finally {
			return author_slug;

		}
	};
	
	var extractPublishDate = function() {
		var date = '';

		try {
			date_string = $('#content .documentByLine').text().split('by')[0].trim();
			date = new Date(date_string);
		
		} finally {
			return date;

		}
	};
	
	var extractFeaturedImage = function() {
		var image = '';

		try {
			// attribute is ../image-mini. We want ../image, hence the slice.
			image = $('#content .newsImageContainer img').attr('src').slice(0,-5);

		} finally {
			return image;

		}

	};
	
	var extractContent = function() {
		var content = '';

		try {
			var no_share_content = $('#parent-fieldname-text').clone().find('#share-buttons').remove().end();
			content = no_share_content.html().trim();
			// Eff you, MS Word.
			content = content.replace(/[\u2018\u2019\u201A]/g, "\'");
			content = content.replace(/[\u201C\u201D\u201E]/g, "\"");
			content = content.replace(/\u2026/g, "...");
			content = content.replace(/[\u2013\u2014]/g, "-");
			content = content.replace(/\u02C6/g, "^");
			content = content.replace(/\u2039/g, "<");
			content = content.replace(/\u203A/g, ">");
			content = content.replace(/[\u02DC\u00A0]/g, " ");
			content = content.replace(/[^ -~]/g, '');
		
		} finally {
			return content;

		}
	};



	/**
	 * MAIN CODE
	 */

	 var counter = 0;

	 $('#file-collection .file').on('JSONify', function(){
	 	// console.log('Loading ' + $(this).attr('id') + ' ...');
	 	
	 	var this_page = {
	 		page_num		: counter,
	 		page_slug		: '',
	 		title			: '',
	 		author			: '',
	 		author_slug		: '',
	 		publish_date	: '',
	 		featured_image	: '',
	 		content			: '',
	 	};

	 	counter ++;

	 	JSONify($(this).attr('id') + ' ' + content_selector, this_page);

	 });

	 $('#file-collection .file').first().trigger('JSONify');
	 // $('#file-collection .file').eq(70).trigger('JSONify');
});

