/**
 * External Dependencies
 */
import { cloneDeep } from 'lodash';

const PrepareState = (payload: any) => {
	const connections = isObject(payload?.connections)
		? (cloneDeep(payload?.connections) as { [id: string]: any })
		: {};

	return { connections };
};

const isObject = (variable: unknown): boolean => {
	return (
		typeof variable === 'object' &&
		variable !== null &&
		!Array.isArray(variable)
	);
};

export default PrepareState;
