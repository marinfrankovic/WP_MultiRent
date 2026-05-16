(function () {
	function getLabels() {
		return window.MultiRentAdmin || {};
	}

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

	function setGalleryPreview(control, attachments) {
		const idField = control.querySelector('[data-multirent-gallery-ids]');
		const preview = control.querySelector('[data-multirent-gallery-preview]');
		const labels = getLabels();

		if (!idField || !preview) {
			return;
		}

		idField.value = attachments.map((attachment) => attachment.id).filter(Boolean).join(',');
		preview.innerHTML = '';

		if (!attachments.length) {
			const empty = document.createElement('span');
			empty.textContent = labels.noImages || 'No gallery images selected';
			preview.appendChild(empty);
			return;
		}

		attachments.forEach((attachment) => {
			preview.appendChild(createGalleryItem(attachment));
		});

		refreshGalleryOrder(control);
	}

	function createGalleryItem(attachment) {
		const labels = getLabels();
		const sizes = attachment.sizes || {};
		const item = document.createElement('div');
		const image = document.createElement('img');
		const actions = document.createElement('div');
		const moveUpButton = document.createElement('button');
		const moveDownButton = document.createElement('button');
		const removeButton = document.createElement('button');

		item.className = 'multirent-gallery-preview-item';
		item.dataset.attachmentId = attachment.id || '';
		item.setAttribute('data-multirent-gallery-item', '');

		image.src = sizes.thumbnail ? sizes.thumbnail.url : attachment.url;
		image.alt = attachment.alt || '';

		actions.className = 'multirent-gallery-preview-actions';

		moveUpButton.type = 'button';
		moveUpButton.className = 'button button-small';
		moveUpButton.dataset.multirentGalleryMove = 'up';
		moveUpButton.textContent = labels.moveUp || 'Move up';

		moveDownButton.type = 'button';
		moveDownButton.className = 'button button-small';
		moveDownButton.dataset.multirentGalleryMove = 'down';
		moveDownButton.textContent = labels.moveDown || 'Move down';

		removeButton.type = 'button';
		removeButton.className = 'button button-small button-link-delete';
		removeButton.setAttribute('data-multirent-gallery-remove-item', '');
		removeButton.textContent = labels.removeImage || 'Remove image';

		actions.appendChild(moveUpButton);
		actions.appendChild(moveDownButton);
		actions.appendChild(removeButton);
		item.appendChild(image);
		item.appendChild(actions);

		return item;
	}

	function refreshGalleryOrder(control) {
		const idField = control.querySelector('[data-multirent-gallery-ids]');
		const preview = control.querySelector('[data-multirent-gallery-preview]');
		const labels = getLabels();

		if (!idField || !preview) {
			return;
		}

		const items = Array.from(preview.querySelectorAll('[data-multirent-gallery-item]'));
		idField.value = items.map((item) => item.dataset.attachmentId).filter(Boolean).join(',');

		if (!items.length) {
			preview.innerHTML = '';
			const empty = document.createElement('span');
			empty.textContent = labels.noImages || 'No gallery images selected';
			preview.appendChild(empty);
			return;
		}

		items.forEach((item, index) => {
			const moveUpButton = item.querySelector('[data-multirent-gallery-move="up"]');
			const moveDownButton = item.querySelector('[data-multirent-gallery-move="down"]');

			if (moveUpButton) {
				moveUpButton.disabled = index === 0;
			}

			if (moveDownButton) {
				moveDownButton.disabled = index === items.length - 1;
			}
		});
	}

	function openApartmentEditorBox() {
		const editorBox = document.getElementById('multirent_unit_details');
		if (!editorBox) {
			return;
		}

		editorBox.classList.remove('closed');
		editorBox.classList.add('multirent-editor-panel-open');

		const toggleButton = editorBox.querySelector('.handlediv, .handle-actions button');
		if (toggleButton) {
			toggleButton.setAttribute('aria-expanded', 'true');
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			openApartmentEditorBox();
		});
	} else {
		openApartmentEditorBox();
	}

	document.addEventListener('click', function (event) {
		const selectButton = event.target.closest('[data-multirent-media-select]');
		const removeButton = event.target.closest('[data-multirent-media-remove]');
		const gallerySelectButton = event.target.closest('[data-multirent-gallery-select]');
		const galleryRemoveButton = event.target.closest('[data-multirent-gallery-remove]');
		const galleryMoveButton = event.target.closest('[data-multirent-gallery-move]');
		const galleryRemoveItemButton = event.target.closest('[data-multirent-gallery-remove-item]');
		const labels = getLabels();

		if (selectButton) {
			event.preventDefault();
			const control = selectButton.closest('[data-multirent-media-control]');
			if (!control || !window.wp || !wp.media) {
				return;
			}

			const frame = wp.media({
				title: labels.chooseImage || 'Choose image',
				button: { text: labels.useImage || 'Use this image' },
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

		if (gallerySelectButton) {
			event.preventDefault();
			const control = gallerySelectButton.closest('[data-multirent-gallery-control]');
			if (!control || !window.wp || !wp.media) {
				return;
			}

			const frame = wp.media({
				title: labels.chooseGallery || 'Choose apartment gallery images',
				button: { text: labels.useGallery || 'Use selected images' },
				multiple: true,
				library: { type: 'image' }
			});

			frame.on('select', function () {
				setGalleryPreview(control, frame.state().get('selection').toJSON());
			});

			frame.open();
		}

		if (galleryRemoveButton) {
			event.preventDefault();
			const control = galleryRemoveButton.closest('[data-multirent-gallery-control]');
			if (control) {
				setGalleryPreview(control, []);
			}
		}

		if (galleryMoveButton) {
			event.preventDefault();
			const item = galleryMoveButton.closest('[data-multirent-gallery-item]');
			const control = galleryMoveButton.closest('[data-multirent-gallery-control]');
			if (!item || !control) {
				return;
			}

			if (galleryMoveButton.dataset.multirentGalleryMove === 'up' && item.previousElementSibling) {
				item.parentNode.insertBefore(item, item.previousElementSibling);
			}

			if (galleryMoveButton.dataset.multirentGalleryMove === 'down' && item.nextElementSibling) {
				item.parentNode.insertBefore(item.nextElementSibling, item);
			}

			refreshGalleryOrder(control);
		}

		if (galleryRemoveItemButton) {
			event.preventDefault();
			const item = galleryRemoveItemButton.closest('[data-multirent-gallery-item]');
			const control = galleryRemoveItemButton.closest('[data-multirent-gallery-control]');
			if (item && control) {
				item.remove();
				refreshGalleryOrder(control);
			}
		}
	});

}());
