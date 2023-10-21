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
	ADD_MAILER_ACCOUNT,
	UPDATE_MAILER_ACCOUNT,
	DELETE_MAILER_ACCOUNT,
	ADD_CONNECTION,
	UPDATE_CONNECTION,
	DELETE_CONNECTION,
} from './constants';
import { CoreActionTypes, App, Account, Connection } from './types';

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
