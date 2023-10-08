/**
 * Internal Dependencies
 */
import { State } from './reducer';
import {
	Connections,
	Connection,
	Mailers,
	Account,
	Mailer,
	MailerProvider,
} from './types';

/**
 * Returns the connections object.
 *
 * @param {State} state State.
 *
 * @return {Connections} Connections.
 */
export const getConnections = (state: State): Connections => {
	return state.connections;
};

/**
 * Returns the connection object.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {Connection} Connection.
 */
export const getConnection = (
	state: State,
	connectionId: string
): Connection => {
	const connections = state.connections;

	return connections[connectionId];
};

/**
 * Returns the mailers object.
 *
 * @param {State} state State.
 *
 * @return {Mailers} Mailers.
 */
export const getMailers = (state: State): Mailers => {
	return state.mailers;
};

/**
 * Returns the mailer object by slug.
 *
 * @param {State} state State.
 * @param {string} mailer Mailer slug.
 *
 * @return {Mailer} Mailer.
 */
export const getMailer = (state: State, mailer: string): Mailer => {
	const mailers = state.mailers;
	console.log(mailers, mailer);

	return mailers[mailer];
};

/**
 * Returns the mailer account object.
 *
 * @param {State} state State.
 * @param {string} mailer Mailer slug.
 * @param {string} accountId Account ID.
 *
 * @return {Account} Mailer account.
 */
export const getMailerAccount = (
	state: State,
	mailer: string,
	accountId: string
): Account => {
	const mailers = state.mailers;

	return mailers[mailer].accounts[accountId];
};

/**
 * Returns the current connection.
 *
 * @param {State} state State.
 *
 * @return {Connection} Current connection.
 */
export const getCurrentConnection = (state: State): Connection => {
	const connections = state.connections;
	const currentConnection = state.currentConnectionId;

	return connections[currentConnection];
};

/**
 * Returns the current mailer slug.
 *
 * @param {State} state State.
 *
 * @return {MailerProvider} Current mailer slug.
 */
export const getCurrentMailerProvider = (state: State): MailerProvider => {
	return state.currentMailerProvider;
};

/**
 * Returns the current mailer.
 *
 * @param {State} state State.
 *
 * @return {Mailer} Current mailer.
 */
export const getCurrentMailer = (state: State): Mailer => {
	const mailers = state.mailers;
	const currentMailer = state.currentMailerProvider.slug;

	return mailers[currentMailer];
};

/**
 * Returns the current connection ID.
 *
 * @param {State} state State.
 *
 * @return {string} Current connection ID.
 */
export const getCurrentConnectionId = (state: State): string => {
	return state.currentConnectionId;
};
