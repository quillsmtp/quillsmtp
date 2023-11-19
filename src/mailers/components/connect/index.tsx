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
import App from './account-setup/app';

interface Props {
	connectionId: string;
	setup?: SetupType;
	main: ConnectMain;
}

const Connect: React.FC<Props> = ({ connectionId, setup, main }) => {
	const { connection, mailers } = useSelect((select) => {
		return {
			connection: select('quillSMTP/core').getConnection(connectionId),
			mailers: select('quillSMTP/core').getMailers(),
		};
	});

	if (!connection?.mailer) return null;

	const mailer = mailers[connection.mailer];
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
				<Setup connectionId={connectionId} setup={setup} />
			) : (
				<>
					{setup && !needSetup && (
						<App connectionId={connectionId} setup={setup} />
					)}
					<Main connectionId={connectionId} main={main} />
				</>
			)}
		</div>
	);
};

export default Connect;
