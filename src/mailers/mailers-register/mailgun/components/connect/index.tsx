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
									label: __('Private API Key', 'quillsmtp'),
									type: 'text',
									required: true,
								},
								domain_name: {
									label: __('Domain Name', 'quillsmtp'),
									type: 'text',
									required: true,
								},
								region: {
									label: __('Region', 'quillsmtp'),
									type: 'select',
									options: [
										{
											label: __('US', 'quillsmtp'),
											value: 'us',
										},
										{
											label: __('EU', 'quillsmtp'),
											value: 'eu',
										},
									],
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
