/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { ConnectMain } from '../../types';
import AccountSelector from './account-selector';
import Footer from '../footer';
import './style.scss';

interface Props {
	connectionId: string;
	main: ConnectMain;
	close: () => void;
}

const Main: React.FC<Props> = ({ connectionId, main, close }) => {
	const { getConnection, provider } = useSelect((select) => {
		return {
			getConnection: select('quillSMTP/core').getConnection,
			provider: select('quillSMTP/core').getCurrentMailerProvider(),
		};
	});
	const connection = getConnection(connectionId);

	// dispatch notices.
	const { createSuccessNotice, createErrorNotice } =
		useDispatch('core/notices');

	const save = () => {
		// check validity.
		if (!validate()) {
			return;
		}
		createSuccessNotice('✅ ' + __('updated successfully!', 'quillsmtp'), {
			type: 'snackbar',
			isDismissible: true,
		});
	};

	const error = (message) => {
		createErrorNotice('⛔ ' + message, {
			type: 'snackbar',
			isDismissible: true,
		});
	};

	const validate = () => {
		if (!connection.account_id) {
			error(
				sprintf(
					__('Please select an account for %s.', 'quillsmtp'),
					provider.title
				)
			);
			return false;
		}
		return true;
	};

	return (
		<div className="mailer-connect-main">
			<div className="mailer-connect-main__wrapper">
				<AccountSelector main={main} />
				<Footer
					save={{
						label: __('Save', 'quillforms'),
						onClick: save,
						disabled: false,
					}}
					close={{
						label: __('Cancel', 'quillforms'),
						onClick: close,
					}}
				/>
			</div>
		</div>
	);
};

export default Main;
