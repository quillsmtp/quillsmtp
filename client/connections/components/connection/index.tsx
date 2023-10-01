/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useReducer, useRef } from 'react';

/**
 * Internal Dependencies
 */
import reducer, { State } from './state/reducer';
import ConfigAPI from '../../../config';
import PrepareState from './utils/PrepareState';
import actions from './state/actions';
import { ConnectionContextProvider } from './state/context';

interface Props {
	children: React.ReactNode;
}

const ConnectionProvider: React.FC<Props> = ({ children }) => {
	const payload = ConfigAPI.getInitialPayload();
	const [state, dispatch] = useReducer(reducer, PrepareState(payload));
	const { connections } = state;
	const stateRef = useRef<State>(state);
	stateRef.current = state;
	const $actions = actions(dispatch);

	// update payload.
	const updatePayload = (key: string, value: any) => {
		ConfigAPI.setInitialPayload({
			...ConfigAPI.getInitialPayload(),
			[key]: value,
		});
	};
	// save payload from state.
	const savePayload = (key: string) => {
		updatePayload(key, stateRef.current[key]);
	};

	return (
		<ConnectionContextProvider
			value={{
				connections,
				...$actions,
				updatePayload,
				savePayload,
			}}
		>
			{children}
		</ConnectionContextProvider>
	);
};

export default ConnectionProvider;
