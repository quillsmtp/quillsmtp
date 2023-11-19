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
}

const ConnectPage: React.FC<Props> = ({ connectionId }) => {
	return (
		<div className="qsmtp-connect-page">
			<Connect
				connectionId={connectionId}
				setup={{
					Instructions: () => null,
					fields: {
						client_id: {
							label: __('Client ID', 'quillsmtp'),
							type: 'text',
							check: true,
						},
						client_secret: {
							label: __('Client Secret', 'quillsmtp'),
							type: 'text',
							check: false,
						},
					},
				}}
				main={{
					accounts: {
						auth: {
							type: 'oauth',
						},
					},
				}}
			/>
		</div>
	);
};

export default ConnectPage;
