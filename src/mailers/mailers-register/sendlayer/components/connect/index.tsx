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
									label: __('API Key', 'quillsmtp'),
									type: 'text',
									required: true,
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
