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
import CancelIcon from '@mui/icons-material/Cancel';
import ViewIcon from '@mui/icons-material/Visibility';

/**
 * Internal Dependencies
 */
import { Log } from '../types';
import './style.scss';
import { Button, Typography } from '@mui/material';

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
			case 'succeeded':
				return <Chip label={__('Sent', 'quillsmtp')} color="success" />;
			case 'failed':
				return <Chip label={__('Failed', 'quillsmtp')} color="error" />;
			default:
				return (
					<Chip label={__('Debug', 'quillsmtp')} color="default" />
				);
		}
	};

	let response = '';
	try {
		response = JSON.stringify(JSON.parse(log.response), null, 2);
	} catch (e) {
		// check if is string or not
		if (typeof log.response === 'string') {
			response = log.response;
		} else {
			response = JSON.stringify(log.response, null, 2);
		}
	}

	response = response.replace(/\\/g, '');

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
			<div className='flex'>
				<DialogTitle
					id="qsmtp-log-dialog-title"
					sx={{
						backgroundColor: 'white',
						padding: '10px 20px',
						border: '1px solid #ddd',
						marginBottom: '20px',
						width: "100%"
					}}
					className='flex justify-between'
				>
					<div className='flex gap-2 items-center font-roboto'>
						<ViewIcon className='text-[white] bg-[#333333] font-roboto rounded-full p-[0.15rem]' fontSize='medium' />
						{__('Email Log Details')}
					</div>
					<DialogActions className='cursor-pointer'>
						<CancelIcon onClick={onClose} color="primary" className='text-[#333333]' />
					</DialogActions>
				</DialogTitle>
			</div>
			<DialogContent>
				<DialogContentText id="qsmtp-log-dialog-description">
					<Grid container spacing={2}>
						<Grid item xs={12} md={6}>
							<Stack spacing={0}>
								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 bg-[#f8f7f7] text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>Status:</Typography>
									<Chip
										label={log.status}
										sx={{
											backgroundColor: log.status === "succeeded" ? "#03A32C33" : "#E9383833",
											color: log.status === "succeeded" ? "#03A32C" : "#E93838",
										}}
										className='bg-opacity-20 font-roboto capitalize'
									/>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>Opened:</Typography>
									<Typography className='font-roboto'>{log.status === "succeeded" ? "Yes" : "No"}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 bg-[#f8f7f7] text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>Subject:</Typography>
									<Typography className='font-roboto'>{log.subject}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>To:</Typography>
									<Typography className='font-roboto'>{log.recipients.to}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 bg-[#f8f7f7] text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>From:</Typography>
									<Typography className='font-roboto'>{log.from}</Typography>
								</Stack>
							</Stack>
						</Grid>
						<Grid item xs={12} md={6} sx={{marginBottom:"20px"}}>
							<Stack spacing={0}>
								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 bg-[#f8f7f7] text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>Date Time:</Typography>
									<Typography className='font-roboto'>{log.datetime}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>Connection Name:</Typography>
									<Typography className='font-roboto'>{log.connection_name}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 bg-[#f8f7f7] text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>Mailer:</Typography>
									<Typography className='font-roboto'>{log.provider_name}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>Reply To:</Typography>
									<Typography className='font-roboto'>{log.recipients.reply_to || "-"}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 bg-[#f8f7f7] text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>BCC:</Typography>
									<Typography className='font-roboto'>{log.recipients.bcc || "-"}</Typography>
								</Stack>

								<Stack direction="row" justifyContent="space-between" className='border border-[#d2d1d1] p-3 text-[#333333]'>
									<Typography fontWeight="bold" className='font-roboto'>CC:</Typography>
									<Typography className='font-roboto'>{log.recipients.cc || "-"}</Typography>
								</Stack>
							</Stack>
						</Grid>
					</Grid>
					<Accordion defaultExpanded={true}>
						<AccordionSummary expandIcon={<ExpandMoreIcon />} className='font-roboto text-[#333333]'>
							{__('Email Body', 'quillsmtp')}
						</AccordionSummary>
						<AccordionDetails>
							<div
								dangerouslySetInnerHTML={{
									__html: log.body,
								}}
							></div>
						</AccordionDetails>
					</Accordion>
					<Box
						mt={2}
						sx={{
							borderTop: '2px solid rgba(0, 0, 0, .125)',
							padding: '10px 0',
						}}
						className="flex items-center justify-between font-roboto"
					>
						<div className="log-modal__label text-[#333333]">
							{__('Headers', 'quillsmtp')}
						</div>
						<div className="log-modal__value">
							<pre>{JSON.stringify(log.headers, null, 2)}</pre>
						</div>
					</Box>
					<Box
						mt={2}
						sx={{
							borderTop: '2px solid rgba(0, 0, 0, .125)',
							padding: '10px 0',
						}}
						className="flex justify-between items-center font-roboto"
					>
						<div className="log-modal__label text-[#333333]">
							{__('Server Response', 'quillsmtp')}
						</div>
						<div className={`log-model__value ${log?.status === "succeeded"
							? "text-[#03A32C]"
							: "text-[#F35A5A]"
							}`}>
							<pre>{response}</pre>
						</div>
					</Box>
					<Accordion>
						<AccordionSummary expandIcon={<ExpandMoreIcon />} className='text-[#333333]'>
							{sprintf(
								/* translators: %s: Attachment Count */
								__('Attachment (%s)', 'quillsmtp'),
								log.attachments.length
							)}
						</AccordionSummary>
						<AccordionDetails>
							{log.attachments.length > 0 ? (
								<ol className="log-modal__attachments">
									{log.attachments.map(
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
