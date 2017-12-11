(function ($) {
	'use strict';

	$('body').on('click', 'a[href*="/uploads/"]', function (e) {
		e.preventDefault();

		var el = $(this);

		var data = {
			'action'	: 'top_downloads',
			'attachment': el.attr('href'),
			'nonce'		: top_downloads.nonce,
		};

		$.post(top_downloads.admin_ajax, data, function () {
			window.location.href = el.attr('href');
		});

		return false;
	});
})(jQuery);
