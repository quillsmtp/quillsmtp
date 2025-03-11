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
import TextField from '@mui/material/TextField';
import { FormControl, FormHelperText } from '@mui/material';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';

interface Props {
	connectionId: string;
}

const FromEmail: React.FC<Props> = ({ connectionId }) => {
	const [isFetching, setIsFetching] = useState(false);
	const [fromEmails, setFromEmails] = useState<any>([]);
	const { mailer, from_email, account_id } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getTempConnectionMailer(connectionId),
			account_id:
				select('quillSMTP/core').getTempConnectionAccountId(connectionId),
			from_email:
				select('quillSMTP/core').getTempConnectionFromEmail(connectionId),
		};
	});
	const { updateTempConnection, createNotice } = useDispatch('quillSMTP/core');

	const fetchFromEmails = applyFilters(
		'QuillSMTP.Fetch.FromEmails',
		false,
		mailer
	);

	useEffect(() => {
		if (fetchFromEmails) {
			getFromEmails();
			updateTempConnection(connectionId, {
				from_email: '',
			});
		}
	}, [fetchFromEmails]);

	useEffect(() => {
		if (fetchFromEmails && !from_email) {
			updateTempConnection(connectionId, {
				from_email: getFromValue(),
			});
		}
	}, [fromEmails]);

	const getFromEmails = () => {
		if (isFetching || !mailer || !account_id) return;
		setIsFetching(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${mailer}/settings/${account_id}/from-emails`,
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
		<>
			{mailer && (
				<>
					{!fetchFromEmails && (
						<div className='w-[82%]'>
							<label className='font-roboto text-[#3858E9] mb-4 text-[18px] font-semibold'>{__('From Email', 'quillsmtp')}</label>
							<TextField
								label={__('From Name', 'quillsmtp')}
								value={from_email}
								onChange={(e) =>
									updateTempConnection(connectionId, {
										from_email: e.target.value,
									})
								}
								autoComplete='new-password'
								variant="outlined"
								fullWidth
								sx={{ my: 1}}
							/>
							<FormHelperText sx={{ mb:2 }} className='text-[#333333] text-[14px] font-roboto capitalize'>
								{__(
									'If left blank, the default WordPress from email will be used.',
									'quillsmtp'
								)}
							</FormHelperText>
						</div>
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
									updateTempConnection(connectionId, {
										from_email: e.target.value,
									})
								}
								required
								disabled={isFetching}
							>
								{fromEmails.map((option, index) => (
									<MenuItem key={index} value={option.value}>
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
				</>
			)}
		</>
	);
};

export default FromEmail;
