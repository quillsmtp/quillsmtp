/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal Dependencies
 */
import type { ConnectMain } from '../../../types';
import type { Account } from '@quillsmtp/store';
import AccountAuth from '../../account-setup/account-auth';

interface Props {
	connectionId: string;
	main: ConnectMain;
}

const AccountSelector: React.FC<Props> = ({ connectionId, main }) => {
	// context.
	const { currentMailer } = useSelect((select) => {
		return {
			currentMailer: select('quillSMTP/core').getCurrentMailer(),
		};
	});

	const { accounts } = currentMailer;
	console.log(accounts, 'accounts');

	const { addAccount, updateAccount, updateConnection } =
		useDispatch('quillSMTP/core');

	// state.
	const [showingAddNewAccount, setShowingAddNewAccount] = useState(false);
	// @ts-ignore
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
		updateConnection(connectionId, {
			account_id: value,
		});
	};

	const onAdded = (id: string, account: Account) => {
		// add or update the account.
		if (accounts[id]) {
			updateAccount(id, account);
		} else {
			addAccount(id, account);
		}
		// select it.
		onChange(id);
	};

	return (
		<div className="mailer-connect-main__account-selector">
			<button onClick={() => addAccount('1234', { name: 'abdo' })}>
				Test
			</button>
			{showingAddNewAccount && (
				<AccountAuth
					data={main.accounts}
					onAdding={setAddingNewAccount}
					onAdded={onAdded}
				/>
			)}
		</div>
	);
};

export default AccountSelector;
