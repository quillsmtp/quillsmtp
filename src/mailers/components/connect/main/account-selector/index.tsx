/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal Dependencies
 */
import { size, map } from 'lodash';

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
	const { currentMailer, getConnection } = useSelect((select) => {
		return {
			currentMailer: select('quillSMTP/core').getCurrentMailer(),
			getConnection: select('quillSMTP/core').getConnection,
		};
	});

	// dispatch.
	const connection = getConnection(connectionId);
	const { accounts } = currentMailer;
	const { addAccount, updateAccount, updateConnection } =
		useDispatch('quillSMTP/core');

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
	console.log(connection);

	return (
		<div className="mailer-connect-main__account-selector">
			{addingNewAccount && 'Adding new account. Please wait a moment...'}
			{size(accounts) > 0 && (
				<select
					value={connection.account_id}
					onChange={(e) => onChange(e.target.value)}
				>
					<option value="select">
						{__('Select an account', 'quillsmtp')}
					</option>
					{map(accounts, (account, id) => (
						<option
							key={id}
							value={id}
							selected={id === connection.account_id}
						>
							{account.name}
						</option>
					))}
					<option value="add">{__('Add new', 'quillsmtp')}</option>
				</select>
			)}
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
