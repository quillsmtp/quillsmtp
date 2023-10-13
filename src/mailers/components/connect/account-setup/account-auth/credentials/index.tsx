/**
 * External Dependencies
 */
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';

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
	onAdding?: (status: boolean) => void;
	onAdded: (id: string, account: { name: string }) => void;
	labels?: AccountsLabels;
	fields?: AccountsAuthFields;
	Instructions?: React.FC;
}

const Credentials: React.FC<Props> = ({
	onAdding,
	onAdded,
	labels,
	fields,
	Instructions,
}) => {
	const { provider } = useSelect((select) => {
		return {
			provider: select('quillSMTP/core').getCurrentMailerProvider(),
		};
	});

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
			path: `/qsmtp/v1/mailers/${provider.slug}/accounts`,
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
			{Object.entries(fields).map(([key, field]) => (
				<TextField
					key={key}
					label={field.label}
					value={inputs[key] ?? ''}
					onChange={(e) =>
						setInputs({ ...inputs, [key]: e.target.value })
					}
					disabled={submitting}
					variant="outlined"
					fullWidth
					sx={{ mb: 2 }}
				/>
			))}
			<Button
				onClick={submit}
				variant="contained"
				disabled={!inputsFilled || submitting}
			>
				{__('Add', 'quillsmtp')}
			</Button>
			{Instructions && (
				<div className="mailer-auth-instructions">
					<Instructions />
				</div>
			)}
		</div>
	);
};

export default Credentials;
