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
import { getMailerModules } from '@quillsmtp/mailers';
import './style.scss';

interface Props {
	connectionId: string;
}
// @ts-ignore
const MailersSelector: React.FC<Props> = ({ connectionId }) => {
	const mailerModules = getMailerModules();
	const { mailerSlug, mailers } = useSelect((select) => {
		return {
			mailerSlug: select('quillSMTP/core').getTempConnectionMailer(connectionId),
			mailers: select('quillSMTP/core').getMailers(),
		}
	});

	const { updateTempConnection } = useDispatch('quillSMTP/core');

	const onChange = (key: string) => {
		let account_id = '';
		const mailerData = mailers[key];
		if (mailerData) {
			const { accounts } = mailerData;
			if (size(accounts) > 0) {
				// get first account.
				account_id = keys(accounts)[0];
			}
		}

		updateTempConnection(connectionId, { mailer: key, account_id: account_id });
	};

	return (
		<div className="qsmtp-mailers-selector">
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
												mailerSlug === key,
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

		</div>
	);
};

export default MailersSelector;
