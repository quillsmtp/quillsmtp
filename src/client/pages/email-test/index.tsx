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
import { useDispatch } from '@wordpress/data';

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

/**
 * Internal dependencies
 */
import './style.scss';

const EmailTest: React.FC = () => {
	const adminEmail = ConfigAPI.getAdminEmail();
	const [email, setEmail] = useState<string>(adminEmail);
	const [connection, setConnection] = useState<string>('');
	const [isHTML, setIsHTML] = useState<boolean>(true);
	const [isSending, setIsSending] = useState<boolean>(false);
	const { connections } = useSelect((select) => {
		return {
			connections: select('quillSMTP/core').getConnections(),
		};
	});
	const mailersModules = getMailerModules();
	const { createNotice } = useDispatch('quillSMTP/core');

	// Ajax request to send the email.
	const sendEmail = () => {
		const connectionId = connection ? connection : keys(connections)[0];
		if (!connectionId || !email) return;
		setIsSending(true);
		const ajaxURL = ConfigAPI.getAjaxUrl();
		const nonce = ConfigAPI.getNonce();
		const body = new FormData();

		body.append('action', 'quillsmtp_send_test_email');
		body.append('nonce', nonce);
		body.append('email', email);
		body.append('connection', connectionId);
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
					createNotice({
						type: 'success',
						message: __('Email sent successfully.', 'quillsmtp'),
					});
				} else {
					createNotice({
						type: 'error',
						message: __('Error sending email.', 'quillsmtp'),
					});
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
			<Card className="qsmtp-email-test-card qsmtp-card" variant="outlined">
				<div className="qsmtp-card-header">
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
						<FormControl fullWidth sx={{
							mb: 2, "& .MuiOutlinedInput-notchedOutline": {
								borderColor: "gray",
							},
							"&:hover > .MuiOutlinedInput-notchedOutline": {
								borderColor: "gray"
							}
						}}>
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
							sx={{
								mb: 2, "& .MuiOutlinedInput-notchedOutline": {
									borderColor: "gray",
								},
								"&:hover > .MuiOutlinedInput-notchedOutline": {
									borderColor: "gray"
								}
							}}
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
							size='large'
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
		</div>
	);
};

export default EmailTest;
