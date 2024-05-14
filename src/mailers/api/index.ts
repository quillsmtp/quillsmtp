/**
 * WordPress Dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal Modules
 */
import type { MailerModules, MailerModuleSettings } from '../types';
import Connect from '../components/connect';

let mailerModules = {};

/**
 * Register Mailer Module
 *
 * @param {string}                    slug     The module slug
 * @param {MailerModuleSettings} settings The module settings.
 *
 */
export const registerMailerModule = (
	slug: string,
	settings: MailerModuleSettings
) => {
	settings = applyFilters(
		'QuillSMTP.Mailers.MailerModuleSettings',
		settings,
		slug,
		Connect
	) as MailerModuleSettings;

	if (mailerModules[slug]) {
		console.error(
			sprintf(
				__('This mailer %s is already registered!', 'quillsmtp'),
				slug
			)
		);
		return;
	}

	if (!settings.title) {
		console.error(__('The "title" property is mandatory!', 'quillsmtp'));
		return;
	}

	if (typeof settings.title !== 'string') {
		console.error(
			__('The "title" property must be a string!', 'quillsmtp')
		);
		return;
	}

	if (!settings.description) {
		console.error(
			__('The "description" property is mandatory!', 'quillsmtp')
		);
		return;
	}

	if (typeof settings.description !== 'string') {
		console.error(
			__('The "description" property must be a string!', 'quillsmtp')
		);
		return;
	}

	mailerModules[slug] = settings;
};

export const getMailerModules = (): MailerModules => {
	return mailerModules;
};

export const getMailerModule = (slug: string): MailerModuleSettings => {
	return mailerModules[slug];
};
