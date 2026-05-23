(function (wp) {
	if (!wp || !wp.plugins || !wp.element || !wp.components || !wp.data || !window.MultiRentUnitSidebar) {
		return;
	}

	const el = wp.element.createElement;
	const registerPlugin = wp.plugins.registerPlugin;
	const editorModule = wp.editPost || wp.editor || {};
	const PluginDocumentSettingPanel = editorModule.PluginDocumentSettingPanel;
	const TextControl = wp.components.TextControl;
	const CheckboxControl = wp.components.CheckboxControl;
	const Button = wp.components.Button;
	const Notice = wp.components.Notice;
	const MediaUpload = wp.blockEditor && wp.blockEditor.MediaUpload;
	const MediaUploadCheck = wp.blockEditor && wp.blockEditor.MediaUploadCheck;
	const useSelect = wp.data.useSelect;
	const useDispatch = wp.data.useDispatch;
	const useEntityProp = wp.coreData && wp.coreData.useEntityProp;

	if (!PluginDocumentSettingPanel) {
		return;
	}

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

	function parseApartmentPageIds(value) {
		if (!value) {
			return [];
		}

		return String(value)
			.split(',')
			.map(function (id) {
				return parseInt(id, 10);
			})
			.filter(function (id, index, ids) {
				return id > 0 && ids.indexOf(id) === index;
			});
	}

	function formatApartmentPageIds(ids) {
		const cleanIds = (ids || []).filter(function (id, index, allIds) {
			return id > 0 && allIds.indexOf(id) === index;
		});

		return cleanIds.length ? `,${cleanIds.join(',')},` : '';
	}

	function defaultApartmentPageIds() {
		const pages = window.MultiRentUnitSidebar.apartmentPages || [];
		const firstPage = pages.find(function (page) {
			return parseInt(page.index, 10) === 1;
		});

		return firstPage && firstPage.id ? [parseInt(firstPage.id, 10)] : [];
	}

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

		const apartmentPageKey = window.MultiRentUnitSidebar.apartmentPageKey || '_multirent_apartment_page_ids';
		const apartmentPages = window.MultiRentUnitSidebar.apartmentPages || [];
		const selectedApartmentPageIds = parseApartmentPageIds(meta[apartmentPageKey]);
		const visibleApartmentPageIds = selectedApartmentPageIds.length ? selectedApartmentPageIds : defaultApartmentPageIds();

		function toggleApartmentPage(pageId, checked) {
			const currentIds = parseApartmentPageIds(meta[apartmentPageKey]);
			const nextIds = currentIds.length ? currentIds.slice() : defaultApartmentPageIds();
			const cleanPageId = parseInt(pageId, 10);

			if (checked && nextIds.indexOf(cleanPageId) === -1) {
				nextIds.push(cleanPageId);
			}

			if (!checked) {
				const index = nextIds.indexOf(cleanPageId);
				if (index !== -1) {
					nextIds.splice(index, 1);
				}
			}

			updateMeta(apartmentPageKey, formatApartmentPageIds(nextIds));
		}

		return el(
			wp.element.Fragment,
			null,
			el(
				PluginDocumentSettingPanel,
				{
					name: 'multirent-apartment-page-assignment',
					title: window.MultiRentUnitSidebar.apartmentPanelTitle || 'Apartment Page Assignment',
					initialOpen: true
				},
				el('p', { className: 'components-base-control__help' }, window.MultiRentUnitSidebar.apartmentPanelHelp),
				apartmentPages.length ? apartmentPages.map(function (page) {
					const pageId = parseInt(page.id, 10);
					const title = page.title ? ` - ${page.title}` : '';

					return el(CheckboxControl, {
						key: pageId,
						label: `${page.label}${title}`,
						help: `#${pageId}`,
						checked: visibleApartmentPageIds.indexOf(pageId) !== -1,
						onChange: function (checked) {
							toggleApartmentPage(pageId, checked);
						}
					});
				}) : el(Notice, { status: 'warning', isDismissible: false }, window.MultiRentUnitSidebar.noApartmentPages)
			),
			el(
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
			)
		);
	}

	registerPlugin('multirent-unit-sidebar', { render: ApartmentDetailsPanel });
}(window.wp));