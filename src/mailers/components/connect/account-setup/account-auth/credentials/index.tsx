/**
 * QuillSMTP Dependencies
 */
import { getMailerModules } from '@quillsmtp/mailers';

/**
 * External Dependencies
 */
import TextField from '@mui/material/TextField';
import { LoadingButton } from '@mui/lab';
import AddIcon from '@mui/icons-material/Add';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import FormHelperText from '@mui/material/FormHelperText';
import Switch from '@mui/material/Switch';
import FormControlLabel from '@mui/material/FormControlLabel';
import ControlPointOutlinedIcon from '@mui/icons-material/ControlPointOutlined';
import { keys, isFunction } from 'lodash';

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
} from '../../../../types';
import './style.scss';
import { OutlinedInput } from '@mui/material';
import { SaveOutlined } from '@mui/icons-material';
import { FaCheck } from 'react-icons/fa';

interface Props {
	connectionId: string;
	onAdding?: (status: boolean) => void;
	onAdded: (id: string, account: { name: string }) => void;
	labels?: AccountsLabels;
	fields?: AccountsAuthFields;
	Instructions?: React.FC;
}

const Credentials: React.FC<Props> = ({
	connectionId,
	onAdding,
	onAdded,
	labels,
	fields,
	Instructions,
}) => {
	const { mailer, initialValues } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getTempConnectionMailer(connectionId),
			initialValues: select('quillSMTP/core').getInitialAccountData(),
		};
	});

	// provider.
	const provider = getMailerModules()[mailer];

	fields = fields ?? {
		api_key: { label: provider.title + ' API Key', type: 'text' },
	};

	// state.
	const [inputs, setInputs] = useState({});
	const [submitting, setSubmitting] = useState(false);

	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	// Get credentials.
	const getCredentials = () => {
		const credentials: any = {};
		for (const [id, field] of Object.entries(fields ?? {})) {
			if (field.type === 'toggle') {
				credentials[id] =
					inputs[id] ??
					initialValues?.[id] ??
					field?.default ??
					false;
			} else {
				credentials[id] =
					inputs[id] ?? initialValues?.[id] ?? field?.default ?? '';
			}
		}

		return credentials;
	};
	const randomId = () => Math.random().toString(36).substr(2, 9);

	// submit.
	const submit = () => {
		const valid = checkInputsFilled();
		if (!valid) {
			createNotice({
				type: 'error',
				message: __('Please fill all required fields', 'quillsmtp'),
			});
			return;
		}

		setSubmitting(true);
		if (onAdding) onAdding(true);

		apiFetch({
			path: `/qsmtp/v1/mailers/${mailer}/accounts`,
			method: 'POST',
			data: {
				credentials: getCredentials(),
				name: inputs['name'],
				id: randomId(),
			},
		})
			.then((res: any) => {
				createNotice({
					type: 'success',
					message:
						(labels?.singular ?? __('Account', 'quillsmtp')) +
						' ' +
						__('added successfully!', 'quillsmtp'),
				});
				onAdded(res.id, {
					name: res.name,
					credentials: res.credentials,
				});
				setInputs({});
			})
			.catch((err) => {
				createNotice({
					type: 'error',
					message:
						err.message ??
						__('Error in adding the ', 'quillsmtp') +
						(
							labels?.singular ?? __('Account', 'quillsmtp')
						).toLowerCase(),
				});
			})
			.finally(() => {
				setSubmitting(false);
				if (onAdding) onAdding(false);
			});
	};

	// Function to check if all required fields are filled.
	const checkInputsFilled = () => {
		if (!inputs['name']) return false;
		for (const key of keys(fields)) {
			if (
				!inputs[key] &&
				fields?.[key].required &&
				!fields[key].default &&
				!initialValues?.[key]
			) {
				return false;
			}
		}
		return true;
	};

	let inputsFilled = true;
	for (const key of keys(fields)) {
		if (
			!inputs[key] &&
			fields[key].required &&
			!fields[key].default &&
			!initialValues?.[key]
		) {
			inputsFilled = false;
			break;
		}
	}

	if (!inputs['name']) {
		inputsFilled = false;
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
		<div className="mailer-auth-credentials w-[82%]">
			<label className='font-roboto text-[#3858E9] mb-4 text-[18px] font-[500]'>{__('Account Name ', 'quillsmtp')}<span className='text-[18px] text-pink-600'>*</span></label>
			<TextField
				label={__('Account Name', 'quillsmtp')}
				value={inputs['name'] ?? initialValues?.name ?? ''}
				onChange={(e) =>
					setInputs({
						...inputs,
						name: e.target.value,
					})
				}
				required
				autoComplete="new-password"
				disabled={submitting}
				variant="outlined"
				fullWidth
				sx={{ mb: 2, mt: 1 }}
			/>
			{Object.entries(fields).map(([key, field]) => {
				if (!getFieldVisibility(field)) return null;
				const inputValue =
					inputs[key] ?? initialValues?.[key] ?? field?.default ?? '';
				switch (field.type) {
					case 'text':
					case 'number':
					case 'password':
						return (
							<div key={key}>
								<label className='font-roboto text-[#333333] mb-4 text-[18px] font-[500]'>{field.label}<span className='text-[18px] text-pink-600 pl-1'>*</span></label>
								<TextField
									autoComplete="new-password"
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
									sx={{ mb: 2, mt: 1 }}
									type={field.type}
								/>
								{field?.help && (
									<FormHelperText className="text-[14px] font-roboto text-[#333333] ml-0 mb-3 mt-0 bg-[#E6EAFF] p-1 capitalize w-fit help-text">
										<Help field={field} />
									</FormHelperText>
								)}
							</div>
						);
					case 'select':
						return (
							<FormControl
								key={key}
								fullWidth
								sx={{ mb: 2 }}
								required={field.required}
							>
								<label className='font-roboto text-[#333333] mb-2 text-[18px] font-[500]'>{field.label}<span className='text-[18px] text-pink-600 pl-1'>*</span></label>
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
									input={<OutlinedInput />}
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
									<FormHelperText className='text-[14px] font-roboto text-[#333333] ml-0 my-3 bg-[#E6EAFF] p-1 capitalize w-fit'>
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
								<div className="switch-container">
									<div
										className={`switch ${inputValue ? "checked" : ""}`}
										onClick={() =>
											setInputs({
												...inputs,
												[key]: !inputValue, // ✅ Toggle boolean value
											})
										}
									>
										<div className="circle">
											{inputValue ? <FaCheck className="text-[#3858E9]" /> : null}
											{/* ✅ Use null instead of "" */}
										</div>
									</div>
									<span className="font-roboto font-[500] text-[#333333]">{field.label}</span>
								</div>
								{/* <FormControlLabel
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
								/> */}
								{field?.help && (
									<FormHelperText className='text-[14px] font-roboto text-[#333333] ml-0 my-3 bg-[#E6EAFF] p-1 capitalize w-fit'>
										{<Help field={field} />}
									</FormHelperText>
								)}
							</FormControl>
						);
					default:
						return null;
				}
			})}
			<LoadingButton
				variant="contained"
				color="primary"
				startIcon={<SaveOutlined />}
				loading={submitting}
				disabled={!inputsFilled || submitting}
				onClick={submit}
				className='py-3 px-12 bg-[#333333] text-white font-roboto normal-case'
			>
				{__('Save', 'quillsmtp')}
			</LoadingButton>
			{Instructions && (
				<div className="mailer-auth-instructions">
					<Instructions />
				</div>
			)}
		</div>
	);
};

export default Credentials;
