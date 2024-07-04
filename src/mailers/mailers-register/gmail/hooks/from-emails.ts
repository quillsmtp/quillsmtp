/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';

addFilter(
	'QuillSMTP.Fetch.FromEmails',
	'QuillSMTP/Gmail/Fetch/FromEmails',
	(fetch: boolean, slug: string) => {
		if (slug === 'gmail') {
			return true;
		}
		return fetch;
	}
);
