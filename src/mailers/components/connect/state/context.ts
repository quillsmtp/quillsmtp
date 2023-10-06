/**
 * WordPress Dependencies
 */
import { createContext, useContext } from 'react';
import { Provider } from '../../types';

/**
 * Internal Dependencies
 */
import { Account, Accounts } from './types';

const ConnectContext = createContext<{
	connectionId: string;
	provider: Provider;
	accounts: Accounts;
	setupApp: (app: any) => void;
	addAccount: (id: string, account: Account) => void;
	updateAccount: (id: string, account: Partial<Account>) => void;
	updatePayload: (key: string, value: any) => void;
	savePayload: (key: string) => void;
}>({
	connectionId: '',
	provider: {
		label: 'Provider',
		slug: 'provider',
	},
	accounts: {},
	setupApp: (_app: any) => {
		throw 'setupApp() not implemented.';
	},
	addAccount: (_id: string, _account: Account) => {
		throw 'addAccount() not implemented.';
	},
	updateAccount: (_id: string, _account: Partial<Account>) => {
		throw 'updateAccount() not implemented.';
	},
	updatePayload: (_key: string, _value: any) => {
		throw 'updatePayload() not implemented.';
	},
	savePayload: (_key: string) => {
		throw 'savePayload() not implemented.';
	},
});

const ConnectContextProvider = ConnectContext.Provider;
const useConnectContext = () => useContext(ConnectContext);

export { ConnectContext, ConnectContextProvider, useConnectContext };
