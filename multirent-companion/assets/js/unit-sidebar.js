(function (wp) {
	if (!wp || !wp.plugins || !wp.editPost || !wp.element || !wp.components || !wp.data || !window.MultiRentUnitSidebar) {
		return;
	}

	const el = wp.element.createElement;
	const registerPlugin = wp.plugins.registerPlugin;
	const PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	const TextControl = wp.components.TextControl;
	const Button = wp.components.Button;
	const Notice = wp.components.Notice;
	const MediaUpload = wp.blockEditor && wp.blockEditor.MediaUpload;
	const MediaUploadCheck = wp.blockEditor && wp.blockEditor.MediaUploadCheck;
	const useSelect = wp.data.useSelect;
	const useDispatch = wp.data.useDispatch;
	const useEntityProp = wp.coreData && wp.coreData.useEntityProp;

	function hideDuplicateFeaturedImagePanel() {
		let unsubscribe = null;
		unsubscribe = wp.data.subscribe(function () {
			const postType = wp.data.select('core/editor').getCurrentPostType();
			if (postType !== 'rental_unit') {
				return;
			}

			const editPostStore = wp.data.dispatch('core/edit-post');
			if (editPostStore && editPostStore.removeEditorPanel) {
				editPostStore.removeEditorPanel('featured-image');
			}

			if (unsubscribe) {
				unsubscribe();
			}
		});
	}

	hideDuplicateFeaturedImagePanel();

	function ApartmentDetailsPanel() {
		const postType = useSelect(function (select) {
			return select('core/editor').getCurrentPostType();
		}, []);

		const editorMeta = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('meta') || {};
		}, []);
		const entityMetaPair = useEntityProp ? useEntityProp('postType', postType || 'rental_unit', 'meta') : null;
		const meta = entityMetaPair ? (entityMetaPair[0] || {}) : editorMeta;
		const setEntityMeta = entityMetaPair ? entityMetaPair[1] : null;

		const editPost = useDispatch('core/editor').editPost;

		if (postType !== 'rental_unit') {
			return null;
		}

		function updateMeta(key, value) {
			const nextMeta = Object.assign({}, meta);
			nextMeta[key] = value;
			if (setEntityMeta) {
				setEntityMeta(nextMeta);
				return;
			}

			editPost({ meta: nextMeta });
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'multirent-apartment-details',
				title: window.MultiRentUnitSidebar.panelTitle || 'Apartment Details',
				initialOpen: true
			},
			el(Notice, { status: 'info', isDismissible: false }, window.MultiRentUnitSidebar.imageHelp),
			(window.MultiRentUnitSidebar.fields || []).map(function (field) {
				return el(TextControl, {
					key: field.key,
					label: field.label,
					help: field.description,
					type: field.type || 'text',
					value: meta[field.key] || '',
					onChange: function (value) {
						updateMeta(field.key, value);
					}
				});
			}),
			MediaUpload && MediaUploadCheck ? el(
				'div',
				{ className: 'multirent-sidebar-media-field' },
				el('p', { className: 'components-base-control__label' }, window.MultiRentUnitSidebar.qrImageLabel || 'QR code image'),
				meta[window.MultiRentUnitSidebar.qrImageKey] ? el('p', { className: 'multirent-sidebar-media-value' }, `Image ID: ${meta[window.MultiRentUnitSidebar.qrImageKey]}`) : null,
				el(MediaUploadCheck, null,
					el(MediaUpload, {
						onSelect: function (media) {
							updateMeta(window.MultiRentUnitSidebar.qrImageKey, media && media.id ? media.id : 0);
						},
						allowedTypes: ['image'],
						value: meta[window.MultiRentUnitSidebar.qrImageKey] || 0,
						render: function (props) {
							return el(Button, { variant: 'secondary', onClick: props.open }, window.MultiRentUnitSidebar.qrImageButton || 'Choose QR code image');
						}
					})
				),
				meta[window.MultiRentUnitSidebar.qrImageKey] ? el(Button, {
					variant: 'link',
					isDestructive: true,
					onClick: function () {
						updateMeta(window.MultiRentUnitSidebar.qrImageKey, 0);
					}
				}, window.MultiRentUnitSidebar.qrImageRemove || 'Remove QR code image') : null,
				el('p', { className: 'components-base-control__help' }, window.MultiRentUnitSidebar.qrImageHelp)
			) : null,
			el(Notice, { status: 'warning', isDismissible: false }, window.MultiRentUnitSidebar.publishHelp)
		);
	}

	registerPlugin('multirent-unit-sidebar', { render: ApartmentDetailsPanel });
}(window.wp));