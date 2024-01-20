/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';

addFilter(
	'QuillSMTP.Fetch.FromEmails',
	'QuillSMTP/ElasticEmail/Fetch/FromEmails',
	(fetch: boolean, slug: string) => {
		if (slug === 'elasticemail') {
			return true;
		}
		return fetch;
	}
);
