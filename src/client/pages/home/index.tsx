/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import Stack from '@mui/material/Stack';

/**
 * QuillSMTP dependencies
 */
import { ConnectionsList } from '@quillsmtp/connections';
import GeneralSettings from './general-settings';

/**
 * Internal dependencies
 */
import './style.scss';

const Home = () => {
	return (
		<div className="qsmtp-home-page">
			<Stack alignItems={'center'} spacing={2}>
				<GeneralSettings />
				<ConnectionsList />
			</Stack>
		</div>
	);
};

export default Home;
