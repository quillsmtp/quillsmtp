/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * External dependencies
 */
import { map, keys, size } from 'lodash';
import Stack from '@mui/material/Stack';
import classnames from 'classnames';
import Tooltip from '@mui/material/Tooltip';

/**
 * Internal dependencies
 */
import MailerAccounts from './mailer-accounts';
import { getMailerModules } from '@quillsmtp/mailers';
import './style.scss';

interface Props {
	connectionId: string;
}
// @ts-ignore
const MailersSelector: React.FC<Props> = ({ connectionId }) => {
	const mailerModules = getMailerModules();
	const { getConnection } = useSelect((select) => ({
		getConnection: select('quillSMTP/core').getConnection,
	}));
	const connection = getConnection(connectionId);
	const { updateConnection } = useDispatch('quillSMTP/core');

	const onChange = (key: string) => {
		updateConnection(connectionId, { mailer: key });
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
				/>
			)}
		</div>
	);
};

export default MailersSelector;
