(function () {
	function initTabs() {
		var navs = document.querySelectorAll('.multirent-tab-nav');
		Array.prototype.forEach.call(navs, function (nav) {
			var tabs = nav.querySelectorAll('.nav-tab[data-multirent-tab]');
			Array.prototype.forEach.call(tabs, function (tab) {
				tab.addEventListener('click', function (event) {
					event.preventDefault();
					var target = tab.getAttribute('data-multirent-tab');

					Array.prototype.forEach.call(tabs, function (item) {
						item.classList.remove('nav-tab-active');
					});
					tab.classList.add('nav-tab-active');

					var panels = document.querySelectorAll('[data-multirent-panel]');
					Array.prototype.forEach.call(panels, function (panel) {
						if (panel.getAttribute('data-multirent-panel') === target) {
							panel.classList.add('is-active');
						} else {
							panel.classList.remove('is-active');
						}
					});
				});
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initTabs);
	} else {
		initTabs();
	}
})();
