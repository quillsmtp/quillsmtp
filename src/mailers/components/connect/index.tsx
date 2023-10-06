/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useReducer, useRef } from 'react';

/**
 * Internal Dependencies
 */
import type { Provider, Setup as SetupType, ConnectMain } from '../types';
import reducer, { State } from './state/reducer';
import actions from './state/actions';
import { ConnectContextProvider } from './state/context';
import Setup from './setup';
import Main from './main';
import PrepareState from './utils/PrepareState';
import ConfigAPI from '../../../config';

interface Props {
	connectionId: string;
	provider: Provider;
	setup?: SetupType;
	main: ConnectMain;
	close: () => void;
}

const Connect: React.FC<Props> = ({
	connectionId,
	provider,
	setup,
	main,
	close,
}) => {
	const payload = ConfigAPI.getInitialPayload()?.mailers?.[provider.slug];
	const [state, dispatch] = useReducer(reducer, PrepareState(payload));
	const { app, accounts } = state;
	const stateRef = useRef<State>(state);
	stateRef.current = state;
	const $actions = actions(dispatch);

	// update payload.
	const updatePayload = (key: string, value: any) => {
		ConfigAPI.setInitialPayload({
			...ConfigAPI.getInitialPayload(),
			addons: {
				...ConfigAPI.getInitialPayload()?.mailers,
				[provider.slug]: {
					...payload,
					[key]: value,
				},
			},
		});
	};
	// save payload from state.
	const savePayload = (key: string) => {
		updatePayload(key, stateRef.current[key]);
	};

	// check if need setup.
	let needSetup = false;
	if (setup) {
		for (const [key, field] of Object.entries(setup.fields)) {
			if (field.check && !app[key]) {
				needSetup = true;
				break;
			}
		}
	}

	return (
		<div className="mailer-connect">
			<ConnectContextProvider
				value={{
					connectionId,
					provider,
					accounts,
					...$actions,
					updatePayload,
					savePayload,
				}}
			>
				{setup && needSetup ? (
					<Setup setup={setup} close={close} />
				) : (
					<Main
						connectionId={connectionId}
						main={main}
						close={close}
					/>
				)}
			</ConnectContextProvider>
		</div>
	);
};

export default Connect;
