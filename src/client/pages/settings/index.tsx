/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { Grid } from '@mui/material';

/**
 * QuillSMTP dependencies
 */
import { ConnectionsList } from '@quillsmtp/connections';
import GeneralSettings from './general-settings';

/**
 * Internal dependencies
 */
import './style.scss';

const Settings = () => {
	return (
		<div className="qsmtp-settings-page">
			<Grid container maxWidth={'1200px'} margin={'auto'} spacing={2} >
				<Grid item xs={8}>
					<ConnectionsList />
				</Grid>
				<Grid item xs={4}>
					<GeneralSettings />
				</Grid>
			</Grid>
		</div>
	);
};

export default Settings;
