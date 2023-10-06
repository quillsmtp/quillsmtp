/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Connect } from '@quillsmtp/mailers';

interface Props {
	connectionId: string;
	onClose: () => void;
}

const ConnectPage: React.FC<Props> = ({ connectionId, onClose }) => {
	return (
		<div className="qsmtp-connect-page">
			<Connect
				connectionId={connectionId}
				provider={{
					slug: 'sendinblue',
					label: __('Sendinblue', 'quillsmtp'),
				}}
				main={{
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								apiKey: {
									label: __('API Key', 'quillsmtp'),
									type: 'text',
									required: true,
								},
								sendingDomain: {
									label: __('Sending Domain', 'quillsmtp'),
									type: 'text',
									required: false,
								},
							},
						},
					},
				}}
				close={onClose}
			/>
		</div>
	);
};

export default ConnectPage;
