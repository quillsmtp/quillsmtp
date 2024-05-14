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
import { getMailerModule } from '@quillsmtp/mailers';

interface Props {
	connectionId: string;
	setup?: SetupType;
	main: ConnectMain;
}

const Connect: React.FC<Props> = ({ connectionId, setup, main }) => {
	const { connectionMailer, mailers } = useSelect((select) => {
		return {
			connectionMailer:
				select('quillSMTP/core').getConnectionMailer(connectionId),
			mailers: select('quillSMTP/core').getMailers(),
		};
	});

	if (!connectionMailer) return null;

	const mailer = mailers[connectionMailer];
	const app = mailer?.app || {};
	const storeMailer = getMailerModule(connectionMailer);

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
			{storeMailer.documentation && (
				<p
					className="qsmtp-mailer-accounts__documentation"
					style={{ marginBottom: '20px' }}
				>
					{__('Need help setting up your account?', 'quillsmtp')}{' '}
					<a
						href={storeMailer.documentation}
						target="_blank"
						rel="noreferrer"
						className="qsmtp-mailer-accounts__documentation"
					>
						{__('View Documentation', 'quillsmtp')}
					</a>
				</p>
			)}
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
