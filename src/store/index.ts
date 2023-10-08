import { register, createReduxStore } from '@wordpress/data';
import reducer from './reducer';
import * as actions from './actions';
import * as selectors from './selectors';
import * as resolvers from './resolvers';
import { STORE_KEY } from './constants';
import type { State } from './reducer';

import type { DispatchFromMap, SelectFromMap } from './types';

const store: ReturnType<typeof createReduxStore> = createReduxStore<
	State,
	typeof actions,
	typeof selectors
>(STORE_KEY, {
	actions,
	selectors,
	reducer,
	resolvers,
});

export default store;
export * from './types';
register(store);

declare module '@wordpress/data' {
	function dispatch(key: typeof STORE_KEY): DispatchFromMap<typeof actions>;
	function select(key: typeof STORE_KEY): SelectFromMap<typeof selectors>;
	function useSelect<R>(
		selector: (customSelect: typeof select) => R,
		deps?: any[]
	): R;
	function useDispatch(
		key: typeof STORE_KEY
	): DispatchFromMap<typeof actions>;
}
