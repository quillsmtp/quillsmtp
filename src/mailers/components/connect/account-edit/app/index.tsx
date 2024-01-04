/**
 * External Dependencies.
 */
import TextField from '@mui/material/TextField';
import { Stack } from '@mui/material';
import Button from '@mui/material/Button';

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
	const { connection } = useSelect((select) => {
		return {
			connection: select('quillSMTP/core').getConnection(connectionId),
		};
	});
	const { setupApp, setupAccounts } = useDispatch('quillSMTP/core');
	const [inputs, setInputs] = useState(values);
	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');
	const submit = () => {
		if (onEditing) onEditing(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${connection.mailer}/settings`,
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
				setupApp(connection.mailer, app);
				setupAccounts(connection.mailer, {});
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

				{Object.entries(fields).map(([key, field]) => (
					<TextField
						key={key}
						label={field.label}
						value={inputs[key] ?? ''}
						onChange={(e) =>
							setInputs({ ...inputs, [key]: e.target.value })
						}
						variant="outlined"
						fullWidth
						sx={{ mb: 2 }}
						type={field.type}
					/>
				))}
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
