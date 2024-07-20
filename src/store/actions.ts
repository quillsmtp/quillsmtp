/**
 * QuillSMTP Dependencies.
 */
import type { InitialPayload } from '@quillsmtp/config';

/**
 * Internal Dependencies.
 */
import {
	SETUP_STORE,
	SETUP_MAILER_APP,
	SETUP_MAILER_ACCOUNTS,
	ADD_MAILER_ACCOUNT,
	UPDATE_MAILER_ACCOUNT,
	DELETE_MAILER_ACCOUNT,
	ADD_CONNECTION,
	UPDATE_CONNECTION,
	DELETE_CONNECTION,
	ADD_NOTICE,
	DELETE_NOTICE,
	DELETE_CONNECTIONS,
	SET_INITIAL_ACCOUNT_DATA,
	UPDATE_TEMP_CONNECTION,
	REMOVE_ALL_TEMP_CONNECTIONS,
} from './constants';
import {
	CoreActionTypes,
	App,
	Account,
	Connection,
	Accounts,
	Notice,
	InitialAccountData,
} from './types';

/**
 * Setup Store Action.
 * @param {InitialPayload} initialPayload Initial payload object.
 * @returns {CoreActionTypes} Setup Store Action.
 */
export const setupStore = (
	initialPayload: InitialPayload
): CoreActionTypes => ({
	type: SETUP_STORE,
	initialPayload,
});

/**
 * Setup App Action.
 * @param {App} app App object.
 * @param {string} mailer Mailer slug.
 * @returns {CoreActionTypes} Setup App Action.
 */
export const setupApp = (mailer: string, app: App): CoreActionTypes => ({
	type: SETUP_MAILER_APP,
	mailer,
	app,
});

/**
 * Setup Accounts Action.
 * @param {string} mailer Mailer slug.
 * @param {Accounts} accounts Accounts array.
 * @returns {CoreActionTypes} Setup Accounts Action.
 */
export const setupAccounts = (
	mailer: string,
	accounts: Accounts
): CoreActionTypes => ({
	type: SETUP_MAILER_ACCOUNTS,
	mailer,
	accounts,
});

/**
 * Add Account Action.
 * @param {string} mailer Mailer slug.
 * @param {string} id Account ID.
 * @param {Account} account Account object.
 * @returns {CoreActionTypes} Add Account Action.
 */
export const addAccount = (
	mailer: string,
	id: string,
	account: Account
): CoreActionTypes => ({
	type: ADD_MAILER_ACCOUNT,
	mailer,
	id,
	account,
});

/**
 * Update Account Action.
 * @param {string} mailer Mailer slug.
 * @param {string} id Account ID.
 * @param {Partial<Account>} account Account object.
 * @returns {CoreActionTypes} Update Account Action.
 */
export const updateAccount = (
	mailer: string,
	id: string,
	account: Partial<Account>
): CoreActionTypes => ({
	type: UPDATE_MAILER_ACCOUNT,
	mailer,
	id,
	account,
});

/**
 * Delete Account Action.
 * @param {string} mailer Mailer slug.
 * @param {string} id Account ID.
 * @returns {CoreActionTypes} Delete Account Action.
 */
export const deleteAccount = (mailer: string, id: string): CoreActionTypes => ({
	type: DELETE_MAILER_ACCOUNT,
	mailer,
	id,
});

/**
 * Set Initial Account Data Action.
 * @param {InitialAccountData} initialAccountData Initial Account Data object.
 * @returns {CoreActionTypes} Set Initial Account Data Action.
 */
export const setInitialAccountData = (
	initialAccountData: InitialAccountData
): CoreActionTypes => ({
	type: SET_INITIAL_ACCOUNT_DATA,
	data: initialAccountData,
});

/**
 * Add Connection Action.
 * @param {string} id Connection ID.
 * @param {Connection} connection Connection object.
 * @param {boolean} permenant If Connection is permenant
 * 
 * @returns {CoreActionTypes} Add Connection Action.
 */
export const addConnection = (
	id: string,
	connection: Connection,
	permenant = true
): CoreActionTypes => ({
	type: ADD_CONNECTION,
	id,
	connection,
	permenant
});

/**
 * Update Connection Action.
 * @param {string} id Connection ID.
 * @param {Partial<Connection>} connection Connection object.
 * @param {boolean} recursive Recursive flag.
 * @returns {CoreActionTypes} Update Connection Action.
 */
export const updateConnection = (
	id: string,
	connection: Partial<Connection>
): CoreActionTypes => ({
	type: UPDATE_CONNECTION,
	id,
	connection,
});


/**
 * Update Temporary Connection Action.
 * @param {string} id Connection ID.
 * @param {Partial<Connection>} connection Connection object.
 * @param {boolean} recursive Recursive flag.
 * @returns {CoreActionTypes} Update Connection Action.
 */
export const updateTempConnection = (
	id: string,
	connection: Partial<Connection>
): CoreActionTypes => ({
	type: UPDATE_TEMP_CONNECTION,
	id,
	connection,
});

/**
 * Delete Connection Action.
 * @param {string} id Connection ID.
 * @returns {CoreActionTypes} Delete Connection Action.
 */
export const deleteConnection = (id: string): CoreActionTypes => ({
	type: DELETE_CONNECTION,
	id,
});

/**
 * Remove All Temporaray Connections Action.
 * 
 * @returns {CoreActionTypes} Delete Connections Action.
 */
export const removeAllTempConnections = (): CoreActionTypes => ({
	type: REMOVE_ALL_TEMP_CONNECTIONS,
});

/**
 * Delete Connections Action.
 * @param {string[]} ids Connection IDs.
 * @returns {CoreActionTypes} Delete Connections Action.
 */
export const deleteConnections = (ids: string[]): CoreActionTypes => ({
	type: DELETE_CONNECTIONS,
	ids,
});

/**
 * Add Notice Action.
 * @param {Notice} notice Notice.
 * @returns {CoreActionTypes} Add Notice Action.
 */
export const createNotice = (notice: Notice): CoreActionTypes => ({
	type: ADD_NOTICE,
	notice,
});

/**
 * Delete Notice Action.
 * @param {string} id Notice ID.
 * @returns {CoreActionTypes} Delete Notice Action.
 */
export const deleteNotice = (id: string): CoreActionTypes => ({
	type: DELETE_NOTICE,
	id,
});
