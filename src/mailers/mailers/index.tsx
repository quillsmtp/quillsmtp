/**
 * WordPress Dependencies.
 */
import { addAction } from '@wordpress/hooks';

/**
 * Internal Dependencies.
 */
import ConfigAPI from '@quillsmtp/config';
import { registerMailerModule } from '../api';

addAction(
	'QuillSMTP.Admin.PluginsLoaded',
	'QuillSMTP/Mailers/RegisterStoreModules',
	register
);

function register() {
	for (const [slug, mailer] of Object.entries(ConfigAPI.getStoreMailers())) {
		registerMailerModule(slug, {
			title: mailer.name,
			description: mailer.description,
			icon: mailer.assets.icon,
			connectParameters: {},
			is_pro: mailer?.is_pro,
			documentation: mailer?.documentation,
		});
	}
}
