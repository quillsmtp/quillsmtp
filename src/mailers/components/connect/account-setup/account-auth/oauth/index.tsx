/**
 * WordPress Dependencies
 */
import { useDispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { Button } from '@wordpress/components';

/**
 * Internal Dependencies
 */
import { AccountsLabels } from '../../../../types';

interface Props {
	connectionId: string;
	labels?: AccountsLabels;
	onAdded: (id: string, account: { name: string }) => void;
	Instructions?: React.FC;
}

const Oauth: React.FC<Props> = ({
	connectionId,
	labels,
	onAdded,
	Instructions,
}) => {
	const { connection } = useSelect((select) => {
		return {
			connection: select('quillSMTP/core').getConnection(connectionId),
		};
	});

	// dispatch notices.
	const { createSuccessNotice } = useDispatch('core/notices');

	const authorize = () => {
		window[`add_new_${connection.mailer}_account`] = (
			id: string,
			name: string
		) => {
			createSuccessNotice(
				'✅ ' +
					(
						labels?.singular ?? __('Account', 'quillsmtp')
					).toLowerCase() +
					' ' +
					__('added successfully!', 'quillsmtp'),
				{
					type: 'snackbar',
					isDismissible: true,
				}
			);
			onAdded(id, { name });
		};
		window.open(
			`${window['qsmtpAdmin'].adminUrl}admin.php?quillsmtp-${connection.mailer}=authorize`,
			'authorize',
			'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100'
		);
	};

	return (
		<div className="mailer-auth-oauth">
			<Button onClick={authorize}>
				{sprintf(
					__('Authorize Your %s', 'quillsmtp'),
					labels?.singular ?? __('Account', 'quillsmtp')
				)}
			</Button>
			{Instructions && (
				<div className="mailer-auth-instructions">
					<Instructions />
				</div>
			)}
		</div>
	);
};

export default Oauth;
