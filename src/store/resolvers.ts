/**
 * QuillSMTP Dependencies.
 */
import ConfigAPI from '@quillsmtp/config';

/**
 * Internal Dependencies.
 */
import { setupStore } from './actions';

export const getConnections = () => {
	const initialPayload = ConfigAPI.getInitialPayload();
	console.log('initialPayload', initialPayload);

	return setupStore(initialPayload);
};

export const getMailers = () => {
	const initialPayload = ConfigAPI.getInitialPayload();

	return setupStore(initialPayload);
};
