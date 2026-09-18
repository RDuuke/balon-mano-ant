(function (wp, config) {
	'use strict';

	if (!wp || !config) {
		return;
	}

	var el = wp.element && wp.element.createElement;
	var strings = config.strings || {};
	var maxBytes = Number(config.effectiveMaxBytes || 0);

	function formatBytes(bytes) {
		var value = Number(bytes || 0);
		if (!value) {
			return '';
		}
		if (value >= 1024 * 1024) {
			return (value / (1024 * 1024)).toLocaleString(undefined, { maximumFractionDigits: 1 }) + ' MB';
		}
		return Math.max(1, Math.round(value / 1024)).toLocaleString() + ' KB';
	}

	function attachmentData(model) {
		var data = model && model.toJSON ? model.toJSON() : model || {};
		var bytes = Number(data.filesizeInBytes || data.filesize || data.size || 0);
		return {
			id: Number(data.id || 0),
			name: data.filename || data.name || '',
			size: bytes,
			sizeLabel: formatBytes(bytes),
			mime: data.mime || data.mime_type || '',
			valid: true
		};
	}

	function validateSelectedFile(file) {
		if (!file.id || (file.mime && file.mime !== 'application/pdf')) {
			return strings.wrongType;
		}
		if (maxBytes && file.size > maxBytes) {
			return strings.tooLarge;
		}
		return '';
	}

	function openMedia(opener, selected, failed) {
		var frame = wp.media({
			title: strings.mediaTitle,
			button: { text: strings.usePdf },
			library: { type: 'application/pdf' },
			multiple: false
		});

		frame.on('select', function () {
			var file = attachmentData(frame.state().get('selection').first());
			var error = validateSelectedFile(file);
			if (error) {
				failed(error);
				return;
			}
			selected(file);
		});
		frame.on('error', function () {
			failed(strings.mediaError);
		});
		frame.on('close', function () {
			if (opener && opener.focus) {
				opener.focus();
			}
		});
		frame.open();
	}

	function validDate(value) {
		var parts;
		var date;
		if (!value) {
			return true;
		}
		if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
			return false;
		}
		parts = value.split('-').map(Number);
		date = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));
		return date.getUTCFullYear() === parts[0] && date.getUTCMonth() === parts[1] - 1 && date.getUTCDate() === parts[2];
	}

	function focusField(field, fallback) {
		var target;
		if (field === 'post_title') {
			target = document.querySelector('.editor-post-title__input, [aria-label="Add title"], [name="post_title"]');
		} else if (field === 'labm_documento_fecha') {
			target = document.querySelector('[data-labm-document-date], #labm-documento-fecha');
		} else {
			target = fallback;
		}
		if (target && target.focus) {
			target.focus();
		}
	}

	function BlockDocumentPanel() {
		var useSelect = wp.data.useSelect;
		var useDispatch = wp.data.useDispatch;
		var useEffect = wp.element.useEffect;
		var useRef = wp.element.useRef;
		var useState = wp.element.useState;
		var Button = wp.components.Button;
		var SelectControl = wp.components.SelectControl;
		var TextControl = wp.components.TextControl;
		var state = useState(config.attachment || {});
		var attachment = state[0];
		var setAttachment = state[1];
		var errorState = useState('');
		var error = errorState[0];
		var setError = errorState[1];
		var liveState = useState('');
		var live = liveState[0];
		var setLive = liveState[1];
		var pdfButton = useRef(null);
		var errorBox = useRef(null);
		var editor = useSelect(function (select) {
			var store = select('core/editor');
			return {
				meta: store.getEditedPostAttribute('meta') || {},
				terms: store.getEditedPostAttribute('labm_documento_categoria') || [],
				title: store.getEditedPostAttribute('title') || ''
			};
		}, []);
		var editPost = useDispatch('core/editor').editPost;
		var pdfId = Number(editor.meta.labm_documento_pdf_id || attachment.id || 0);
		var date = editor.meta.labm_documento_fecha || '';
		var typeId = Number(editor.terms[0] || config.generalTermId || 0);

		function setMeta(key, value) {
			var next = Object.assign({}, editor.meta);
			next[key] = value;
			editPost({ meta: next });
		}

		function announce(message) {
			setLive('');
			window.setTimeout(function () { setLive(message); }, 20);
		}

		function showError(message, field) {
			setError(message);
			announce(message);
			if (wp.data.dispatch('core/notices')) {
				wp.data.dispatch('core/notices').createErrorNotice(message, {
					id: 'labm-document-admin-error',
					isDismissible: true
				});
			}
			window.setTimeout(function () {
				focusField(field, pdfButton.current || errorBox.current);
			}, 30);
		}

		function choosePdf(event) {
			var opener = event.currentTarget;
			openMedia(opener, function (file) {
				setAttachment(file);
				setMeta('labm_documento_pdf_id', file.id);
				setError('');
				announce(strings.validPdf + ': ' + file.name);
			}, function (message) {
				showError(message, 'labm_documento_pdf_id');
			});
		}

		function removePdf() {
			setAttachment({ id: 0, name: '', size: 0, sizeLabel: '', valid: false });
			setMeta('labm_documento_pdf_id', 0);
			setError('');
			announce(strings.noPdf + ' ' + strings.keptInLibrary);
			window.setTimeout(function () { focusField('labm_documento_pdf_id', pdfButton.current); }, 20);
		}

		function firstError() {
			if (!String(editor.title).trim()) {
				return { field: 'post_title', message: strings.titleRequired };
			}
			if (!pdfId) {
				return { field: 'labm_documento_pdf_id', message: strings.pdfRequired };
			}
			if (!validDate(date)) {
				return { field: 'labm_documento_fecha', message: strings.dateInvalid };
			}
			return null;
		}

		useEffect(function () {
			function mediaFailure() {
				showError(strings.mediaError, 'labm_documento_pdf_id');
			}
			function oversized() {
				showError(strings.tooLarge, 'labm_documento_pdf_id');
			}
			function guardSave(event) {
				var trigger = event.target.closest('.editor-post-publish-button, .editor-post-publish-panel__toggle, .editor-post-save-draft, .editor-post-publish-button__button');
				var invalid = trigger && firstError();
				if (!invalid) {
					return;
				}
				event.preventDefault();
				event.stopImmediatePropagation();
				showError(invalid.message, invalid.field);
			}
			window.addEventListener('labm:media-error', mediaFailure);
			window.addEventListener('labm:oversized-pdf', oversized);
			document.addEventListener('click', guardSave, true);
			return function () {
				window.removeEventListener('labm:media-error', mediaFailure);
				window.removeEventListener('labm:oversized-pdf', oversized);
				document.removeEventListener('click', guardSave, true);
			};
		}, [pdfId, date, editor.title]);

		return el('div', { 'data-labm-document-admin': '', 'data-labm-editor': 'blocks' },
			el('section', { role: 'region', 'aria-label': strings.pdfLegend },
				el('fieldset', { 'aria-describedby': 'labm-block-pdf-help labm-block-pdf-limit' },
					el('legend', null, strings.pdfLegend),
					error ? el('div', { className: 'notice notice-error', role: 'alert', tabIndex: '-1', ref: errorBox }, el('p', null, error)) : null,
					pdfId ? el('p', null,
						el('strong', null, attachment.name || strings.validPdf),
						attachment.sizeLabel ? ' · ' + attachment.sizeLabel : '',
						' · ' + (attachment.valid === false ? strings.invalidPdf : strings.validPdf)
					) : el('p', null, strings.noPdf),
					el('p', null,
						el(Button, { variant: 'secondary', onClick: choosePdf, ref: pdfButton }, pdfId ? strings.replacePdf : strings.selectPdf),
						pdfId ? el(Button, { variant: 'tertiary', isDestructive: true, onClick: removePdf }, strings.removePdf) : null
					),
					el('p', { id: 'labm-block-pdf-help', className: 'components-base-control__help' }, strings.keptInLibrary),
					el('p', { id: 'labm-block-pdf-limit', className: 'components-base-control__help', 'data-labm-effective-max-bytes': maxBytes }, strings.limitDescription)
				)
			),
			el(TextControl, {
				label: strings.documentDate,
				type: 'date',
				value: date,
				help: wp.i18n.__('Opcional. Usa la fecha oficial del documento.', 'labm-core'),
				onChange: function (value) { setMeta('labm_documento_fecha', value); },
				__nextHasNoMarginBottom: true,
				'data-labm-document-date': ''
			}),
			el(SelectControl, {
				label: strings.documentType,
				value: String(typeId),
				options: (config.terms || []).map(function (term) { return { label: term.name, value: String(term.id) }; }),
				help: wp.i18n.__('Selecciona un solo tipo.', 'labm-core'),
				onChange: function (value) {
					var change = {};
					change.labm_documento_categoria = [Number(value)];
					editPost(change);
				},
				__nextHasNoMarginBottom: true
			}),
			el('p', { className: 'screen-reader-text', 'aria-live': 'polite' }, live)
		);
	}

	function registerBlockPanel() {
		var Panel;
		if (!el || !wp.plugins) {
			return;
		}
		Panel = (wp.editor && wp.editor.PluginDocumentSettingPanel) || (wp.editPost && wp.editPost.PluginDocumentSettingPanel);
		if (!Panel) {
			return;
		}
		wp.plugins.registerPlugin('labm-document-admin', {
			render: function () {
				return el(Panel, { name: 'labm-document-admin', title: strings.panelTitle, className: 'labm-document-admin-panel', initialOpen: true }, el(BlockDocumentPanel));
			}
		});
		window.setTimeout(function () {
			var editorDispatcher = wp.data.dispatch('core/editor');
			var editorSelector = wp.data.select('core/editor');
			var sidebarDispatcher = wp.data.dispatch('core/edit-post');
			var panelName = 'labm-document-admin/labm-document-admin';
			if (editorDispatcher && editorDispatcher.removeEditorPanel) {
				editorDispatcher.removeEditorPanel('taxonomy-panel-labm_documento_categoria');
				editorDispatcher.removeEditorPanel('post-custom-fields');
			}
			if (editorDispatcher && editorDispatcher.toggleEditorPanelOpened && editorSelector && editorSelector.isEditorPanelOpened && !editorSelector.isEditorPanelOpened(panelName)) {
				editorDispatcher.toggleEditorPanelOpened(panelName);
			}
			if (sidebarDispatcher && sidebarDispatcher.openGeneralSidebar) {
				sidebarDispatcher.openGeneralSidebar('edit-post/document');
			}
		}, 100);
	}

	function initClassic() {
		var root = document.querySelector('[data-labm-document-admin][data-labm-editor="classic"]');
		var form;
		var hidden;
		var summary;
		var selectButton;
		var removeButton;
		var alertBox;
		var live;
		if (!root) {
			return;
		}
		form = root.closest('form') || document.getElementById('post');
		hidden = root.querySelector('[data-labm-pdf-id]');
		summary = root.querySelector('[data-labm-pdf-summary]');
		selectButton = root.querySelector('[data-labm-select-pdf]');
		removeButton = root.querySelector('[data-labm-remove-pdf]');
		alertBox = root.querySelector('[data-labm-admin-alert]');
		live = root.querySelector('[data-labm-admin-live]');

		function announce(message) {
			live.textContent = '';
			window.setTimeout(function () { live.textContent = message; }, 20);
		}
		function showError(message, field) {
			alertBox.hidden = false;
			alertBox.textContent = message;
			announce(message);
			window.setTimeout(function () { focusField(field, selectButton); }, 20);
		}
		function update(file) {
			hidden.value = String(file.id || 0);
			summary.textContent = file.id ? file.name + (file.sizeLabel ? ' · ' + file.sizeLabel : '') + ' · ' + strings.validPdf : strings.noPdf;
			selectButton.textContent = file.id ? strings.replacePdf : strings.selectPdf;
			removeButton.hidden = !file.id;
			alertBox.hidden = true;
		}

		selectButton.addEventListener('click', function (event) {
			openMedia(event.currentTarget, function (file) {
				update(file);
				announce(strings.validPdf + ': ' + file.name);
			}, function (message) { showError(message, 'labm_documento_pdf_id'); });
		});
		removeButton.addEventListener('click', function () {
			update({ id: 0 });
			announce(strings.noPdf + ' ' + strings.keptInLibrary);
			selectButton.focus();
		});
		window.addEventListener('labm:media-error', function () { showError(strings.mediaError, 'labm_documento_pdf_id'); });
		window.addEventListener('labm:oversized-pdf', function () { showError(strings.tooLarge, 'labm_documento_pdf_id'); });
		if (form) {
			form.addEventListener('submit', function (event) {
				var title = document.querySelector('[name="post_title"]');
				var date = root.querySelector('[name="labm_documento_fecha"]');
				var invalid = !title || !title.value.trim() ? { field: 'post_title', message: strings.titleRequired } :
					!Number(hidden.value) ? { field: 'labm_documento_pdf_id', message: strings.pdfRequired } :
					!validDate(date.value) ? { field: 'labm_documento_fecha', message: strings.dateInvalid } : null;
				if (invalid) {
					event.preventDefault();
					showError(invalid.message, invalid.field);
				}
			});
		}
	}

	registerBlockPanel();
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initClassic);
	} else {
		initClassic();
	}
})(window.wp, window.labmDocumentAdmin);
