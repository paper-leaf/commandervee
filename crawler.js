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

	var PAGE_SELECTOR = "#content";

	var TITLE_SELECTOR = "#ctl00_mainContent_pageTitle";
	var CONTENT_SELECTOR = "#ctl00_mainContent_pageContent";

	var files = $('#file-collection .file');


	/**
	 * MAIN FUNCTIONS
	 */

	var JSONify = function (url, page) {


		try {

			result = $('#imported-content').load(url, function (responseText, textStatus, jqXHR) {

				if (textStatus === 'success') {
					// console.log('Processing ' + url);
					processData(page);

				} else {
					console.log('Could not load ' + url);
					files.eq(page.page_num + 1).trigger('JSONify');

				};

			});

		} catch(e) {
			console.log('Issue loading ' + url);
			error.log(e)

		}

	};


	var processData = function(page) {

		page.title = extractTitle();
		page.content = extractContent();
		page.attachments = extractAttachments();

		// page.author = extractAuthor();
		// page.author_slug = extractAuthorSlug();
		// page.publish_date = extractPublishDate();
		// page.featured_image = extractFeaturedImage();

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
			title = $(TITLE_SELECTOR).text().trim();

		} finally {
			return title;

		}
	};

	var extractContent = function () {
		var content = '';

		try {
			content = $(CONTENT_SELECTOR).html().trim();
			// // Eff you, MS Word.
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

	var extractAttachments = function () {
		var attachments = [];

		try {
			// Find downloadable files
			$('#content a').each(function () {
				let href = $(this).attr('href');
				if (href.indexOf('doc.aspx') === 0) {
					var file = {
						'link': href,
						'title': $(this).text()
					}
					attachments.push(file);
				}
			});
			$('#content img').each(function () {
				let src = $(this).attr('src');
				if (src.indexOf('image.aspx') === 0) {
					var image = {
						'link': src,
						'title': $(this).attr('title') ? $(this).attr('title') : ''
					}
					attachments.push(image);
				}
			});

		} finally {
			console.log(attachments);
			return attachments;
		}
	}

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



	/**
	 * MAIN CODE
	 */

	var counter = 0;

	$('#file-collection .file').on('JSONify', function () {
	 	var this_page = {
	 		page_num		: counter,
	 		page_slug		: '',
	 		title			: '',
	 		author			: '',
	 		author_slug		: '',
	 		publish_date	: '',
	 		featured_image	: '',
	 		content			: '',
	 		attachments		: [],
			old_url			: $(this).attr('id'),
	 	};

		counter ++;

		JSONify($(this).attr('id') + ' ' + PAGE_SELECTOR, this_page);

	});

	$('#file-collection .file').first().trigger('JSONify');
});

