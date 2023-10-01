/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Connect } from '../../../../components';

interface Props {
	onClose: () => void;
}

const ConnectPage: React.FC<Props> = ({ onClose }) => {
	return (
		<div className="qsmtp-connect-page">
			<Connect
				provider={{
					slug: 'sendinblue',
					label: __('Sendinblue', 'quillsmtp'),
				}}
				setup={{
					Instructions: () => <div>Instructions</div>,
					fields: {
						api_key: {
							label: __('API Key', 'quillsmtp'),
							check: true,
							type: 'text',
						},
					},
				}}
				main={{}}
				close={onClose}
			/>
		</div>
	);
};

export default ConnectPage;
