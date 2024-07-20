/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Internal Dependencies
 */
import { default as GenericSetup } from '../account-setup';
import Footer from '../footer';
import type { Setup as SetupType } from '../../types';
import './style.scss';

interface Props {
	connectionId: string;
	setup: SetupType;
}

const Setup: React.FC<Props> = ({ connectionId, setup }) => {
	const { mailer } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getTempConnectionMailer(connectionId),
		};
	});
	const { setupApp } = useDispatch('quillSMTP/core');

	const SetupControls: React.FC<{ submit: () => void }> = ({ submit }) => {
		return (
			<Footer
				save={{
					label: 'Save',
					onClick: submit,
					disabled: false,
				}}
			/>
		);
	};
	return (
		<GenericSetup
			connectionId={connectionId}
			Instructions={setup.Instructions}
			fields={setup.fields}
			Controls={SetupControls}
			onFinish={(app) => {
				setupApp(mailer, app);
			}}
		/>
	);
};

export default Setup;
