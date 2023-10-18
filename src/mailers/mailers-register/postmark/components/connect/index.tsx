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
				main={{
					accounts: {
						auth: {
							type: 'credentials',
							fields: {
								api_key: {
									label: __('Server API Token', 'quillsmtp'),
									type: 'text',
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
				close={onClose}
			/>
		</div>
	);
};

export default ConnectPage;
