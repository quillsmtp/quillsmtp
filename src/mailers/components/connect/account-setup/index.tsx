/**
 * External Dependencies.
 */
import TextField from '@mui/material/TextField';
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

/**
 * Internal Dependencies
 */
import type { SetupFields } from '../../types';
import './style.scss';

interface Props {
	connectionId: string;
	Instructions: React.FC;
	fields: SetupFields;
	Controls: React.FC<{ submit: () => void }>;
	onFinish: (app: any) => void;
}

const Setup: React.FC<Props> = ({
	connectionId,
	Instructions,
	fields,
	Controls,
	onFinish,
}) => {
	const { connection } = useSelect((select) => {
		return {
			connection: select('quillSMTP/core').getConnection(connectionId),
		};
	});
	const [inputs, setInputs] = useState({});
	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	const submit = () => {
		apiFetch({
			path: `/qsmtp/v1/mailers/${connection.mailer}/settings`,
			method: 'POST',
			data: {
				app: inputs,
			},
		})
			.then(() => {
				const app = {};
				// @ts-ignore
				for (const [key, field] of Object.entries(fields)) {
					app[key] = inputs[key];
				}
				onFinish(app);
			})
			.catch((err) => {
				createNotice({
					type: 'error',
					message: err.message,
				});
			});
	};

	return (
		<div className="mailer-setup">
			<div className="mailer-setup__body">
				<div className="mailer-setup__instructions">
					<Instructions />
				</div>

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

			<Controls submit={submit} />
		</div>
	);
};

export default Setup;
