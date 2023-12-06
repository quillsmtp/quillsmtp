/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';
import { getMailerModules } from '@quillsmtp/mailers';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

/**
 * External dependencies
 */
import React from 'react';
import { size, map, keys } from 'lodash';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import LoadingButton from '@mui/lab/LoadingButton';
import SendIcon from '@mui/icons-material/Send';
import { TextField } from '@mui/material';
import FormControlLabel from '@mui/material/FormControlLabel';
import Switch from '@mui/material/Switch';
import FormHelperText from '@mui/material/FormHelperText';
import Snackbar from '@mui/material/Snackbar';
import MuiAlert, { AlertProps } from '@mui/material/Alert';

/**
 * Internal dependencies
 */
import './style.scss';

const Alert = React.forwardRef<HTMLDivElement, AlertProps>(
	function Alert(props, ref) {
		return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
	}
);

const EmailTest: React.FC = () => {
	const adminEmail = ConfigAPI.getAdminEmail();
	const [email, setEmail] = useState<string>(adminEmail);
	const [connection, setConnection] = useState<string>('');
	const [isHTML, setIsHTML] = useState<boolean>(true);
	const [isSending, setIsSending] = useState<boolean>(false);
	const [isSent, setIsSent] = useState<boolean>(false);
	const [isError, setIsError] = useState<boolean>(false);
	const { connections } = useSelect((select) => {
		return {
			connections: select('quillSMTP/core').getConnections(),
		};
	});
	const mailersModules = getMailerModules();

	// Ajax request to send the email.
	const sendEmail = () => {
		if (!connection || !email) return;
		setIsSending(true);
		const ajaxURL = ConfigAPI.getAjaxUrl();
		const nonce = ConfigAPI.getNonce();
		const body = new FormData();

		body.append('action', 'quillsmtp_send_test_email');
		body.append('nonce', nonce);
		body.append('email', email);
		body.append('connection', connection);
		body.append('content_type', isHTML ? 'html' : 'plain');

		// Send the email.
		fetch(ajaxURL, {
			method: 'POST',
			body,
		})
			.then((response: any) => {
				if (response.status !== 200) {
					throw new Error(response.statusText);
				}
				return response.json();
			})
			.then((response: any) => {
				if (response.success) {
					setIsSent(true);
				} else {
					setIsError(true);
				}
				setIsSending(false);
			})
			.catch((error) => {
				alert(error.message);
				setIsSending(false);
			});
	};

	return (
		<div className="qsmtp-email-test-page">
			<Card className="qsmtp-email-test-card" variant="outlined">
				<div className="qsmtp-email-test-header">
					<div className="qsmtp-email-test-header__title">
						{__('Send Test Email', 'quillsmtp')}
					</div>
				</div>
				<CardContent>
					<form
						onSubmit={(e) => {
							e.preventDefault();
							setIsSending(true);
						}}
					>
						<FormControl fullWidth sx={{ mb: 2 }}>
							<InputLabel id="qsmtp-general-settings-default-connection-label">
								{__('Default Connection', 'quillsmtp')}
							</InputLabel>
							<Select
								labelId="qsmtp-general-settings-default-connection-label"
								id="qsmtp-general-settings-default-connection"
								value={
									connection
										? connection
										: keys(connections).length > 0
										? keys(connections)[0]
										: ''
								}
								label={__('Default Connection', 'quillsmtp')}
								onChange={(event: SelectChangeEvent) => {
									setConnection(event.target.value);
								}}
							>
								{map(keys(connections), (key) => {
									return (
										<MenuItem value={key} key={key}>
											{`${connections[key].name}`}
											{connections[key].mailer && (
												<>
													{' '}
													-{' '}
													{
														mailersModules[
															connections[key]
																.mailer
														]?.title
													}
												</>
											)}
										</MenuItem>
									);
								})}
							</Select>
						</FormControl>
						<TextField
							label={__('Email Address', 'quillsmtp')}
							value={email}
							onChange={(event) => {
								setEmail(event.target.value);
							}}
							fullWidth
							sx={{ mb: 2 }}
							type="email"
						/>
						<FormControlLabel
							control={
								<Switch
									checked={isHTML}
									onChange={(event) => {
										setIsHTML(event.target.checked);
									}}
								/>
							}
							label={__('HTML Email', 'quillsmtp')}
							sx={{
								display: 'block',
							}}
						/>
						<FormHelperText sx={{ mb: 3 }}>
							{__(
								'Send the email as HTML or plain text.',
								'quillsmtp'
							)}
						</FormHelperText>
						<LoadingButton
							loading={isSending}
							type="submit"
							variant="contained"
							color="primary"
							disabled={size(connections) === 0 || !email}
							startIcon={<SendIcon />}
							onClick={() => {
								sendEmail();
							}}
						>
							{__('Send', 'quillsmtp')}
						</LoadingButton>
					</form>
				</CardContent>
			</Card>
			{isSent && (
				<Snackbar
					open={isSent}
					autoHideDuration={6000}
					onClose={() => {
						setIsSent(false);
					}}
					anchorOrigin={{
						vertical: 'bottom',
						horizontal: 'center',
					}}
				>
					<Alert
						onClose={() => {
							setIsSent(false);
						}}
						sx={{ width: '100%' }}
						severity="success"
					>
						{__('Email sent successfully.', 'quillsmtp')}
					</Alert>
				</Snackbar>
			)}
			{isError && (
				<Snackbar
					open={isError}
					autoHideDuration={6000}
					onClose={() => {
						setIsError(false);
					}}
					anchorOrigin={{
						vertical: 'bottom',
						horizontal: 'center',
					}}
				>
					<Alert
						onClose={() => {
							setIsError(false);
						}}
						sx={{ width: '100%' }}
						severity="error"
					>
						{__('Error sending email.', 'quillsmtp')}
					</Alert>
				</Snackbar>
			)}
		</div>
	);
};

export default EmailTest;
