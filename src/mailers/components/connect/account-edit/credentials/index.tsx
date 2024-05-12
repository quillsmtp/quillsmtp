/**
 * QuillSMTP Dependencies
 */
import { getMailerModules } from '@quillsmtp/mailers';
import type { Account } from '@quillsmtp/store';

/**
 * External Dependencies
 */
import TextField from '@mui/material/TextField';
import { LoadingButton } from '@mui/lab';
import Button from '@mui/material/Button';
import SaveIcon from '@mui/icons-material/Save';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import FormHelperText from '@mui/material/FormHelperText';
import Switch from '@mui/material/Switch';
import FormControlLabel from '@mui/material/FormControlLabel';
import { Stack } from '@mui/material';
import { isFunction } from 'lodash';

/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import { useDispatch, useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import {
	AccountsAuthFields,
	AccountsLabels,
	AccountsAuthField,
} from '../../../types';
import './style.scss';

interface Props {
	connectionId: string;
	accountId: string;
	account: Account;
	onEditing?: (status: boolean) => void;
	onEdited: (id: string, account: Account) => void;
	onCancel: () => void;
	labels?: AccountsLabels;
	fields?: AccountsAuthFields;
	Instructions?: React.FC;
}

const EditCredentials: React.FC<Props> = ({
	connectionId,
	accountId,
	account,
	onEditing,
	onEdited,
	onCancel,
	labels,
	fields,
	Instructions,
}) => {
	const { mailer } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getConnectionMailer(connectionId),
		};
	});

	// provider.
	const provider = getMailerModules()[mailer];

	fields = fields ?? {
		api_key: { label: provider.title + ' API Key', type: 'text' },
	};

	// state.
	const [inputs, setInputs] = useState(account.credentials ?? {});
	const [name, setName] = useState(account.name ?? '');
	const [submitting, setSubmitting] = useState(false);

	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	// Get credentials.
	const getCredentials = () => {
		const credentials: any = {};
		for (const [id, field] of Object.entries(fields ?? {})) {
			if (field.type === 'toggle') {
				credentials[id] = inputs[id] ?? field?.default ?? false;
			} else {
				credentials[id] = inputs[id] ?? field?.default ?? '';
			}
		}

		return credentials;
	};

	// submit.
	const submit = () => {
		if (submitting || !name) return;
		setSubmitting(true);
		if (onEditing) onEditing(true);

		apiFetch({
			path: `/qsmtp/v1/mailers/${mailer}/accounts`,
			method: 'POST',
			data: {
				id: accountId,
				name,
				credentials: getCredentials(),
			},
		})
			.then((res: any) => {
				createNotice({
					type: 'success',
					message:
						(labels?.singular ?? __('Account', 'quillsmtp')) +
						' ' +
						__('updated successfully!', 'quillsmtp'),
				});
				onEdited(res.id, {
					name: res.name,
					credentials: res.credentials ?? {},
				});
			})
			.catch((err) => {
				createNotice({
					type: 'error',
					message:
						err.message ??
						__('Error in updating  the ', 'quillsmtp') +
							(
								labels?.singular ?? __('Account', 'quillsmtp')
							).toLowerCase(),
				});
			})
			.finally(() => {
				setSubmitting(false);
				if (onEditing) onEditing(false);
			});
	};

	let inputsFilled = true;
	for (const key of Object.keys(fields)) {
		if (!inputs[key] && fields[key].required && !fields[key].default) {
			inputsFilled = false;
			break;
		}
	}

	// Get field visibility depending on the field dependencies of other fields.
	const getFieldVisibility = (field: AccountsAuthField) => {
		if (!field?.dependencies) return true;
		const type = field.dependencies.type ?? 'or';
		const conditions = field.dependencies.conditions;
		let result = false;
		for (const condition of conditions) {
			const { field: fieldName, value, operator } = condition;
			const fieldValue =
				inputs[fieldName] ?? fields?.[fieldName]?.default ?? '';
			let visible = false;
			switch (operator) {
				case '==':
					visible = fieldValue == value;
					break;
				case '!=':
					visible = fieldValue != value;
					break;
				case '>':
					visible = fieldValue > value;
					break;
				case '<':
					visible = fieldValue < value;
					break;
				case '>=':
					visible = fieldValue >= value;
					break;
				case '<=':
					visible = fieldValue <= value;
					break;
				default:
					visible = false;
			}

			if (type === 'and') {
				result = result && visible;
			} else {
				result = result || visible;
			}
		}

		return result;
	};

	const Help = ({ field }) => {
		if (!field?.help) return null;
		if (isFunction(field.help)) {
			return <field.help />;
		}

		return <p>{field.help}</p>;
	};

	return (
		<div className="mailer-auth-credentials">
			<TextField
				label={__('Account Name', 'quillsmtp')}
				value={name}
				onChange={(e) =>
					setName(e.target.value.replace(/[^a-zA-Z0-9\s]/g, ''))
				}
				required
				disabled={submitting}
				variant="outlined"
				fullWidth
				sx={{ mb: 2 }}
			/>
			{Object.entries(fields).map(([key, field]) => {
				if (!getFieldVisibility(field)) return null;
				const inputValue = inputs[key] ?? field?.default ?? '';
				switch (field.type) {
					case 'text':
					case 'number':
					case 'password':
						return (
							<TextField
								key={key}
								label={field.label}
								value={inputValue}
								onChange={(e) =>
									setInputs({
										...inputs,
										[key]: e.target.value,
									})
								}
								required={field.required}
								disabled={submitting}
								variant="outlined"
								fullWidth
								sx={{ mb: 2 }}
								helperText={<Help field={field} />}
								type={field.type}
							/>
						);
					case 'select':
						return (
							<FormControl
								key={key}
								fullWidth
								sx={{ mb: 2 }}
								required={field.required}
							>
								<InputLabel>{field.label}</InputLabel>
								<Select
									value={inputValue}
									label={field.label}
									onChange={(e: SelectChangeEvent) =>
										setInputs({
											...inputs,
											[key]: e.target.value,
										})
									}
									required={field.required}
									disabled={submitting}
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
										{<Help field={field} />}
									</FormHelperText>
								)}
							</FormControl>
						);
					case 'toggle':
						return (
							<FormControl
								component="fieldset"
								variant="standard"
								key={key}
								sx={{
									display: 'block',
								}}
							>
								<FormControlLabel
									control={
										<Switch
											checked={inputValue || false}
											onChange={(e) =>
												setInputs({
													...inputs,
													[key]: e.target.checked,
												})
											}
										/>
									}
									label={field.label}
								/>
								{field?.help && (
									<FormHelperText>
										{<Help field={field} />}
									</FormHelperText>
								)}
							</FormControl>
						);
					default:
						return null;
				}
			})}
			<Stack direction="row" spacing={2}>
				<LoadingButton
					variant="contained"
					color="primary"
					startIcon={<SaveIcon />}
					loading={submitting}
					disabled={!inputsFilled || submitting}
					onClick={submit}
				>
					{__('Save', 'quillsmtp')}
				</LoadingButton>
				<Button
					variant="outlined"
					color="primary"
					disabled={submitting}
					onClick={onCancel}
				>
					{__('Cancel', 'quillsmtp')}
				</Button>
			</Stack>
			{Instructions && (
				<div className="mailer-auth-instructions">
					<Instructions />
				</div>
			)}
		</div>
	);
};

export default EditCredentials;
