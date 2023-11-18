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
				main={{
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								api_key: {
									label: __('API Key', 'quillsmtp'),
									type: 'text',
									required: true,
								},
								sender_name: {
									label: __('Sender Name', 'quillsmtp'),
									type: 'text',
									required: true,
								},
							},
						},
					},
				}}
			/>
		</div>
	);
};

export default ConnectPage;
