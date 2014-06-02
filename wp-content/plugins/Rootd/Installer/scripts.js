/**
 * Rootd Framework installer scripts.
 *
 * @package  	Rootd
 * @author   	Rick Buczynski <me@rickbuczynski.com>
 * @copyright   2014 Rick Buczynski. All Rights Reserved.
 */

(function($) {

	/**
	 * Alert Messages
	 */
	
	$('[data-alert]').each(function() {
		$(this).on('click', alert.bind(null, $(this).attr('data-alert')));
	});

})(jQuery);