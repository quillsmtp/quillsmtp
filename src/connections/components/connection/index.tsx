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
import { useState, useEffect } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

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
import Select, { SelectChangeEvent } from '@mui/material/Select';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';

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
	const [isFetching, setIsFetching] = useState(false);
	const [fromEmails, setFromEmails] = useState<any>([]);
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
	const fetchFromEmails = applyFilters(
		'QuillSMTP.Fetch.FromEmails',
		false,
		connection.mailer
	);
	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	useEffect(() => {
		if (fetchFromEmails) {
			getFromEmails();
		}
	}, [fetchFromEmails]);

	const getFromEmails = () => {
		if (isFetching || !connection.mailer || !connection.account_id) return;
		setIsFetching(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${connection.mailer}/settings/${connection.account_id}/from-emails`,
		})
			.then((res: any) => {
				if (res.success) {
					setFromEmails(res.options);
				}
			})
			.catch(() => {
				createNotice({
					type: 'error',
					message: __(
						'Error fetching from emails for this account.',
						'quillsmtp'
					),
				});
			})
			.finally(() => {
				setIsFetching(false);
			});
	};

	const save = () => {
		// check validity.
		if (!validate()) {
			return;
		}
		setIsSaving(true);
		const updatedConnection = { ...connection };
		if (fromEmails.length) {
			updatedConnection.from_email = getFromValue();
		}
		apiFetch({
			path: `/qsmtp/v1/settings`,
			method: 'POST',
			data: {
				connections: {
					...ConfigAPI.getInitialPayload().connections,
					[connectionId]: updatedConnection,
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
				createNotice({
					type: 'success',
					message: __('Settings saved successfully.', 'quillsmtp'),
				});
			} else {
				createNotice({
					type: 'error',
					message: __('Error saving settings.', 'quillsmtp'),
				});
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
				createNotice({
					type: 'success',
					message: __('Settings saved successfully.', 'quillsmtp'),
				});
			} else {
				createNotice({
					type: 'error',
					message: __('Error saving settings.', 'quillsmtp'),
				});
			}

			setIsDeleting(false);
		});
	};

	const validate = () => {
		if (!connection.name) {
			createNotice({
				type: 'error',
				message: __(
					'Please enter a name for this connection.',
					'quillsmtp'
				),
			});
			return false;
		}

		if (!connection.account_id && connection.mailer !== 'phpmailer') {
			createNotice({
				type: 'error',
				message: __(
					'Please select an account for this connection.',
					'quillsmtp'
				),
			});
			return false;
		}
		return true;
	};

	const getFromValue = () => {
		if (!fromEmails.length) return '';
		// Check if fromEmails has the from_email.
		for (const fromEmail of fromEmails) {
			if (fromEmail.value === from_email) {
				return from_email;
			}
		}

		// return the first from email.
		return fromEmails[0].value ?? '';
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
							{!fetchFromEmails && (
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
							)}
							{fetchFromEmails && (
								<FormControl sx={{ mb: 3 }} fullWidth required>
									<InputLabel>
										{__('From Email', 'quillsmtp')}
									</InputLabel>
									<Select
										value={getFromValue()}
										label={__('From Email', 'quillsmtp')}
										onChange={(e: SelectChangeEvent) =>
											updateConnection(connectionId, {
												from_email: e.target.value,
											})
										}
										required
										disabled={isFetching}
									>
										{fromEmails.map((option, index) => (
											<MenuItem
												key={index}
												value={option.value}
											>
												{option.label}
											</MenuItem>
										))}
									</Select>
									{__(
										'If left blank, the default WordPress from email will be used.',
										'quillsmtp'
									)}
								</FormControl>
							)}
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
