import {
	SETUP_CONNECTIONS,
	ADD_CONNECTION,
	UPDATE_CONNECTION,
	DELETE_CONNECTION,
} from './constants';

type DeepPartial<T> = {
	[P in keyof T]?: DeepPartial<T[P]>;
};
export type ConnectionDeepPartial = DeepPartial<Connection>;

export type Connections = {
	[connectionId: string]: Connection;
};

export type Connection = {
	name: string;
	from_name: string;
	force_from_name: boolean;
	from_email: string;
	force_from_email: boolean;
	mailer: string;
	account_id?: string;
};

type setupConnections = {
	type: typeof SETUP_CONNECTIONS;
	connections: Connections;
};

type addConnection = {
	type: typeof ADD_CONNECTION;
	id: string;
	connection: Connection;
};

type updateConnection = {
	type: typeof UPDATE_CONNECTION;
	id: string;
	connection: ConnectionDeepPartial;
	recursive: boolean;
};

type deleteConnection = {
	type: typeof DELETE_CONNECTION;
	id: string;
};

export type ConnectionsActionTypes =
	| setupConnections
	| addConnection
	| updateConnection
	| deleteConnection
	| ReturnType<() => { type: 'NOOP' }>;
