/**
 * WordPress Dependencies
 */
import { createContext, useContext } from 'react';

/**
 * Internal Dependencies
 */
import { Connection, ConnectionDeepPartial, Connections } from './types';

const ConnectionContext = createContext<{
	connections: Connections;
	addConnection: (id: string, connection: Connection) => void;
	updateConnection: (
		id: string,
		connection: ConnectionDeepPartial,
		recursive?: boolean
	) => void;
	deleteConnection: (id: string) => void;
	updatePayload: (key: string, value: any) => void;
	savePayload: (key: string) => void;
}>({
	connections: {},
	addConnection: (_id: string, _connection: Connection) => {
		throw 'addConnection() not implemented.';
	},
	updateConnection: (
		_id: string,
		_connection: ConnectionDeepPartial,
		_recursive: boolean = true
	) => {
		throw 'updateConnection() not implemented.';
	},
	deleteConnection: (_id: string) => {
		throw 'deleteConnection() not implemented.';
	},
	updatePayload: (_key: string, _value: any) => {
		throw 'updatePayload() not implemented.';
	},
	savePayload: (_key: string) => {
		throw 'savePayload() not implemented.';
	},
});

const ConnectionContextProvider = ConnectionContext.Provider;
const useConnectionContext = () => useContext(ConnectionContext);

export { ConnectionContext, ConnectionContextProvider, useConnectionContext };
