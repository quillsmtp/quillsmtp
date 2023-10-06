/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import type { ConnectMain } from '../../../types';
import { useConnectContext } from '../../state/context';
import { Account } from '../../state/types';
import AccountAuth from '../../../shared/account-auth';
import { useConnectionContext } from '@quillsmtp/connections';

interface Props {
	connectionId: string;
	main: ConnectMain;
}

const AccountSelector: React.FC<Props> = ({ connectionId, main }) => {
	// context.
	const { provider, accounts, addAccount, updateAccount, savePayload } =
		useConnectContext();
	const { connections, updateConnection } = useConnectionContext();
	const connection = connections[connectionId];
	console.log(connection);

	// state.
	const [showingAddNewAccount, setShowingAddNewAccount] = useState(false);
	const [addingNewAccount, setAddingNewAccount] = useState(false);

	// if there is no accounts, show add account.
	if (!showingAddNewAccount) {
		if (Object.entries(accounts).length === 0) {
			setTimeout(() => setShowingAddNewAccount(true));
			return null;
		}
	}

	// updating connection on changing account selection.
	const onChange = (value) => {
		setShowingAddNewAccount(value === 'add');
		if (value === 'select' || value === 'add') {
			value = undefined;
		}
		updateConnection(
			connectionId,
			{
				account_id: value,
			},
			false
		);
	};

	const onAdded = (id: string, account: Account) => {
		// add or update the account.
		if (accounts[id]) {
			updateAccount(id, account);
		} else {
			addAccount(id, account);
		}
		// save payload.
		setTimeout(() => savePayload('accounts'));
		// select it.
		onChange(id);
	};

	return (
		<div className="mailer-connect-main__account-selector">
			{showingAddNewAccount && (
				<AccountAuth
					provider={provider}
					data={main.accounts}
					onAdding={setAddingNewAccount}
					onAdded={onAdded}
				/>
			)}
		</div>
	);
};

export default AccountSelector;
