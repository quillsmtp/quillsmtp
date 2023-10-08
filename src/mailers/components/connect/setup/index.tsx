/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';

/**
 * Internal Dependencies
 */
import { default as GenericSetup } from '../account-setup';
import Footer from '../footer';
import type { Setup as SetupType } from '../../types';
import './style.scss';

interface Props {
	setup: SetupType;
	close: () => void;
}

const Setup: React.FC<Props> = ({ setup, close }) => {
	const { setupApp } = useDispatch('quillSMTP/core');

	const SetupControls: React.FC<{ submit: () => void }> = ({ submit }) => {
		return (
			<Footer
				save={{
					label: 'Save',
					onClick: submit,
					disabled: false,
				}}
				close={{ label: 'Close', onClick: close }}
			/>
		);
	};
	return (
		<GenericSetup
			Instructions={setup.Instructions}
			fields={setup.fields}
			Controls={SetupControls}
			onFinish={(app) => {
				setupApp(app);
			}}
		/>
	);
};

export default Setup;
