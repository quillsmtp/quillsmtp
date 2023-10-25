/**
 * WordPress Dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import styled from '@mui/material/styles/styled';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import Stack from '@mui/material/Stack';
import Grid from '@mui/material/Grid';
import MuiAccordion, { AccordionProps } from '@mui/material/Accordion';
import MuiAccordionSummary, {
	AccordionSummaryProps,
} from '@mui/material/AccordionSummary';
import MuiAccordionDetails from '@mui/material/AccordionDetails';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import MuiChip from '@mui/material/Chip';
import Box from '@mui/material/Box';

/**
 * Internal Dependencies
 */
import { Log } from '../types';
import './style.scss';

interface Props {
	log: Log | null;
	open: boolean;
	onClose: () => void;
}

const Accordion = styled((props: AccordionProps) => (
	<MuiAccordion disableGutters elevation={0} square {...props} />
))(({ theme }) => ({
	border: `1px solid ${theme.palette.divider}`,
	borderRight: 0,
	borderLeft: 0,
	marginTop: 10,
	fontWeight: theme.typography.fontWeightBold,
	color: '#606266',
	'&.Mui-expanded': {
		borderBottom: 0,
	},
}));

const AccordionSummary = styled((props: AccordionSummaryProps) => (
	<MuiAccordionSummary {...props} />
))(() => ({
	backgroundColor: 'transparent',
	paddingLeft: 0,
	paddingRight: 0,
}));

const AccordionDetails = styled(MuiAccordionDetails)(({ theme }) => ({
	borderTop: '1px solid rgba(0, 0, 0, .125)',
	padding: theme.spacing(2),
}));

const Chip = styled(MuiChip)(() => ({
	height: 22,
}));

const LogModal: React.FC<Props> = ({ log, open, onClose }) => {
	if (!log) return null;

	const getLogLevel = (level) => {
		switch (level) {
			case 'error':
				return <Chip label={__('Error', 'quillsmtp')} color="error" />;
			case 'info':
				return <Chip label={__('Sent', 'quillsmtp')} color="success" />;
			default:
				return (
					<Chip label={__('Debug', 'quillsmtp')} color="default" />
				);
		}
	};

	return (
		<Dialog
			open={open}
			onClose={onClose}
			aria-labelledby="qsmtp-log-dialog-title"
			aria-describedby="qsmtp-log-dialog-description"
			className="qsmtp-log-modal"
			maxWidth="lg"
			fullWidth
		>
			<DialogTitle
				id="qsmtp-log-dialog-title"
				sx={{
					backgroundColor: '#f5f5f5',
					padding: '10px 20px',
					border: '1px solid #ddd',
					marginBottom: '20px',
					fontWeight: 'lighter',
				}}
			>
				{__('Log Details')}
			</DialogTitle>
			<DialogContent>
				<DialogContentText id="qsmtp-log-dialog-description">
					<Grid container spacing={2}>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('Status', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{getLogLevel(log.level)}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('Date Time', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.datetime}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('Connection Name', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.connection_name}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('Mailer', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.provider}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('Subject', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.email_details.subject}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('From', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.email_details.from}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('To', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.email_details.to}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('Reply To', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.email_details.reply_to || '-'}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('CC', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.email_details.cc || '-'}
							</div>
						</Stack>
						<Stack
							direction="row"
							spacing={1}
							component={Grid}
							item
							xs={6}
						>
							<div className="log-modal__label">
								{__('BCC', 'quillsmtp')}:
							</div>
							<div className="log-modal__value">
								{log.context.email_details.bcc || '-'}
							</div>
						</Stack>
						{log.context.email_details.plain && (
							<Stack
								direction="row"
								spacing={1}
								component={Grid}
								item
								xs={12}
							>
								<div className="log-modal__label">
									{__('Email Body', 'quillsmtp')}:
								</div>
								<div className="log-modal__value">
									{log.context.email_details.plain}
								</div>
							</Stack>
						)}
					</Grid>
					<Accordion defaultExpanded={true}>
						<AccordionSummary expandIcon={<ExpandMoreIcon />}>
							{__('Email Body', 'quillsmtp')}
						</AccordionSummary>
						<AccordionDetails>
							<div
								dangerouslySetInnerHTML={{
									__html: log.context.email_details.html,
								}}
							></div>
						</AccordionDetails>
					</Accordion>
					<Box
						mt={2}
						sx={{
							borderTop: '1px solid rgba(0, 0, 0, .125)',
							padding: '10px 0',
						}}
					>
						<div className="log-modal__label">
							{__('Headers', 'quillsmtp')}
						</div>
						<div className="log-modal__value">
							<pre>
								{JSON.stringify(
									log.context.email_details.headers,
									null,
									2
								)}
							</pre>
						</div>
					</Box>
					<Accordion>
						<AccordionSummary expandIcon={<ExpandMoreIcon />}>
							{sprintf(
								/* translators: %s: Attachment Count */
								__('Attachment (%s)', 'quillsmtp'),
								log.context.email_details.attachments.length
							)}
						</AccordionSummary>
						<AccordionDetails>
							{log.context.email_details.attachments.length >
							0 ? (
								<ol className="log-modal__attachments">
									{log.context.email_details.attachments.map(
										(attachment, index) => (
											<li key={index}>{attachment}</li>
										)
									)}
								</ol>
							) : (
								<div className="log-modal__attachments">
									{__('No attachments', 'quillsmtp')}
								</div>
							)}
						</AccordionDetails>
					</Accordion>
				</DialogContentText>
			</DialogContent>
			<DialogActions>
				{/* <Button onClick={onClose}>Close</Button> */}
			</DialogActions>
		</Dialog>
	);
};

export default LogModal;
