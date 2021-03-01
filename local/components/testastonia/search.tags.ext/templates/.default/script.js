$(document).ready(function () {
	$('.all-request__text').click(function () {
		var $collapse = $('.catalog-seek__collapse-wrap');
		var $favorites = $('.favorite-tags');
		if($collapse.is(':visible')) {
			$favorites.show();
			$collapse.slideUp();
		} else {
			$favorites.hide();
			$collapse.slideDown();
		}
	});
});