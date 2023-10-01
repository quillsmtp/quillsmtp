/**
 * WordPress Dependencies.
 */
import { addAction } from '@wordpress/hooks';

/**
 * Internal Dependencies.
 */
import ConfigAPI from '../../config';
import { registerMailerModule } from '../api';

addAction(
	'QuillSMTP.Admin.PluginsLoaded',
	'QuillSMTP/Mailers/RegisterStoreModules',
	register
);

console.log(ConfigAPI.getStoreMailers(), 'ConfigAPI.getStoreMailers()');

function register() {
	for (const [slug, mailer] of Object.entries(ConfigAPI.getStoreMailers())) {
		registerMailerModule(slug, {
			title: mailer.name,
			description: mailer.description,
			icon: mailer.assets.icon,
			render: () => null,
			settingsRender: () => null,
		});
	}
}
