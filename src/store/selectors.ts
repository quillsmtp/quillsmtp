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
	Notices,
	InitialAccountData,
} from './types';

/**
 * External Dependencies
 */
import createSelector from 'rememo';

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
 * Returns the temp connections object.
 * 
 * @param {State} state State.
 * 
 * @return {Connections} Temp Connections.
 */
export const getTempConnections = (state: State): Connections => {
	return state.tempConnections;
}

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
 * Returns the temp connection object.
 * 
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 * 
 * @return {Connection} Connection.
 * */
export const getTempConnection = (
	state: State,
	connectionId: string
): Connection => {
	const tempConnections = state.tempConnections;

	return tempConnections[connectionId];
}

/**
 * Get connectionsIds by account id.
 *
 * @param {State} state State.
 * @param {string} accountId Account ID.
 *
 * @return {string[]} Connections ids.
 */
export const getConnectionsIdsByAccountId = (
	state: State,
	accountId: string
): string[] => {
	const connections = state.connections;

	return Object.keys(connections).filter(
		(connectionId) => connections[connectionId].account_id === accountId
	);
};

/**
 * Returns the connection Mailer.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {string} Mailer.
 */
export const getConnectionMailer = (
	state: State,
	connectionId: string
): string => {
	const connections = state.connections;
	if (!connections[connectionId]) {
		return '';
	}
	return connections[connectionId].mailer;
};

/**
 * Returns the temp connection Mailer.
 *
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 *
 * @return {string} Mailer.
 */
export const getTempConnectionMailer = (
	state: State,
	connectionId: string
): string => {
	const tempConnections = state.tempConnections;
	if (!tempConnections[connectionId]) {
		return '';
	}
	return tempConnections[connectionId].mailer;
};

/**
 * Returns the connection Account Id.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {string} account id.
 */
export const getConnectionAccountId = (
	state: State,
	connectionId: string
): string | null => {
	const connections = state.connections;
	if (!connections[connectionId]) {
		return null;
	}

	return connections[connectionId].account_id || null;
};


/**
 * Returns the temp connection Account Id.
 * 
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 * 
 * @return {string} account id.
 */
export const getTempConnectionAccountId = (
	state: State,
	connectionId: string
): string | null => {
	const tempConnections = state.tempConnections;
	if (!tempConnections[connectionId]) {
		return null;
	}

	return tempConnections[connectionId].account_id || null;
}


/**
 * Get connection from email.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {string} From email.
 */
export const getConnectionFromEmail = (
	state: State,
	connectionId: string
): string => {
	const connections = state.connections;

	return connections[connectionId].from_email;
};

/**
 * Get temp connection from email.
 *
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 *
 * @return {string} From email.
 */
export const getTempConnectionFromEmail = (
	state: State,
	connectionId: string
): string => {
	const tempConnections = state.tempConnections;

	return tempConnections[connectionId].from_email;
}

/**
 * Get connection from name.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {string} From name.
 */
export const getConnectionFromName = (
	state: State,
	connectionId: string
): string => {
	const connections = state.connections;

	return connections[connectionId].from_name;
};

/**
 * Get temp connection from name.
 *
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 *
 * @return {string} From name.
 */
export const getTempConnectionFromName = (
	state: State,
	connectionId: string
): string => {
	const tempConnections = state.tempConnections;

	return tempConnections[connectionId].from_name;
}

/**
 * Get connection force from email.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {boolean} Force from email.
 */
export const getConnectionForceFromEmail = (
	state: State,
	connectionId: string
): boolean => {
	const connections = state.connections;

	return connections[connectionId].force_from_email;
};

/**
 * Get temp connection force from email.
 *
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 *
 * @return {boolean} Force from email.
 */
export const getTempConnectionForceFromEmail = (
	state: State,
	connectionId: string
): boolean => {
	const tempConnections = state.tempConnections;

	return tempConnections[connectionId].force_from_email;
}

/**
 * Get connection force from name.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {boolean} Force from name.
 */
export const getConnectionForceFromName = (
	state: State,
	connectionId: string
): boolean => {
	const connections = state.connections;

	return connections[connectionId].force_from_name;
};

/**
 * Get temp connection force from name.
 *
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 *
 * @return {boolean} Force from name.
 */
export const getTempConnectionForceFromName = (
	state: State,
	connectionId: string
): boolean => {
	const tempConnections = state.tempConnections;

	return tempConnections[connectionId].force_from_name;
}

/**
 * Get connection name.
 *
 * @param {State} state State.
 * @param {string} connectionId Connection ID.
 *
 * @return {boolean} Connection name.
 */
export const getConnectionName = (
	state: State,
	connectionId: string
): string => {
	const connections = state.connections;
	// if (!connections[connectionId]) {
	// 	return '';
	// }

	return connections[connectionId].name;
};

/**
 * Get temp connection name.
 *
 * @param {State} state State.
 * @param {string} connectionId Temp Connection ID.
 *
 * @return {boolean} Connection name.
 */
export const getTempConnectionName = (
	state: State,
	connectionId: string
): string => {
	const tempConnections = state.tempConnections;
	// if (!tempConnections[connectionId]) {
	// 	return '';
	// }

	return tempConnections[connectionId].name;
}

/**
 * Get connections ids list.
 *
 * @param {State} state State.
 *
 * @return {string[]} Connections ids.
 */
export const getConnectionsIds = createSelector(
	(state: State): string[] => {
		const connections = state.connections;

		return Object.keys(connections);
	},
	(state) => [Object.keys(state.connections).length]
);

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
 * Get initial account data.
 *
 * @param {State} state State.
 *
 * @return {InitialAccountData} Initial account data.
 */
export const getInitialAccountData = (state: State): InitialAccountData => {
	return state.initialAccountData;
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

/**
 * Get notices.
 *
 * @param {State} state State.
 *
 * @return {Notices} Notices.
 */
export const getNotices = (state: State): Notices => {
	return state.notices;
};
