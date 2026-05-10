(function () {
	function setPreview(control, id, url) {
		const idField = control.querySelector('[data-multirent-media-id]');
		const preview = control.querySelector('[data-multirent-media-preview]');
		if (!idField || !preview) {
			return;
		}

		idField.value = id || '';
		if (url) {
			preview.innerHTML = '';
			const image = document.createElement('img');
			image.src = url;
			image.alt = '';
			preview.appendChild(image);
			return;
		}

		preview.innerHTML = '<span>No image selected</span>';
	}

	document.addEventListener('click', function (event) {
		const selectButton = event.target.closest('[data-multirent-media-select]');
		const removeButton = event.target.closest('[data-multirent-media-remove]');

		if (selectButton) {
			event.preventDefault();
			const control = selectButton.closest('[data-multirent-media-control]');
			if (!control || !window.wp || !wp.media) {
				return;
			}

			const frame = wp.media({
				title: window.MultiRentAdmin ? MultiRentAdmin.chooseImage : 'Choose image',
				button: { text: window.MultiRentAdmin ? MultiRentAdmin.useImage : 'Use this image' },
				multiple: false,
				library: { type: 'image' }
			});

			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				const sizes = attachment.sizes || {};
				const url = sizes.medium ? sizes.medium.url : attachment.url;
				setPreview(control, attachment.id, url);
			});

			frame.open();
		}

		if (removeButton) {
			event.preventDefault();
			const control = removeButton.closest('[data-multirent-media-control]');
			if (control) {
				setPreview(control, '', '');
			}
		}
	});
}());
