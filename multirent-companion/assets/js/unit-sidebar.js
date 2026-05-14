(function (wp) {
	if (!wp || !wp.plugins || !wp.editPost || !wp.element || !wp.components || !wp.data || !window.MultiRentUnitSidebar) {
		return;
	}

	const el = wp.element.createElement;
	const registerPlugin = wp.plugins.registerPlugin;
	const PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	const TextControl = wp.components.TextControl;
	const Notice = wp.components.Notice;
	const useSelect = wp.data.useSelect;
	const useDispatch = wp.data.useDispatch;

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

		const meta = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('meta') || {};
		}, []);

		const editPost = useDispatch('core/editor').editPost;

		if (postType !== 'rental_unit') {
			return null;
		}

		function updateMeta(key, value) {
			const nextMeta = Object.assign({}, meta);
			nextMeta[key] = value;
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
			el(Notice, { status: 'warning', isDismissible: false }, window.MultiRentUnitSidebar.publishHelp)
		);
	}

	registerPlugin('multirent-unit-sidebar', { render: ApartmentDetailsPanel });
}(window.wp));