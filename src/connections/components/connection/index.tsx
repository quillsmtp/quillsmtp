/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { select, useSelect, useDispatch } from '@wordpress/data';
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
import LoadingButton from '@mui/lab/LoadingButton';
import SaveIcon from '@mui/icons-material/Save';
import DeleteIcon from '@mui/icons-material/Delete';
import Stack from '@mui/material/Stack';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import Box from '@mui/material/Box';

/**
 * Internal dependencies.
 */
import MailersSelector from './mailer-selector';
import FromEmail from './from-email';
import ForceFromEmail from './force-from-email';
import FromName from './from-name';
import ForceFromName from './force-from-name';

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
	const { mailer, account_id, name } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getConnectionMailer(connectionId),
			account_id:
				select('quillSMTP/core').getConnectionAccountId(connectionId),
			name: select('quillSMTP/core').getConnectionName(connectionId),
		};
	});
	const { updateConnection, deleteConnection } =
		useDispatch('quillSMTP/core');

	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	const save = () => {
		// check validity.
		if (!validate()) {
			return;
		}
		setIsSaving(true);
		const connection = select('quillSMTP/core').getConnection(connectionId);

		const updatedConnection = { ...connection };
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
		if (!name) {
			createNotice({
				type: 'error',
				message: __(
					'Please enter a name for this connection.',
					'quillsmtp'
				),
			});
			return false;
		}

		if (!account_id && mailer !== 'phpmailer') {
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
				{name}
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
						value={name}
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
					{mailer && (
						<>
							<FromEmail connectionId={connectionId} />
							<ForceFromEmail connectionId={connectionId} />
							<FromName connectionId={connectionId} />
							<ForceFromName connectionId={connectionId} />
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
