/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { ConnectMain } from '../../types';
import AccountSelector from './account-selector';
import { useConnectContext } from '../state/context';
import Footer from '../footer';
import { useConnectionContext } from '@quillsmtp/connections';
import './style.scss';

interface Props {
	connectionId: string;
	main: ConnectMain;
	close: () => void;
}

const Main: React.FC<Props> = ({ connectionId, main, close }) => {
	// context.
	const { provider, savePayload } = useConnectContext();
	const { connections } = useConnectionContext();
	const connection = connections[connectionId];

	// dispatch notices.
	const { createSuccessNotice, createErrorNotice } =
		useDispatch('core/notices');

	const save = () => {
		// check validity.
		if (!validate()) {
			return;
		}
		savePayload('connections');
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
					provider.label
				)
			);
			return false;
		}
		return true;
	};

	return (
		<div className="mailer-connect-main">
			<div className="mailer-connect-main__wrapper">
				<AccountSelector connectionId={connectionId} main={main} />
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
