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
									label: __('Server API Token', 'quillsmtp'),
									type: 'password',
									required: true,
								},
								message_stream_id: {
									label: __('Message Stream ID', 'quillsmtp'),
									type: 'text',
									required: false,
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
