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
import { MdArrowOutward } from 'react-icons/md';

interface Props {
	connectionId: string;
	setup?: SetupType;
	main: ConnectMain;
}

const Connect: React.FC<Props> = ({ connectionId, setup, main }) => {
	const { connectionMailer, mailers } = useSelect((select) => {
		return {
			connectionMailer:
				select('quillSMTP/core').getTempConnectionMailer(connectionId),
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
			<div className='flex items-start justify-between qsmtp-setup-wizard__header'>
				<div className="">
					<h2 className="qsmtp-setup-wizard__header-title font-roboto capitalize">
						{__(
							"Let's configure your mail provider account settings",
							'quillsmtp'
						)}
					</h2>
					<p className='font-roboto text-[#6D6D6D] capitalize'>
						{' '}
						Configure your mail provider account settings to
						connect to your mail provider.{' '}
					</p>
				</div>
				{storeMailer.documentation && (
						<a
							href={storeMailer.documentation}
							target="_blank"
							rel="noreferrer"
							className='qsmtp-mailer-accounts__documentation flex items-center text-[#3858E9] gap-1 text-[15px] font-roboto font-medium'
						>
							{__('View Docs', 'quillsmtp')}
							<MdArrowOutward className='text-[18px]'/>
						</a>
				)}
			</div>
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
