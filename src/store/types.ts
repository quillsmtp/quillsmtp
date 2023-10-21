/**
 * QuillSMTP Dependencies.
 */
import type { InitialPayload } from '@quillsmtp/config';

/**
 * External dependencies
 */
import type { FunctionKeys } from 'utility-types';

/**
 * Internal Dependencies.
 */
import {
	SETUP_STORE,
	SETUP_MAILER_APP,
	ADD_MAILER_ACCOUNT,
	UPDATE_MAILER_ACCOUNT,
	ADD_CONNECTION,
	UPDATE_CONNECTION,
	DELETE_CONNECTION,
	DELETE_MAILER_ACCOUNT,
} from './constants';

export type CorePureState = {
	connections: Connections;
	mailers: Mailers;
};

export type Mailers = {
	[mailer: string]: Mailer;
};

export type Mailer = {
	accounts: Accounts;
	app: App;
};

interface setupStoreAction {
	type: typeof SETUP_STORE;
	initialPayload: InitialPayload;
}

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

type addConnection = {
	type: typeof ADD_CONNECTION;
	id: string;
	connection: Connection;
};

type updateConnection = {
	type: typeof UPDATE_CONNECTION;
	id: string;
	connection: ConnectionDeepPartial;
};

type deleteConnection = {
	type: typeof DELETE_CONNECTION;
	id: string;
};

export type App = {
	[x: string]: any;
};

type setupApp = {
	type: typeof SETUP_MAILER_APP;
	mailer: string;
	app: App;
};

export type AppActionTypes = setupApp | ReturnType<() => { type: 'NOOP' }>;

export type Accounts = {
	[accountId: string]: Account;
};

export type Account = {
	name: string;
};

type addAccount = {
	type: typeof ADD_MAILER_ACCOUNT;
	mailer: string;
	id: string;
	account: Account;
};

type updateAccount = {
	type: typeof UPDATE_MAILER_ACCOUNT;
	mailer: string;
	id: string;
	account: Partial<Account>;
};

type deleteAccount = {
	type: typeof DELETE_MAILER_ACCOUNT;
	mailer: string;
	id: string;
};

export type CoreActionTypes =
	| setupStoreAction
	| addConnection
	| updateConnection
	| deleteConnection
	| addAccount
	| updateAccount
	| deleteAccount
	| AppActionTypes
	| ReturnType<() => { type: 'NOOP' }>;

/**
 * Maps a "raw" selector object to the selectors available when registered on the @wordpress/data store.
 *
 * @template S Selector map, usually from `import * as selectors from './my-store/selectors';`
 */

export type SelectFromMap<S extends Record<string, unknown>> = {
	[selector in FunctionKeys<S>]: S[selector] extends (...args: any[]) => any
		? (...args: TailParameters<S[selector]>) => ReturnType<S[selector]>
		: never;
};

/**
 * Maps a "raw" actionCreators object to the actions available when registered on the @wordpress/data store.
 *
 * @template A Selector map, usually from `import * as actions from './my-store/actions';`
 */
export type DispatchFromMap<A extends Record<string, (...args: any[]) => any>> =
	{
		[actionCreator in keyof A]: (
			...args: Parameters<A[actionCreator]>
		) => A[actionCreator] extends (...args: any[]) => Generator
			? Promise<GeneratorReturnType<A[actionCreator]>>
			: void;
	};
/**
 * Parameters type of a function, excluding the first parameter.
 *
 * This is useful for typing some @wordpres/data functions that make a leading
 * `state` argument implicit.
 */
// eslint-disable-next-line @typescript-eslint/ban-types
export type TailParameters<F extends Function> = F extends (
	head: any,
	...tail: infer T
) => any
	? T
	: never;

/**
 * Obtain the type finally returned by the generator when it's done iterating.
 */
export type GeneratorReturnType<T extends (...args: any[]) => Generator> =
	T extends (...args: any) => Generator<any, infer R, any> ? R : never;
