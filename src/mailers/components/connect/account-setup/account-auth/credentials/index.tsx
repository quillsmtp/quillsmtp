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
import { AccountsAuthFields, AccountsLabels } from '../../../../types';
import './style.scss';

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
	const { connection } = useSelect((select) => {
		return {
			connection: select('quillSMTP/core').getConnection(connectionId),
		};
	});

	// provider.
	const provider = getMailerModules()[connection.mailer];

	fields = fields ?? {
		api_key: { label: provider.title + ' API Key', type: 'text' },
	};

	// state.
	const [inputs, setInputs] = useState({});
	const [submitting, setSubmitting] = useState(false);

	// dispatch notices.
	const { createSuccessNotice, createErrorNotice } =
		useDispatch('core/notices');

	// submit.
	const submit = () => {
		setSubmitting(true);
		if (onAdding) onAdding(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${connection.mailer}/accounts`,
			method: 'POST',
			data: {
				credentials: inputs,
			},
		})
			.then((res: any) => {
				createSuccessNotice(
					'✅ ' +
						(labels?.singular ?? __('Account', 'quillsmtp')) +
						' ' +
						__('added successfully!', 'quillsmtp'),
					{
						type: 'snackbar',
						isDismissible: true,
					}
				);
				onAdded(res.id, { name: res.name });
				setInputs({});
			})
			.catch((err) => {
				createErrorNotice(
					'⛔ ' +
						(err.message ??
							__('Error in adding the ', 'quillsmtp') +
								(
									labels?.singular ??
									__('Account', 'quillsmtp')
								).toLowerCase()),
					{
						type: 'snackbar',
						isDismissible: true,
					}
				);
			})
			.finally(() => {
				setSubmitting(false);
				if (onAdding) onAdding(false);
			});
	};

	let inputsFilled = true;
	for (const key of Object.keys(fields)) {
		if (!inputs[key] && fields[key].required) {
			inputsFilled = false;
			break;
		}
	}

	return (
		<div className="mailer-auth-credentials">
			{Object.entries(fields).map(([key, field]) => {
				switch (field.type) {
					case 'text':
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
								required={field.required}
								disabled={submitting}
								variant="outlined"
								fullWidth
								sx={{ mb: 2 }}
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
									value={inputs[key] ?? ''}
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
							</FormControl>
						);
				}
			})}
			<LoadingButton
				variant="contained"
				color="primary"
				startIcon={<AddIcon />}
				loading={submitting}
				disabled={!inputsFilled || submitting}
				onClick={submit}
			>
				{__('Add', 'quillsmtp')}
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
