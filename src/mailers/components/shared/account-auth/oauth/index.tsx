/**
 * QuillForms Dependencies
 */
import { Button } from '@wordpress/components';

/**
 * WordPress Dependencies
 */
import { useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import { AccountsLabels, Provider } from '../../../types';

interface Props {
	provider: Provider;
	labels?: AccountsLabels;
	onAdded: (id: string, account: { name: string }) => void;
	Instructions?: React.FC;
}

const Oauth: React.FC<Props> = ({
	provider,
	labels,
	onAdded,
	Instructions,
}) => {
	// dispatch notices.
	const { createSuccessNotice } = useDispatch('core/notices');

	const authorize = () => {
		window[`add_new_${provider.slug}_account`] = (
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
			`${window['qsmtpAdmin'].adminUrl}admin.php?quillsmtp-${provider.slug}=authorize`,
			'authorize',
			'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=500,left=100,top=100'
		);
	};

	return (
		<div className="mailer-auth-oauth">
			<Button onClick={authorize}>
				Authorize Your {labels?.singular ?? 'Account'}
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
