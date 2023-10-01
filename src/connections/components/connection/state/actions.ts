/**
 * Internal dependencies
 */
import {
	SETUP_CONNECTIONS,
	ADD_CONNECTION,
	UPDATE_CONNECTION,
	DELETE_CONNECTION,
} from './constants';
import type {
	Connections,
	Connection,
	ConnectionDeepPartial,
	ConnectionsActionTypes,
} from './types';

export default (dispatch: React.Dispatch<ConnectionsActionTypes>) => {
	return {
		setupConnections: (connections: Connections) => {
			dispatch({
				type: SETUP_CONNECTIONS,
				connections,
			});
		},
		addConnection: (id: string, connection: Connection) => {
			dispatch({
				type: ADD_CONNECTION,
				id,
				connection,
			});
		},
		updateConnection: (
			id: string,
			connection: ConnectionDeepPartial,
			recursive: boolean = true
		) => {
			dispatch({
				type: UPDATE_CONNECTION,
				id,
				connection,
				recursive,
			});
		},
		deleteConnection: (id: string) => {
			dispatch({
				type: DELETE_CONNECTION,
				id,
			});
		},
	};
};
