
// Page behavior: border draw trigger + condensed sign window toggles
document.addEventListener('DOMContentLoaded', function () {
	// 1) Trigger .left-nav border draw after `#egg-crack-left` animation completes
	var eggLeft = document.getElementById('egg-crack-left');
	var leftNav = document.querySelector('.left-nav');
	if (eggLeft && leftNav) {
		var onAnimEnd = function (e) {
			if (e.animationName === 'crack-left') {
				leftNav.classList.add('animate-border');
				eggLeft.removeEventListener('animationend', onAnimEnd);
			}
		};
		eggLeft.addEventListener('animationend', onAnimEnd);
	}

	// 2) Condensed sign window toggle
	var condBtn = document.querySelector('.condensed-sign');
	var condWin = document.querySelector('.condensed-sign-window');
	if (condBtn && condWin) {
		condBtn.addEventListener('click', function (e) {
			e.stopPropagation();
			condWin.classList.toggle('show');
		});

		// prevent clicks inside window from bubbling and closing
		condWin.addEventListener('click', function (e) { e.stopPropagation(); });

		// close when clicking outside
		document.addEventListener('click', function () {
			if (condWin.classList.contains('show')) condWin.classList.remove('show');
		});
	}

	// 3) Manage `.active` for menu buttons:
	// - If a button has a real href we don't block navigation.
	// - If it's a placeholder (empty/#) we prevent navigation and persist active state in localStorage.
	var menuContainer = document.querySelector('.second-container');
	if (menuContainer) {
		// On load: if server didn't mark an active item, restore from localStorage
		var serverActive = menuContainer.querySelector('.menu-btn-holder.active');
		if (!serverActive) {
			var stored = localStorage.getItem('activeMenuHref');
			if (stored) {
				var savedBtn = menuContainer.querySelector(".menu-btn-holder[href='" + stored + "']");
				if (savedBtn) savedBtn.classList.add('active');
			}
		}

		menuContainer.addEventListener('click', function (e) {
			var btn = e.target.closest('.menu-btn-holder');
			if (!btn || !menuContainer.contains(btn)) return;

			var href = btn.getAttribute('href');
			// placeholder -> prevent navigation and persist
			if (!href || href === '#' || href.trim() === '') {
				e.preventDefault();
				// record an identifier so active state can persist; try href then text
				var id = href || btn.querySelector('.menu-text-holder')?.textContent?.trim() || '';
				localStorage.setItem('activeMenuHref', id);
			} else {
				// real navigation: store href so on next page blade can match or fallback
				localStorage.setItem('activeMenuHref', href);
			}

			var prev = menuContainer.querySelector('.menu-btn-holder.active');
			if (prev && prev !== btn) prev.classList.remove('active');
			btn.classList.add('active');
		});
	}

	// 4) Auto-insert current date/time into `.tagline p` in format "Month D, YYYY | H:MM am/pm"
	(function insertCurrentDateTime() {
		function formatDateTime(d) {
			var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
			var month = months[d.getMonth()];
			var day = d.getDate();
			var year = d.getFullYear();
			var hours = d.getHours();
			var minutes = d.getMinutes();
			var ampm = hours >= 12 ? 'pm' : 'am';
			hours = hours % 12; hours = hours ? hours : 12; // convert 0 -> 12
			minutes = minutes < 10 ? '0' + minutes : minutes;
			return month + ' ' + day + ', ' + year + ' | ' + hours + ':' + minutes + ' ' + ampm;
		}

		// 5) Toggle `.pop-list` expansion when `#pop-more` is clicked
		(function setupPopListToggle(){
			var popMoreBtn = document.getElementById('pop-more');
			var popList = document.querySelector('.pop-list');
			var COLLAPSED_MAX = 300; // matches CSS collapsed max-height
			if (!popMoreBtn || !popList) return;

			// small helper: arrow HTML and preserve original collapsed HTML
			var arrowHtml = '<img src="/images/right.png" id="right-icn">';
			var popCollapsedHTML = popMoreBtn.innerHTML || ('View All Popular Blogs ' + arrowHtml);

			// ensure initial inline maxHeight aligns with CSS (keeps predictable animation)
			if (!popList.style.maxHeight) popList.style.maxHeight = getComputedStyle(popList).maxHeight || COLLAPSED_MAX + 'px';

			popMoreBtn.addEventListener('click', function (e) {
				e.preventDefault();
				var current = parseFloat(getComputedStyle(popList).maxHeight) || 0;
				var isCollapsed = current <= COLLAPSED_MAX + 1;
				if (isCollapsed) {
					// expand to full content height
					popList.style.maxHeight = popList.scrollHeight + 'px';
					popMoreBtn.setAttribute('aria-expanded', 'true');
					popMoreBtn.innerHTML = 'Show less ' + arrowHtml;
				} else {
					// collapse back
					popList.style.maxHeight = COLLAPSED_MAX + 'px';
					popMoreBtn.setAttribute('aria-expanded', 'false');
					popMoreBtn.innerHTML = popCollapsedHTML;
				}
			});

			// when window resizes and list is expanded, update maxHeight to fit new content
			window.addEventListener('resize', function () {
				var current = parseFloat(getComputedStyle(popList).maxHeight) || 0;
				if (current > COLLAPSED_MAX + 1) {
					popList.style.maxHeight = popList.scrollHeight + 'px';
				}
			});
		})();

		// 5b) Toggle `.rand-list` expansion when `#rand-more` is clicked (mirrors popular)
		(function setupRandListToggle(){
			var randMoreBtn = document.getElementById('rand-more');
			var randList = document.querySelector('.rand-list');
			var RAND_COLLAPSED_MAX = 300;
			if (!randMoreBtn || !randList) return;

			var arrowHtml = '<img src="/images/right.png" id="right-icn">';
			var randCollapsedHTML = randMoreBtn.innerHTML || ('View Random Blogs ' + arrowHtml);

			if (!randList.style.maxHeight) randList.style.maxHeight = getComputedStyle(randList).maxHeight || RAND_COLLAPSED_MAX + 'px';

			randMoreBtn.addEventListener('click', function (e) {
				e.preventDefault();
				var current = parseFloat(getComputedStyle(randList).maxHeight) || 0;
				var isCollapsed = current <= RAND_COLLAPSED_MAX + 1;
				if (isCollapsed) {
					randList.style.maxHeight = randList.scrollHeight + 'px';
					randMoreBtn.setAttribute('aria-expanded', 'true');
					randMoreBtn.innerHTML = 'Show less ' + arrowHtml;
				} else {
					randList.style.maxHeight = RAND_COLLAPSED_MAX + 'px';
					randMoreBtn.setAttribute('aria-expanded', 'false');
					randMoreBtn.innerHTML = randCollapsedHTML;
				}
			});

			window.addEventListener('resize', function () {
				var current = parseFloat(getComputedStyle(randList).maxHeight) || 0;
				if (current > RAND_COLLAPSED_MAX + 1) {
					randList.style.maxHeight = randList.scrollHeight + 'px';
				}
			});
		})();
		var taglineDates = document.querySelectorAll('.the-header .tagline p');
		if (!taglineDates || taglineDates.length === 0) return;

		function updateTagline() {
			var now = new Date();
			var formatted = formatDateTime(now);
			taglineDates.forEach(function (el) { el.textContent = formatted; });
		}

		// run immediately and then keep it live every second
		updateTagline();
		var _taglineInterval = setInterval(updateTagline, 1000);

		// clear interval on unload to avoid leaks when navigating away
		window.addEventListener('beforeunload', function () { clearInterval(_taglineInterval); });
	})();
});

document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('tags-more');
    var wrap = document.querySelector('.tags-wrap');
    var COLLAPSED_H = 120; // px
    if(!btn || !wrap) return;

	// helper to keep arrow icon in the tags button
	var arrowHtml = '<img src="/images/right.png" id="right-icn">';
	var tagsCollapsedHTML = btn.innerHTML || ('View All Tags ' + arrowHtml);

	// ensure starting collapsed height
	if (!wrap.style.height) wrap.style.height = COLLAPSED_H + 'px';

	btn.addEventListener('click', function(e){
		e.preventDefault();
		var isExpanded = wrap.classList.contains('expanded');

		if (!isExpanded) {
			// Expand: set explicit pixel height to animate from current to full, then switch to auto
			var target = wrap.scrollHeight;
			wrap.style.height = target + 'px';
			wrap.classList.add('expanded');
			btn.innerHTML = 'Show less ' + arrowHtml;

			var onEnd = function(){
				// After transition, allow natural height for responsive wrapping
				wrap.style.height = 'auto';
				wrap.removeEventListener('transitionend', onEnd);
			};
			wrap.addEventListener('transitionend', onEnd);
		} else {
			// Collapse: if height is auto, set to pixel first so transition can run
			var cur = wrap.scrollHeight;
			wrap.style.height = cur + 'px';
			// force reflow
			/* eslint-disable no-unused-expressions */ void wrap.offsetHeight; /* eslint-enable */
			// then animate down to collapsed height
			wrap.style.height = COLLAPSED_H + 'px';
			wrap.classList.remove('expanded');
			btn.innerHTML = tagsCollapsedHTML;
		}
	});
});