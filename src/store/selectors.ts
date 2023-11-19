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
	App,
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
 * Get mailer app.
 *
 * @param {State} state State.
 * @param {string} mailer Mailer slug.
 *
 * @return {App} Mailer app.
 */
export const getMailerApp = (state: State, mailer: string): App => {
	const mailers = state.mailers;

	return mailers[mailer].app;
};
