/* globals ELP URL */
(function() {

	var links, delay, modal, modalOverlay, modalOk,
		modalClose, modalDest, modalCountdown, modalContent, redirectTimer;

	/**
	 * Initializes modal on all external links.
	 */
	function init() {
		var homeUrl = new URL(ELP.homeUrl),
			homeHost = homeUrl.host;

		for (var i = 0; i < links.length; i++) {

			var thisLink = new URL(links[i].href);

			if (thisLink.host !== homeHost) {
				if (!isException(links[i].href)) {
					links[i].addEventListener('click', function(e) {
						e.preventDefault();
						doPopup(this);
					});
				}
			}
		}
	}

	/**
	 * Checks if URL is in list of exceptions. Enforces trailing slashes on URLs.
	 *
	 * @param {string} href URL
	 */
	function isException(href) {
		var url = href.replace(/\/?$/, '/'),
			exceptions = ELP.exceptions,
			isException = false;

		for (let i = 0; i < exceptions.length; i++) {

			if (exceptions[i].elp_exception_match === 'regex') {

				if (url.match(exceptions[i].elp_exception_regex) !== null) {
					isException = true;
				}
			} else {

				if (url === exceptions[i].elp_exception_url.replace(/\/?$/, '/')) {
					isException = true;
				}
			}
		}

		if (url.match(/mailto:.*/i) || url.match(/tel:.*/i)) {
			isException = true;
		}

		return isException;
	}

	/**
	 * Opens modal.
	 */
	function openModal() {
		if (!modal.classList.contains('open')) {
			modal.classList.add('open');
			modal.style.display = 'block';
			modalOverlay.style.display = 'block';
			modalContent.setAttribute('aria-hidden', 'false');
			setTimeout(function() {
				modalOverlay.style.opacity = 1;
			}, 50);
			setTimeout(function() {
				modal.style.opacity = 1;
			}, 250);
		}
	}

	/**
	 * Closes modal
	 */
	function closeModal() {
		if (modal.classList.contains('open')) {
			cancelCountdown();
			modal.classList.remove('open');
			modal.style.opacity = 0;
			modalOverlay.style.opacity = 0;
			setTimeout(function() {
				modal.style.display = 'none';
				modalOverlay.style.display = 'none';
				modalContent.setAttribute('aria-hidden', 'true');
			}, 250);
		}
	}

	/**
	 * Starts countdown
	 * @param {string} redirect URL to redirect to when countdown ends
	 */
	function startCountdown(redirect) {
		var countdown = delay;
		redirectTimer = setInterval(function() {
			countdown--;
			if (modalCountdown) {
				modalCountdown.textContent = countdown;
			}
			if (countdown <= 0) {
				clearInterval(redirectTimer);
				if (redirect) {
					document.location.href = redirect;
				}
			}
		}, 1000);
	}

	/**
	 * Resets countdown text in countdown container
	 */
	function resetCountdown() {
		if (modalCountdown) {
			modalCountdown.textContent = delay;
		}
	}

	/**
	 * Clears countdown timer
	 */
	function cancelCountdown() {
		clearInterval(redirectTimer);
		setTimeout(function() {
			resetCountdown();
		}, 200);
	}

	/**
	 * Opens modal and starts countdown to URL redirect
	 * @param {string} link URL to redirect to
	 */
	function doPopup(link) {
		var dest = link.href;

		if (modalDest) {
			modalDest.forEach(function(el) {
				var destLink = document.createElement('a');
				destLink.setAttribute('href', dest);
				destLink.innerHTML = dest;
				el.innerHTML = '';
				el.appendChild(destLink);
			});
		}

		modalOk.setAttribute('href', dest);

		openModal(dest);

		if (delay === '0') {
			if (modalCountdown) {
				modalCountdown.remove();
			}
		} else {
			setTimeout(function() {
				startCountdown(dest);
			}, 600);
		}
	}

    function onDocumentReady() {
		modal = document.getElementById('elp-popup');

		if (modal) {
			links = document.getElementsByTagName('a');
			delay = modal.dataset.redirect;
			modalOverlay = document.getElementById('elp-popup-overlay');
			modalOk = document.querySelector('.elp-popup-ok');
			modalClose = document.querySelectorAll('.elp-popup-cancel');
			modalDest = document.querySelectorAll('.elp-popup-destination');
			modalCountdown = document.getElementById('elp-popup-countdown');
			modalContent = document.getElementById('elp-popup-content');

			init();

			if (modalCountdown) {
				modalCountdown.textContent = delay;
			}

			// Close modal on cancel button click
			for (var i = 0; i < modalClose.length; i++) {
				modalClose[i].addEventListener('click', closeModal);
			}

			// Close modal if ESC key pressed
			document.onkeyup = function(e) {
				e = e || window.event;
				if (e.keyCode === 27) {
					closeModal();
				}
			};
		}
    }

    document.addEventListener('DOMContentLoaded', function() {
        onDocumentReady();
    });

})();
