/**
 * External Dependencies
 */
import { isFunction, some } from 'lodash';

/**
 * WordPress Dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import { Pages, PageSettings } from '../types';

const adminPages: Pages = {};

export const registerAdminPage = (id: string, settings: PageSettings) => {
	if (settings.exact === undefined) {
		settings.exact = true;
	}

	settings = applyFilters(
		'QuillSMTP.Navigation.PageSettings',
		settings,
		id
	) as PageSettings;

	if (adminPages[id]) {
		console.error(__('This page id is already registered', 'quillsmtp'));
		return;
	}

	if (!settings.path) {
		console.error(__('Path property is mandatory!', 'quillsmtp'));
		return;
	}

	if (typeof settings.path !== 'string') {
		console.error(__('The "path" property must be a string!', 'quillsmtp'));
		return;
	}

	if (
		some(Object.values(adminPages), (page) => page.path === settings.path)
	) {
		console.error(__('This path is already registered!', 'quillsmtp'));
		return;
	}

	if (typeof settings.exact !== 'boolean') {
		console.error(
			__('The "exact" property must be a boolean!', 'quillsmtp')
		);
		return;
	}

	if (!settings.component) {
		console.error(__('Component property is mandatory!', 'quillsmtp'));
		return;
	}

	if (!isFunction(settings.component)) {
		console.error(
			__(
				'The "component" property must be a valid function!',
				'quillsmtp'
			)
		);
		return;
	}

	adminPages[id] = settings;
};

export const getAdminPages = (): Pages => {
	return adminPages;
};
