/* eslint-disable no-nested-ternary */
/**
 * External dependencies
 */
import { cloneDeep, forEach, size } from 'lodash';
import type { Reducer } from 'redux';

/**
 * Internal Dependencies.
 */
import {
	SETUP_STORE,
	SETUP_MAILER_APP,
	SETUP_MAILER_ACCOUNTS,
	ADD_MAILER_ACCOUNT,
	UPDATE_MAILER_ACCOUNT,
	ADD_CONNECTION,
	UPDATE_CONNECTION,
	DELETE_CONNECTION,
	DELETE_MAILER_ACCOUNT,
	ADD_NOTICE,
	DELETE_NOTICE,
	DELETE_CONNECTIONS,
	SET_INITIAL_ACCOUNT_DATA,
	UPDATE_TEMP_CONNECTION,
	REMOVE_ALL_TEMP_CONNECTIONS,
} from './constants';
import { CorePureState, CoreActionTypes } from './types';

// Initial State
const initialState: CorePureState = {
	connections: {},
	tempConnections: {},
	mailers: {},
	notices: {},
	initialAccountData: {},
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
				connections:
					size(connections) > 0 ? connections : state.connections,
				mailers,
			};
		}
		case SETUP_MAILER_APP: {
			const { mailer, app } = action;
			const { mailers } = state;

			return {
				...state,
				mailers: {
					...mailers,
					[mailer]: {
						...mailers[mailer],
						app,
					},
				},
			};
		}
		case SETUP_MAILER_ACCOUNTS: {
			const { mailer, accounts } = action;
			const { mailers } = state;

			return {
				...state,
				mailers: {
					...mailers,
					[mailer]: {
						...mailers[mailer],
						accounts,
					},
				},
			};
		}
		case ADD_MAILER_ACCOUNT: {
			const { mailer, id, account } = action;
			const { mailers } = state;
			const { accounts } = mailers[mailer];

			return {
				...state,
				mailers: {
					...mailers,
					[mailer]: {
						...mailers[mailer],
						accounts: {
							...accounts,
							[id]: account,
						},
					},
				},
			};
		}
		case UPDATE_MAILER_ACCOUNT: {
			const { mailer, id, account } = action;
			const { mailers } = state;
			const { accounts } = mailers[mailer];
			const updatedAccount = {
				...accounts[id],
				...account,
			};

			return {
				...state,
				mailers: {
					...mailers,
					[mailer]: {
						...mailers[mailer],
						accounts: {
							...accounts,
							[id]: updatedAccount,
						},
					},
				},
			};
		}
		case DELETE_MAILER_ACCOUNT: {
			const { mailer, id } = action;
			const { mailers } = state;
			const { accounts } = mailers[mailer];
			const updatedAccounts = cloneDeep(accounts);
			// @ts-ignore.
			forEach(updatedAccounts, (account, accountId) => {
				if (accountId === id) {
					delete updatedAccounts[accountId];
				}
			});

			return {
				...state,
				mailers: {
					...mailers,
					[mailer]: {
						...mailers[mailer],
						accounts: updatedAccounts,
					},
				},
			};
		}
		case SET_INITIAL_ACCOUNT_DATA: {
			const { data } = action;

			return {
				...state,
				initialAccountData: data,
			};
		}
		case ADD_CONNECTION: {
			const { id, connection, permenant } = action;
			console.log('permenant', permenant);
			const { connections, tempConnections } = state;
			if (permenant)
				return {
					...state,
					connections: {
						...connections,
						[id]: connection,
					},
				};
			return {
				...state,
				tempConnections: {
					...tempConnections,
					[id]: connection,
				}
			}
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

		case UPDATE_TEMP_CONNECTION: {
			const { id, connection } = action;
			const { tempConnections } = state;
			const updatedTempConnection = {
				...tempConnections[id],
				...connection,
			};
			return {
				...state,
				tempConnections: {
					...tempConnections,
					[id]: updatedTempConnection,
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
		case DELETE_CONNECTIONS: {
			const { ids } = action;
			const { connections } = state;
			const updatedConnections = cloneDeep(connections);
			// @ts-ignore.
			forEach(updatedConnections, (connection, connectionId) => {
				if (ids.includes(connectionId)) {
					delete updatedConnections[connectionId];
				}
			});
			return {
				...state,
				connections: updatedConnections,
			};
		}

		case REMOVE_ALL_TEMP_CONNECTIONS: {
			return {
				...state,
				tempConnections: {},
			};
		}
		case ADD_NOTICE: {
			const { notice } = action;
			const { notices } = state;
			const randomId = () => Math.random().toString(36).substr(2, 9);
			const id = randomId();

			return {
				...state,
				notices: {
					...notices,
					[id]: notice,
				},
			};
		}
		case DELETE_NOTICE: {
			const { id } = action;
			const { notices } = state;
			const updatedNotices = cloneDeep(notices);
			// @ts-ignore.
			forEach(updatedNotices, (notice, noticeId) => {
				if (noticeId === id) {
					delete updatedNotices[noticeId];
				}
			});
			return {
				...state,
				notices: updatedNotices,
			};
		}
		default:
			return state;
	}
};

export type State = ReturnType<typeof reducer>;
export default reducer;
