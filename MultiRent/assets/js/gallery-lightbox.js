(function () {
	const galleryLinks = Array.from(document.querySelectorAll('.unit-gallery-link'));
	if (!galleryLinks.length) {
		return;
	}

	let activeIndex = 0;
	let lastFocusedElement = null;
	const mediaItems = galleryLinks.map((link) => ({
		type: link.dataset.galleryType || 'image',
		href: link.href,
		videoSrc: link.dataset.videoSrc || '',
		thumbSrc: link.querySelector('img')?.getAttribute('src') || link.href,
		alt: link.querySelector('img')?.getAttribute('alt') || '',
	}));

	const lightbox = document.createElement('div');
	lightbox.className = 'gallery-lightbox';
	lightbox.setAttribute('role', 'dialog');
	lightbox.setAttribute('aria-modal', 'true');
	lightbox.setAttribute('aria-label', 'Apartment gallery viewer');
	lightbox.innerHTML = `
		<div class="gallery-lightbox-panel">
			<button class="gallery-lightbox-close" type="button" aria-label="Close gallery"><span aria-hidden="true"></span></button>
			<button class="gallery-lightbox-nav gallery-lightbox-prev" type="button" aria-label="Previous image"><span aria-hidden="true"></span></button>
			<div class="gallery-lightbox-scroll">
				<img class="gallery-lightbox-image" alt="">
				<iframe class="gallery-lightbox-video" title="Apartment video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
			</div>
			<button class="gallery-lightbox-nav gallery-lightbox-next" type="button" aria-label="Next image"><span aria-hidden="true"></span></button>
			<div class="gallery-lightbox-thumbs" aria-label="Gallery media"></div>
		</div>
	`;
	document.body.appendChild(lightbox);

	const image = lightbox.querySelector('.gallery-lightbox-image');
	const closeButton = lightbox.querySelector('.gallery-lightbox-close');
	const previousButton = lightbox.querySelector('.gallery-lightbox-prev');
	const nextButton = lightbox.querySelector('.gallery-lightbox-next');
	const video = lightbox.querySelector('.gallery-lightbox-video');
	const thumbs = lightbox.querySelector('.gallery-lightbox-thumbs');

	mediaItems.forEach((item, index) => {
		const button = document.createElement('button');
		button.type = 'button';
		button.className = `gallery-lightbox-thumb gallery-lightbox-thumb-${item.type}`;
		button.setAttribute('aria-label', item.alt || (item.type === 'video' ? `Open video ${index + 1}` : `Open image ${index + 1}`));
		const thumbImage = document.createElement('img');
		thumbImage.src = item.thumbSrc;
		thumbImage.alt = '';
		button.appendChild(thumbImage);
		if (item.type === 'video') {
			const play = document.createElement('span');
			play.className = 'gallery-lightbox-thumb-play';
			play.setAttribute('aria-hidden', 'true');
			button.appendChild(play);
		}
		button.addEventListener('click', () => showMedia(index));
		thumbs.appendChild(button);
	});

	const thumbButtons = Array.from(thumbs.querySelectorAll('.gallery-lightbox-thumb'));

	function autoplayUrl(url) {
		if (!url) {
			return '';
		}

		return `${url}${url.includes('?') ? '&' : '?'}autoplay=1`;
	}

	function showMedia(index) {
		activeIndex = (index + mediaItems.length) % mediaItems.length;
		const item = mediaItems[activeIndex];
		video.src = '';

		if (item.type === 'video' && item.videoSrc) {
			image.hidden = true;
			image.removeAttribute('src');
			video.hidden = false;
			video.src = autoplayUrl(item.videoSrc);
		} else {
			video.hidden = true;
			image.hidden = false;
			image.src = item.href;
			image.alt = item.alt;
		}

		thumbButtons.forEach((button, buttonIndex) => {
			button.classList.toggle('is-active', buttonIndex === activeIndex);
			button.setAttribute('aria-current', buttonIndex === activeIndex ? 'true' : 'false');
		});
		thumbButtons[activeIndex]?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
	}

	function openLightbox(index) {
		lastFocusedElement = document.activeElement;
		showMedia(index);
		lightbox.classList.add('is-open');
		document.body.classList.add('gallery-lightbox-open');
		closeButton.focus();
	}

	function closeLightbox() {
		lightbox.classList.remove('is-open');
		document.body.classList.remove('gallery-lightbox-open');
		video.src = '';
		lastFocusedElement?.focus();
	}

	galleryLinks.forEach((link, index) => {
		link.addEventListener('click', (event) => {
			event.preventDefault();
			openLightbox(index);
		});
	});

	closeButton.addEventListener('click', closeLightbox);
	previousButton.addEventListener('click', () => showMedia(activeIndex - 1));
	nextButton.addEventListener('click', () => showMedia(activeIndex + 1));
	lightbox.addEventListener('click', (event) => {
		if (event.target === lightbox) {
			closeLightbox();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (!lightbox.classList.contains('is-open')) {
			return;
		}

		if (event.key === 'Escape') {
			closeLightbox();
		}

		if (event.key === 'ArrowLeft') {
			showMedia(activeIndex - 1);
		}

		if (event.key === 'ArrowRight') {
			showMedia(activeIndex + 1);
		}
	});
}());
