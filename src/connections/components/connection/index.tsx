/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * External Dependencies
 */
import TextField from '@mui/material/TextField';
import Checkbox from '@mui/material/Checkbox';
import LoadingButton from '@mui/lab/LoadingButton';
import SaveIcon from '@mui/icons-material/Save';
import Box from '@mui/material/Box';
import FormControlLabel from '@mui/material/FormControlLabel';
import { FormControl, FormHelperText } from '@mui/material';

/**
 * Internal dependencies.
 */
import MailersSelector from './mailer-selector';

interface Props {
	connectionId: string;
}

const Options: React.FC<Props> = ({ connectionId }) => {
	const { connections } = useSelect((select) => {
		return {
			connections: select('quillSMTP/core').getConnections(),
		};
	});
	const { updateConnection } = useDispatch('quillSMTP/core');
	const connection = connections[connectionId];
	if (!connection) return null;
	const {
		from_email,
		force_from_email,
		from_name,
		force_from_name,
		mailer,
		account_id,
	} = connection;

	return (
		<div className="qsmtp-connection-options">
			<Box
				sx={{
					display: 'flex',
					flexDirection: 'column',
					mt: 2,
					mb: 2,
				}}
				component="div"
			>
				<TextField
					id="from_email"
					label={__('From Email', 'quillsmtp')}
					value={from_email}
					onChange={(e) =>
						updateConnection(connectionId, {
							from_email: e.target.value,
						})
					}
					variant="outlined"
					fullWidth
					sx={{ mb: 2, width: '700px', maxWidth: '100%' }}
					helperText={__(
						'If left blank, the default WordPress from email will be used.',
						'quillsmtp'
					)}
				/>
				<FormControl sx={{ mb: 3 }}>
					<FormControlLabel
						control={
							<Checkbox
								checked={force_from_email}
								onChange={(e) =>
									updateConnection(connectionId, {
										force_from_email: !force_from_email,
									})
								}
							/>
						}
						label={__('Force From Email', 'quillsmtp')}
					/>
					<FormHelperText>
						{__(
							'If enabled, the from email will be forced to the above email.',
							'quillsmtp'
						)}
					</FormHelperText>
				</FormControl>
				<TextField
					sx={{ mb: 2, width: '700px', maxWidth: '100%' }}
					label={__('From Name', 'quillsmtp')}
					value={from_name}
					onChange={(e) =>
						updateConnection(connectionId, {
							from_name: e.target.value,
						})
					}
					variant="outlined"
					fullWidth
					helperText={__(
						'If left blank, the default WordPress from name will be used.',
						'quillsmtp'
					)}
				/>
				<FormControl sx={{ mb: 3 }}>
					<FormControlLabel
						control={
							<Checkbox
								checked={force_from_name}
								onChange={(e) =>
									updateConnection(connectionId, {
										force_from_name: !force_from_name,
									})
								}
							/>
						}
						label={__('Force From Name', 'quillsmtp')}
					/>
					<FormHelperText>
						{__(
							'If enabled, the from name will be forced to the above name.',
							'quillsmtp'
						)}
					</FormHelperText>
				</FormControl>
				<MailersSelector connectionId={connectionId} />
			</Box>
		</div>
	);
};

export default Options;
