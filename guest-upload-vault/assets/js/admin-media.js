(function () {
	'use strict';

	function getI18nValue(key, fallback) {
		if (window.guvAdminMediaI18n && window.guvAdminMediaI18n[key]) {
			return window.guvAdminMediaI18n[key];
		}
		return fallback;
	}

	function onReady() {
		var bulkForm = document.getElementById('guv_media_bulk_form');
		var selectAll = document.getElementById('guv_select_all_media');

		if (bulkForm && selectAll) {
			var rowChecks = bulkForm.querySelectorAll('.guv-media-select');
			selectAll.addEventListener('change', function () {
				var checked = !!selectAll.checked;
				rowChecks.forEach(function (checkbox) {
					checkbox.checked = checked;
				});
			});

			rowChecks.forEach(function (checkbox) {
				checkbox.addEventListener('change', function () {
					var allSelected = true;
					rowChecks.forEach(function (item) {
						if (!item.checked) {
							allSelected = false;
						}
					});
					selectAll.checked = allSelected;
				});
			});

			bulkForm.addEventListener('submit', function (event) {
				var submitter = event.submitter || null;
				if (!submitter) {
					return;
				}

				if (submitter.dataset.guvRequiresSelection === '1') {
					var hasSelection = !!bulkForm.querySelector('.guv-media-select:checked');
					if (!hasSelection) {
						event.preventDefault();
						window.alert(getI18nValue('noSelection', 'Please select at least one file first.'));
						return;
					}
				}
			});
		}

		var deleteButtons = document.querySelectorAll('.guv-delete-button');
		deleteButtons.forEach(function (button) {
			button.addEventListener('click', function (event) {
				var scope = button.dataset.guvDeleteScope || 'single';
				if (scope === 'selected' && bulkForm) {
					var hasSelection = !!bulkForm.querySelector('.guv-media-select:checked');
					if (!hasSelection) {
						event.preventDefault();
						window.alert(getI18nValue('noSelection', 'Please select at least one file first.'));
						return;
					}
				}

				var message = getI18nValue('confirmDeleteSingle', 'Delete this file permanently?');
				if (scope === 'selected') {
					message = getI18nValue('confirmDeleteSelected', 'Delete selected media permanently?');
				}
				if (scope === 'all') {
					message = getI18nValue('confirmDeleteAll', 'Delete ALL collected media permanently?');
				}
				if (!window.confirm(message)) {
					event.preventDefault();
				}
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', onReady);
	} else {
		onReady();
	}
})();
