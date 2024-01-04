/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

/**
 * External dependencies
 */
import { map, keys, size } from 'lodash';
import Stack from '@mui/material/Stack';
import classnames from 'classnames';
import Tooltip from '@mui/material/Tooltip';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import IconButton from '@mui/material/IconButton';
import CloseIcon from '@mui/icons-material/Close';

/**
 * Internal dependencies
 */
import MailerAccounts from './mailer-accounts';
import { getMailerModules } from '@quillsmtp/mailers';
import ConfigAPI from '@quillsmtp/config';
import MailerFeatureAvailability from '../../mailer-feature-availability';
import './style.scss';

interface Props {
	connectionId: string;
}
// @ts-ignore
const MailersSelector: React.FC<Props> = ({ connectionId }) => {
	const [proMailer, setProMailer] = useState<string>('');
	const mailerModules = getMailerModules();
	const { getConnection, getMailer } = useSelect((select) => ({
		getConnection: select('quillSMTP/core').getConnection,
		getMailer: select('quillSMTP/core').getMailer,
	}));
	const connection = getConnection(connectionId);
	const { updateConnection } = useDispatch('quillSMTP/core');
	const { getStoreMailers } = ConfigAPI;
	const mailers = getStoreMailers();

	const onChange = (key: string) => {
		const mailer = mailers[key];
		if (!mailer || mailer?.is_pro) {
			setProMailer(key);
			return;
		}
		setProMailer('');
		const mailerData = getMailer(key);
		const { accounts } = mailerData;
		let account_id = '';
		if (size(accounts) > 0) {
			// get first account.
			account_id = keys(accounts)[0];
		}
		updateConnection(connectionId, { mailer: key, account_id: account_id });
	};

	return (
		<div className="qsmtp-mailers-selector">
			<div className="qsmtp-mailers-selector__title">
				{__('Select Mailer', 'quillsmtp')}
			</div>
			<Stack direction="row" spacing={2} useFlexGap flexWrap={'wrap'}>
				{size(mailerModules) > 0 &&
					map(keys(mailerModules), (key) => {
						const mailer = mailerModules[key];
						return (
							<Tooltip
								title={mailer.title}
								key={key}
								placement="top"
							>
								<div
									className={classnames(
										'qsmtp-mailers-selector__mailer',
										{
											'qsmtp-mailers-selector__mailer--active':
												connection.mailer === key,
										}
									)}
									onClick={() => onChange(key)}
								>
									<img src={mailer.icon} alt={mailer.title} />
								</div>
							</Tooltip>
						);
					})}
			</Stack>
			{connection?.mailer && (
				<MailerAccounts
					connectionId={connectionId}
					mailer={mailerModules[connection.mailer]}
					slug={connection.mailer}
				/>
			)}
			{proMailer && (
				<Dialog
					open={proMailer !== ''}
					onClose={() => {
						setProMailer('');
					}}
				>
					<DialogTitle
						sx={{
							display: 'flex',
							justifyContent: 'space-between',
							alignItems: 'center',
							background:
								'linear-gradient(42deg, rgb(235, 54, 221), rgb(238, 142, 22))',
							color: '#fff',
							fontWeight: 'bold',
							marginBottom: '20px',
							'& .MuiSvgIcon-root': {
								color: '#fff',
							},
						}}
					>
						{mailers[proMailer].name +
							__(' is a PRO feature', 'quillsmtp')}
						<IconButton
							aria-label="close"
							onClick={() => {
								setProMailer('');
							}}
						>
							<CloseIcon />
						</IconButton>
					</DialogTitle>
					<DialogContent>
						<MailerFeatureAvailability
							mailerSlug={proMailer}
							showLockIcon={true}
						/>
					</DialogContent>
				</Dialog>
			)}
		</div>
	);
};

export default MailersSelector;
