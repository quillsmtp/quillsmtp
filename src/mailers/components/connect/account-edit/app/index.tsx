/**
 * External Dependencies.
 */
import TextField from '@mui/material/TextField';
import { Stack } from '@mui/material';
import Button from '@mui/material/Button';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import FormHelperText from '@mui/material/FormHelperText';

/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import type { SetupFields } from '../../../types';
import Footer from '../../footer';

interface Props {
	connectionId: string;
	Instructions?: React.FC;
	fields: SetupFields;
	values: any;
	onEditing?: (status: boolean) => void;
	isEditing?: boolean;
	onCancel: () => void;
}

const EditApp: React.FC<Props> = ({
	connectionId,
	Instructions,
	fields,
	values,
	onEditing,
	isEditing,
	onCancel,
}) => {
	const { mailer } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getConnectionMailer(connectionId),
		};
	});
	const { setupApp, setupAccounts } = useDispatch('quillSMTP/core');
	const [inputs, setInputs] = useState(values);
	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');
	const submit = () => {
		if (onEditing) onEditing(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${mailer}/settings`,
			method: 'POST',
			data: {
				app: inputs,
				accounts: {},
			},
		})
			.then(() => {
				const app = {};
				for (const [key, field] of Object.entries(fields)) {
					app[key] = inputs[key];
				}
				setupApp(mailer, app);
				setupAccounts(mailer, {});
				createNotice({
					type: 'success',
					message: __('App edited successfully.', 'quill-smtp'),
				});
			})
			.catch(() => {
				createNotice({
					type: 'error',
					message: __('Failed to edit app.', 'quill-smtp'),
				});
			})
			.finally(() => {
				if (onEditing) onEditing(false);
			});
	};

	return (
		<div className="mailer-setup">
			<div className="mailer-setup__body">
				{Instructions && (
					<div className="mailer-setup__instructions">
						<Instructions />
					</div>
				)}

				{Object.entries(fields).map(([key, field]) => {
					switch (field.type) {
						case 'text':
						case 'password':
							return (
								<TextField
									key={key}
									label={field.label}
									value={inputs[key] ?? ''}
									onChange={(e) =>
										setInputs({
											...inputs,
											[key]: e.target.value,
										})
									}
									variant="outlined"
									fullWidth
									sx={{ mb: 2 }}
									helperText={field?.help}
									type={field.type}
								/>
							);
						case 'select':
							return (
								<FormControl key={key} fullWidth sx={{ mb: 2 }}>
									<InputLabel>{field.label}</InputLabel>
									<Select
										value={inputs[key] ?? ''}
										label={field.label}
										onChange={(e: SelectChangeEvent) =>
											setInputs({
												...inputs,
												[key]: e.target.value,
											})
										}
									>
										{field.options &&
											field.options.map((option) => (
												<MenuItem
													key={option.value}
													value={option.value}
												>
													{option.label}
												</MenuItem>
											))}
									</Select>
									{field?.help && (
										<FormHelperText>
											{field?.help}
										</FormHelperText>
									)}
								</FormControl>
							);
						default:
							return null;
					}
				})}
			</div>
			<Stack direction="row" spacing={2}>
				<Footer
					save={{
						label: 'Save',
						onClick: submit,
						disabled: isEditing ?? false,
					}}
				/>
				<Button
					variant="outlined"
					onClick={onCancel}
					disabled={isEditing}
				>
					{__('Cancel', 'quill-smtp')}
				</Button>
			</Stack>
		</div>
	);
};

export default EditApp;
