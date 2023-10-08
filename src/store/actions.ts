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
	SET_CURRENT_MAILER_PROVIDER,
	ADD_MAILER_ACCOUNT,
	UPDATE_MAILER_ACCOUNT,
	SET_CURRENT_CONNECTION_ID,
	ADD_CONNECTION,
	UPDATE_CONNECTION,
	DELETE_CONNECTION,
} from './constants';
import {
	CoreActionTypes,
	App,
	Account,
	Connection,
	MailerProvider,
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
 * @returns {CoreActionTypes} Setup App Action.
 */
export const setupApp = (app: App): CoreActionTypes => ({
	type: SETUP_MAILER_APP,
	app,
});

/**
 * Set Current Mailer Action.
 * @param {MailerProvider} mailer Mailer slug.
 * @returns {CoreActionTypes} Set Current Mailer Action.
 */
export const setCurrentMailerProvider = (
	mailer: MailerProvider
): CoreActionTypes => ({
	type: SET_CURRENT_MAILER_PROVIDER,
	mailer,
});

/**
 * Set Current Connection Action.
 * @param {string} connectionId Connection ID.
 * @returns {CoreActionTypes} Set Current Connection Action.
 */
export const setCurrentConnectionId = (
	connectionId: string
): CoreActionTypes => ({
	type: SET_CURRENT_CONNECTION_ID,
	connectionId,
});

/**
 * Add Account Action.
 * @param {string} id Account ID.
 * @param {Account} account Account object.
 * @returns {CoreActionTypes} Add Account Action.
 */
export const addAccount = (id: string, account: Account): CoreActionTypes => ({
	type: ADD_MAILER_ACCOUNT,
	id,
	account,
});

/**
 * Update Account Action.
 * @param {string} id Account ID.
 * @param {Partial<Account>} account Account object.
 * @returns {CoreActionTypes} Update Account Action.
 */
export const updateAccount = (
	id: string,
	account: Partial<Account>
): CoreActionTypes => ({
	type: UPDATE_MAILER_ACCOUNT,
	id,
	account,
});

/**
 * Add Connection Action.
 * @param {string} id Connection ID.
 * @param {Connection} connection Connection object.
 * @returns {CoreActionTypes} Add Connection Action.
 */
export const addConnection = (
	id: string,
	connection: Connection
): CoreActionTypes => ({
	type: ADD_CONNECTION,
	id,
	connection,
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
 * Delete Connection Action.
 * @param {string} id Connection ID.
 * @returns {CoreActionTypes} Delete Connection Action.
 */
export const deleteConnection = (id: string): CoreActionTypes => ({
	type: DELETE_CONNECTION,
	id,
});
