/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * Internal Dependencies
 */
import type { Setup as SetupType, ConnectMain } from '../types';
import Setup from './setup';
import Main from './main';

interface Props {
	connectionId: string;
	setup?: SetupType;
	main: ConnectMain;
	close: () => void;
}

const Connect: React.FC<Props> = ({ connectionId, setup, main, close }) => {
	const { provider, mailers } = useSelect((select) => {
		return {
			provider: select('quillSMTP/core').getCurrentMailerProvider(),
			mailers: select('quillSMTP/core').getMailers(),
		};
	});

	const mailer = mailers[provider.slug];
	const app = mailer?.app || {};

	// check if need setup.
	let needSetup = false;
	if (setup) {
		for (const [key, field] of Object.entries(setup.fields)) {
			if (field.check && !app[key]) {
				needSetup = true;
				break;
			}
		}
	}

	return (
		<div className="mailer-connect">
			{setup && needSetup ? (
				<Setup setup={setup} close={close} />
			) : (
				<Main connectionId={connectionId} main={main} close={close} />
			)}
		</div>
	);
};

export default Connect;
