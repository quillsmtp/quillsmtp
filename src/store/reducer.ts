/* eslint-disable no-nested-ternary */
/**
 * External dependencies
 */
import { cloneDeep, forEach } from 'lodash';
import type { Reducer } from 'redux';

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
import { CorePureState, CoreActionTypes } from './types';

// Initial State
const initialState: CorePureState = {
	currentMailerProvider: {
		slug: '',
		title: '',
	},
	currentMailer: {
		accounts: {},
		app: {},
	},
	currentConnectionId: 'default',
	connections: {},
	mailers: {},
};

// Reducer.

/**
 * Reducer returning the core data object.
 *
 * @param {CorePureState}  state  Current state.
 * @param {CoreActionTypes} action Dispatched action.
 *
 * @return {CorePureState} Updated state.
 */
const reducer: Reducer<CorePureState, CoreActionTypes> = (
	state = initialState,
	action
) => {
	switch (action.type) {
		case SETUP_STORE: {
			const { initialPayload } = action;
			const { connections, mailers } = initialPayload;
			return {
				...state,
				connections,
				mailers,
			};
		}
		case SETUP_MAILER_APP: {
			const { app } = action;
			const { mailers, currentMailerProvider } = state;
			return {
				...state,
				mailers: {
					...mailers,
					[currentMailerProvider.slug]: {
						...mailers[currentMailerProvider.slug],
						app,
					},
				},
			};
		}
		case SET_CURRENT_MAILER_PROVIDER: {
			const { mailer } = action;
			return {
				...state,
				currentMailerProvider: mailer,
			};
		}
		case ADD_MAILER_ACCOUNT: {
			const { id, account } = action;
			const { mailers, currentMailerProvider } = state;
			const { accounts } = mailers[currentMailerProvider.slug];
			return {
				...state,
				mailers: {
					...mailers,
					[currentMailerProvider.slug]: {
						...mailers[currentMailerProvider.slug],
						accounts: {
							...accounts,
							[id]: account,
						},
					},
				},
			};
		}
		case UPDATE_MAILER_ACCOUNT: {
			const { id, account } = action;
			const { mailers, currentMailerProvider } = state;
			const { accounts } = mailers[currentMailerProvider.slug];
			const updatedAccount = {
				...accounts[id],
				...account,
			};
			return {
				...state,
				mailers: {
					...mailers,
					[currentMailerProvider.slug]: {
						...mailers[currentMailerProvider.slug],
						accounts: {
							...accounts,
							[id]: updatedAccount,
						},
					},
				},
			};
		}
		case SET_CURRENT_CONNECTION_ID: {
			const { connectionId } = action;
			return {
				...state,
				currentConnection: connectionId,
			};
		}
		case ADD_CONNECTION: {
			const { id, connection } = action;
			const { connections } = state;
			return {
				...state,
				connections: {
					...connections,
					[id]: connection,
				},
			};
		}
		case UPDATE_CONNECTION: {
			const { id, connection } = action;
			const { connections } = state;
			const updatedConnection = {
				...connections[id],
				...connection,
			};
			return {
				...state,
				connections: {
					...connections,
					[id]: updatedConnection,
				},
			};
		}
		case DELETE_CONNECTION: {
			const { id } = action;
			const { connections } = state;
			const updatedConnections = cloneDeep(connections);
			// @ts-ignore.
			forEach(updatedConnections, (connection, connectionId) => {
				if (connectionId === id) {
					delete updatedConnections[connectionId];
				}
			});
			return {
				...state,
				connections: updatedConnections,
			};
		}
		default:
			return state;
	}
};

export type State = ReturnType<typeof reducer>;
export default reducer;
