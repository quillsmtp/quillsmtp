/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';

addFilter(
	'QuillSMTP.FromEmails',
	'QuillSMTP/Gmail/AddFromEmails',
	(fromEmails: any, slug: string) => {
		if (slug === 'gmail') {
			console.log('fromEmails', fromEmails, slug);
		}
		return fromEmails;
	}
);
