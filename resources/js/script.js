
// Page behavior: border draw trigger + condensed sign window toggles
document.addEventListener('DOMContentLoaded', function () {
	// Auto-dismiss alerts after 2 seconds
	var alerts = document.querySelectorAll('.alert, .alert-success, .alert-error, .alert-danger');
	alerts.forEach(function(alert) {
		setTimeout(function() {
			alert.style.transition = 'opacity 0.25s ease';
			alert.style.opacity = '0';
			setTimeout(function() {
				alert.style.display = 'none';
			}, 250);
		}, 2000);
	});

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

	// 2b) Profile dropdown toggle (authenticated layout)
	var profileBtn = document.querySelector('.profile-menu-btn');
	var profileDropdown = document.querySelector('.profile-dropdown');
	if (profileBtn && profileDropdown) {
		profileBtn.addEventListener('click', function (e) {
			e.stopPropagation();
			profileDropdown.classList.toggle('show');
			profileBtn.classList.toggle('active');
			profileBtn.setAttribute('aria-expanded', profileDropdown.classList.contains('show') ? 'true' : 'false');
			profileDropdown.setAttribute('aria-hidden', profileDropdown.classList.contains('show') ? 'false' : 'true');
		});

		profileDropdown.addEventListener('click', function (e) { e.stopPropagation(); });

		document.addEventListener('click', function () {
			if (profileDropdown.classList.contains('show')) {
				profileDropdown.classList.remove('show');
				profileBtn.classList.remove('active');
				profileBtn.setAttribute('aria-expanded', 'false');
				profileDropdown.setAttribute('aria-hidden', 'true');
			}
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
		var taglineDates = document.querySelectorAll('.the-header .tagline p:not(.no-date-update)');
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

// AJAX reaction handler for instant like/dislike updates
document.addEventListener('DOMContentLoaded', function() {
	var reactionBtns = document.querySelectorAll('.reaction-btn[data-reaction]');
	var replyBtns = document.querySelectorAll('.reply-btn');
	var cancelReplyBtns = document.querySelectorAll('.cancel-reply-btn');

	replyBtns.forEach(function(btn) {
		btn.addEventListener('click', function() {
			var commentId = this.getAttribute('data-comment-id');
			var replyForm = document.querySelector('.reply-form[data-comment-id="' + commentId + '"]');

			document.querySelectorAll('.reply-form').forEach(function(form) {
				form.style.display = 'none';
				var formTextArea = form.querySelector('textarea');
				if (formTextArea) {
					formTextArea.value = '';
				}
			});

			document.querySelectorAll('.reply-btn').forEach(function(button) {
				button.style.display = 'inline-flex';
			});

			if (replyForm) {
				replyForm.style.display = 'block';
				this.style.display = 'none';
			}
		});
	});

	cancelReplyBtns.forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			var replyForm = this.closest('.reply-form');
			var commentId = replyForm.getAttribute('data-comment-id');
			var replyBtn = document.querySelector('.reply-btn[data-comment-id="' + commentId + '"]');

			if (replyForm) {
				replyForm.style.display = 'none';
				replyForm.querySelector('textarea').value = '';
				// Reset character counter
				var charCounter = replyForm.querySelector('.char-count');
				var counterContainer = replyForm.querySelector('.char-counter');
				if (charCounter) {
					charCounter.textContent = '0';
				}
				if (counterContainer) {
					counterContainer.classList.remove('over-limit');
				}
			}
			if (replyBtn) {
				replyBtn.style.display = 'inline-block';
			}
		});
	});

	// Character counter for comment and reply textareas
	function updateCharCounter(textarea) {
		var charCount = textarea.value.length;
		var form = textarea.closest('form');
		if (form) {
			var counter = form.querySelector('.char-count');
			var counterContainer = form.querySelector('.char-counter');
			if (counter) {
				counter.textContent = charCount;
				// Add 'over-limit' class if exceeds 1000
				if (counterContainer) {
					if (charCount > 1000) {
						counterContainer.classList.add('over-limit');
					} else {
						counterContainer.classList.remove('over-limit');
					}
				}
			}
		}
	}

	// Main comment textarea
	var mainCommentTextarea = document.querySelector('.comment-form textarea[name="comment"]');
	if (mainCommentTextarea) {
		mainCommentTextarea.addEventListener('input', function() {
			updateCharCounter(this);
		});
	}

	// Reply textareas
	var replyTextareas = document.querySelectorAll('.reply-form textarea[name="comment"]');
	replyTextareas.forEach(function(textarea) {
		textarea.addEventListener('input', function() {
			updateCharCounter(this);
		});
	});
	
	reactionBtns.forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			
			var reactionType = this.getAttribute('data-reaction');
			var blogId = this.getAttribute('data-blog-id');
			var csrfToken = this.getAttribute('data-csrf');
			
			fetch('/user/blogs/' + blogId + '/reaction', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': csrfToken,
					'Accept': 'application/json',
				},
				body: JSON.stringify({
					reaction_type: reactionType,
				})
			})
			.then(function(response) { return response.json(); })
			.then(function(data) {
				if (data.success) {
					// Update button states
					var likeBtn = document.querySelector('.reaction-btn[data-reaction="like"][data-blog-id="' + blogId + '"]');
					var dislikeBtn = document.querySelector('.reaction-btn[data-reaction="dislike"][data-blog-id="' + blogId + '"]');
					
					// Remove active class from both
					likeBtn.classList.remove('active');
					dislikeBtn.classList.remove('active');
					
					// Add active class to clicked button if user still has reaction
					if (data.userReaction === 'like') {
						likeBtn.classList.add('active');
					} else if (data.userReaction === 'dislike') {
						dislikeBtn.classList.add('active');
					}
					
					// Update counts - find the span within the button
					var likeCountSpan = likeBtn.querySelector('.like-count');
					var dislikeCountSpan = dislikeBtn.querySelector('.dislike-count');
					
					if (likeCountSpan) {
						likeCountSpan.textContent = data.likeCount;
					}
					if (dislikeCountSpan) {
						dislikeCountSpan.textContent = data.dislikeCount;
					}
				}
			})
			.catch(function(error) { console.error('Error:', error); });
		});
	});
});