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
import OutlinedInput from '@mui/material/OutlinedInput';
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
import { FaCheck } from "react-icons/fa6";

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
		<div className="qsmtp-email-test-page grid items-start justify-center">
			<div className="">
				<div className="qsmtp-email-test-header__title font-roboto font-[500] text-[38px] text-[#333333] pb-10 pl-0">
					{__('Email Test', 'quillsmtp')}
				</div>
			</div>
			<Card className="qsmtp-email-test-card bg-white py-9 px-14 mb-20" variant="outlined">
				<div className="">
					<div className="font-roboto font-[500] text-[30px] text-[#333333] pb-4 pl-2">
						{__('Test Your Email Configuration', 'quillsmtp')}
					</div>
					<div className='mx-4 pb-5 border-b'>
						<span className='text-[#919191] text-[14px] font-roboto'>Send A Test Email To Verify Your SMTP Or API Setup And Ensure Smooth Email Delivery</span>
					</div>
				</div>
				<CardContent className='mt-2'>
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
							<label className='font-roboto text-[#3858E9] mb-2 text-[16px]'>{__('Default Connection', 'quillsmtp')}</label>
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
								input={<OutlinedInput />}
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
						<label className='font-roboto text-[#3858E9] text-[16px]'>{__('Email Address', 'quillsmtp')}</label>
						<TextField
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
							className='mt-2'
						/>
						{/* <FormControlLabel
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
								"& .MuiTypography-root": {
									fontSize: "16px",
									fontWeight: "bold",
									color: "#3858E9",
								},
							}}
							className='font-roboto'
						/> */}
						<div className="switch-container">
							<div className={`switch ${isHTML ? "checked" : ""}`} onClick={() => setIsHTML((prev) => !prev)}>
								<div className="circle">{isHTML ? <FaCheck className='text-[#3858E9]'/> : ""}</div>
							</div>
							<span className="font-roboto">HTML Email</span>
						</div>
						<FormHelperText sx={{ my: 2 }} className='text-[#333333] text-[16px] font-roboto pb-2 border-b'>
							{__(
								'Send The Email As HTML Or Plain Text.',
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
							className='bg-[#333333] px-8 py-3 mt-2 normal-case font-roboto'
						>
							{__('Test My Email', 'quillsmtp')}
						</LoadingButton>
					</form>
				</CardContent>
			</Card>
		</div>
	);
};

export default EmailTest;
