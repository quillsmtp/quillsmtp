/**
 * External Dependencies
 */
import { cloneDeep } from 'lodash';

const PrepareState = (payload: any) => {
	const app = isObject(payload?.app) ? cloneDeep(payload?.app) : {};
	const accounts = isObject(payload?.accounts)
		? cloneDeep(payload?.accounts)
		: {};

	return {
		app,
		accounts,
	};
};

const isObject = (variable: unknown): boolean => {
	return (
		typeof variable === 'object' &&
		variable !== null &&
		!Array.isArray(variable)
	);
};

export default PrepareState;
