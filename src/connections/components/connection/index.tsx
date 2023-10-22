/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { useState } from '@wordpress/element';

/**
 * External Dependencies
 */
import { styled } from '@mui/material/styles';
import MuiAccordion, { AccordionProps } from '@mui/material/Accordion';
import MuiAccordionSummary, {
	AccordionSummaryProps,
} from '@mui/material/AccordionSummary';
import MuiAccordionDetails from '@mui/material/AccordionDetails';
import TextField from '@mui/material/TextField';
import Checkbox from '@mui/material/Checkbox';
import LoadingButton from '@mui/lab/LoadingButton';
import SaveIcon from '@mui/icons-material/Save';
import DeleteIcon from '@mui/icons-material/Delete';
import Stack from '@mui/material/Stack';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import Box from '@mui/material/Box';
import FormControlLabel from '@mui/material/FormControlLabel';
import { FormControl, FormHelperText } from '@mui/material';

/**
 * Internal dependencies.
 */
import MailersSelector from './mailer-selector';

const Accordion = styled((props: AccordionProps) => (
	<MuiAccordion disableGutters elevation={0} square {...props} />
))(({ theme }) => ({
	border: `1px solid ${theme.palette.divider}`,
	'&:not(:last-child)': {
		borderBottom: 0,
	},
	'&:before': {
		display: 'none',
	},
}));

const AccordionSummary = styled((props: AccordionSummaryProps) => (
	<MuiAccordionSummary {...props} />
))(({ theme }) => ({
	backgroundColor:
		theme.palette.mode === 'dark'
			? 'rgba(255, 255, 255, .05)'
			: 'rgba(0, 0, 0, .03)',
}));

const AccordionDetails = styled(MuiAccordionDetails)(({ theme }) => ({
	padding: theme.spacing(2),
	borderTop: '1px solid rgba(0, 0, 0, .125)',
}));

interface Props {
	connectionId: string;
	index: number;
}

const Connection: React.FC<Props> = ({ connectionId, index }) => {
	const [isSaving, setIsSaving] = useState(false);
	const [isDeleting, setIsDeleting] = useState(false);
	const { connections } = useSelect((select) => {
		return {
			connections: select('quillSMTP/core').getConnections(),
		};
	});
	const { updateConnection, deleteConnection } =
		useDispatch('quillSMTP/core');
	const connection = connections[connectionId];
	const { from_email, force_from_email, from_name, force_from_name } =
		connection;

	// dispatch notices.
	const { createSuccessNotice, createErrorNotice } =
		useDispatch('core/notices');

	const save = () => {
		// check validity.
		if (!validate()) {
			return;
		}
		setIsSaving(true);
		apiFetch({
			path: `/qsmtp/v1/settings`,
			method: 'POST',
			data: {
				connections: {
					...ConfigAPI.getInitialPayload().connections,
					[connectionId]: connection,
				},
			},
		}).then((res: any) => {
			if (res.success) {
				ConfigAPI.setInitialPayload({
					...ConfigAPI.getInitialPayload(),
					connections: {
						...ConfigAPI.getInitialPayload().connections,
						[connectionId]: connection,
					},
				});
				createSuccessNotice(
					('✅ ' +
						__(
							'Settings saved successfully.',
							'quillsmtp'
						)) as string,
					{
						type: 'snackbar',
						isDismissible: true,
					}
				);
			} else {
				createErrorNotice(
					('❌ ' +
						__('Error saving settings.', 'quillsmtp')) as string,
					{
						type: 'snackbar',
						isDismissible: true,
					}
				);
			}
			setIsSaving(false);
		});
	};

	const remove = () => {
		// First check if this is connection in stored in the initial payload.
		// If so, we need to remove it from the initial payload.
		const initialPayload = ConfigAPI.getInitialPayload();
		if (!initialPayload.connections[connectionId]) {
			deleteConnection(connectionId);
			return;
		}

		const newConnections = { ...initialPayload.connections };
		delete newConnections[connectionId];
		setIsDeleting(true);
		apiFetch({
			path: `/qsmtp/v1/settings`,
			method: 'POST',
			data: {
				connections: newConnections,
			},
		}).then((res: any) => {
			if (res.success) {
				ConfigAPI.setInitialPayload({
					...ConfigAPI.getInitialPayload(),
					connections: newConnections,
				});
				deleteConnection(connectionId);
				createSuccessNotice(
					('✅ ' +
						__(
							'Settings saved successfully.',
							'quillsmtp'
						)) as string,
					{
						type: 'snackbar',
						isDismissible: true,
					}
				);
			} else {
				createErrorNotice(
					('❌ ' +
						__('Error saving settings.', 'quillsmtp')) as string,
					{
						type: 'snackbar',
						isDismissible: true,
					}
				);
			}

			setIsDeleting(false);
		});
	};

	const validate = () => {
		if (!connection.name) {
			createErrorNotice(
				__('Please enter a name for this connection.', 'quillsmtp'),
				{
					type: 'snackbar',
					isDismissible: true,
				}
			);
			return false;
		}
		return true;
	};

	return (
		<Accordion
			className="qsmtp-connection-options"
			defaultExpanded={index === 0}
		>
			<AccordionSummary
				expandIcon={<ExpandMoreIcon />}
				aria-controls="panel1a-content"
				id="panel1a-header"
			>
				{connection.name}
			</AccordionSummary>
			<AccordionDetails>
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
						id="name"
						label={__('Name', 'quillsmtp')}
						value={connection.name}
						onChange={(e) =>
							updateConnection(connectionId, {
								name: e.target.value,
							})
						}
						variant="outlined"
						fullWidth
						sx={{ mb: 2 }}
					/>
					<MailersSelector connectionId={connectionId} />
					{connection.mailer && (
						<>
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
								sx={{ mb: 2 }}
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
											onChange={() =>
												updateConnection(connectionId, {
													force_from_email:
														!force_from_email,
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
								sx={{ mb: 2 }}
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
											onChange={() =>
												updateConnection(connectionId, {
													force_from_name:
														!force_from_name,
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
						</>
					)}
				</Box>
				<Stack direction="row" justifyContent={'space-between'}>
					<LoadingButton
						variant="contained"
						onClick={save}
						loading={isSaving}
						loadingPosition="start"
						startIcon={<SaveIcon />}
					>
						{__('Save', 'quillsmtp')}
					</LoadingButton>
					<LoadingButton
						variant="contained"
						onClick={remove}
						loading={isDeleting}
						loadingPosition="start"
						startIcon={<DeleteIcon />}
						color="error"
					>
						{__('Delete', 'quillsmtp')}
					</LoadingButton>
				</Stack>
			</AccordionDetails>
		</Accordion>
	);
};

export default Connection;
