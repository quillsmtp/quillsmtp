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
								smtp_host: {
									label: __('SMTP Host', 'quillsmtp'),
									type: 'text',
									required: true,
								},
								smtp_port: {
									label: __('SMTP Port', 'quillsmtp'),
									type: 'number',
									required: true,
									default: 587,
								},
								encryption: {
									label: __('Encryption', 'quillsmtp'),
									type: 'select',
									options: [
										{
											label: __('None', 'quillsmtp'),
											value: 'none',
										},
										{
											label: __('SSL', 'quillsmtp'),
											value: 'ssl',
										},
										{
											label: __('TLS', 'quillsmtp'),
											value: 'tls',
										},
									],
									default: 'tls',
								},
								auto_tls: {
									label: __('Auto TLS', 'quillsmtp'),
									type: 'toggle',
									help: __(
										'Enable this option to automatically enable TLS encryption if the server supports it.',
										'quillsmtp'
									),
									dependencies: {
										type: 'or',
										conditions: [
											{
												field: 'encryption',
												operator: '==',
												value: 'none',
											},
											{
												field: 'encryption',
												operator: '==',
												value: 'ssl',
											},
										],
									},
									default: true,
								},
								authentication: {
									label: __('Authentication', 'quillsmtp'),
									type: 'toggle',
									help: __(
										'if your SMTP server needs server credentials (username and password), you can enable this option.',
										'quillsmtp'
									),
									default: true,
								},
								username: {
									label: __('Username', 'quillsmtp'),
									type: 'text',
									dependencies: {
										conditions: [
											{
												field: 'authentication',
												operator: '==',
												value: true,
											},
										],
									},
								},
								password: {
									label: __('Password', 'quillsmtp'),
									type: 'password',
									dependencies: {
										conditions: [
											{
												field: 'authentication',
												operator: '==',
												value: true,
											},
										],
									},
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
