/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * External Dependencies
 */
import Dialog from '@mui/material/Dialog';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';

/**
 * Internal Dependencies.
 */
import ConfigAPI from '@quillsmtp/config';

const SettingsRender: React.FC<{ slug: string; connectionId: string }> = ({
	slug,
}) => {
	const [open, setOpen] = useState(true);
	const mailer = ConfigAPI.getStoreMailers()[slug];

	return (
		<Dialog open={open} onClose={() => setOpen(false)}>
			<DialogTitle>{__('Settings', 'quillsmtp')}</DialogTitle>
			<DialogContent>
				<DialogContentText>Test</DialogContentText>
			</DialogContent>
		</Dialog>
	);
};

export default SettingsRender;
