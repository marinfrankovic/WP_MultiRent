(function () {
	const galleryLinks = Array.from(document.querySelectorAll('.unit-gallery-link'));
	if (!galleryLinks.length) {
		return;
	}

	let activeIndex = 0;
	let lastFocusedElement = null;
	const images = galleryLinks.map((link) => ({
		href: link.href,
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
			</div>
			<button class="gallery-lightbox-nav gallery-lightbox-next" type="button" aria-label="Next image"><span aria-hidden="true"></span></button>
			<div class="gallery-lightbox-thumbs" aria-label="Gallery images"></div>
		</div>
	`;
	document.body.appendChild(lightbox);

	const image = lightbox.querySelector('.gallery-lightbox-image');
	const closeButton = lightbox.querySelector('.gallery-lightbox-close');
	const previousButton = lightbox.querySelector('.gallery-lightbox-prev');
	const nextButton = lightbox.querySelector('.gallery-lightbox-next');
	const thumbs = lightbox.querySelector('.gallery-lightbox-thumbs');

	images.forEach((item, index) => {
		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'gallery-lightbox-thumb';
		button.setAttribute('aria-label', item.alt || `Open image ${index + 1}`);
		button.innerHTML = `<img src="${item.href}" alt="">`;
		button.addEventListener('click', () => showImage(index));
		thumbs.appendChild(button);
	});

	const thumbButtons = Array.from(thumbs.querySelectorAll('.gallery-lightbox-thumb'));

	function showImage(index) {
		activeIndex = (index + images.length) % images.length;
		image.src = images[activeIndex].href;
		image.alt = images[activeIndex].alt;
		thumbButtons.forEach((button, buttonIndex) => {
			button.classList.toggle('is-active', buttonIndex === activeIndex);
			button.setAttribute('aria-current', buttonIndex === activeIndex ? 'true' : 'false');
		});
		thumbButtons[activeIndex]?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
	}

	function openLightbox(index) {
		lastFocusedElement = document.activeElement;
		showImage(index);
		lightbox.classList.add('is-open');
		document.body.classList.add('gallery-lightbox-open');
		closeButton.focus();
	}

	function closeLightbox() {
		lightbox.classList.remove('is-open');
		document.body.classList.remove('gallery-lightbox-open');
		lastFocusedElement?.focus();
	}

	galleryLinks.forEach((link, index) => {
		link.addEventListener('click', (event) => {
			event.preventDefault();
			openLightbox(index);
		});
	});

	closeButton.addEventListener('click', closeLightbox);
	previousButton.addEventListener('click', () => showImage(activeIndex - 1));
	nextButton.addEventListener('click', () => showImage(activeIndex + 1));
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
			showImage(activeIndex - 1);
		}

		if (event.key === 'ArrowRight') {
			showImage(activeIndex + 1);
		}
	});
}());
