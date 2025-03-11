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
import { OutlinedInput } from '@mui/material';

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
	const { mailer } = useSelect((select) => {
		return {
			mailer: select('quillSMTP/core').getTempConnectionMailer(connectionId),
		};
	});
	const [inputs, setInputs] = useState({});
	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	const submit = () => {
		apiFetch({
			path: `/qsmtp/v1/mailers/${mailer}/settings`,
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
		<div className="mailer-setup w-[82%]">
			<div className="mailer-setup__body">
				<div className="mailer-setup__instructions className='text-[14px] font-roboto text-[#333333] ml-0 mt-3 mb-[2.5rem] bg-[#E6EAFF] px-[1rem] pt-[10px] pb-[0.02px] capitalize w-fit'">
					<Instructions />
				</div>

				{Object.entries(fields).map(([key, field]) => {
					switch (field.type) {
						case 'text':
						case 'password':
							return (
								<div key={key}>
									<label className='font-roboto text-[#333333] mb-4 text-[18px] font-[500]'>{field.label}<span className='text-[18px] text-pink-600 pl-1'>*</span></label>
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
										sx={{ mb: 3, mt: 2 }}
										type={field.type}
									/>
									{field?.help && (
										<FormHelperText className="text-[14px] font-roboto text-[#333333] ml-0 mb-3 mt-0 bg-[#E6EAFF] p-1 capitalize w-fit help-text">
											{field?.help}
										</FormHelperText>
									)}
								</div>
							);
						case 'select':
							return (
								<FormControl key={key} fullWidth sx={{ mb: 2 }}>
									<label className='font-roboto text-[#333333] mb-2 text-[18px] font-[500]'>{field.label}<span className='text-[18px] text-pink-600 pl-1'>*</span></label>
									<Select
										value={inputs[key] ?? ''}
										label={field.label}
										onChange={(e: SelectChangeEvent) =>
											setInputs({
												...inputs,
												[key]: e.target.value,
											})
										}
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
										<FormHelperText className="text-[14px] font-roboto text-[#333333] ml-0 mb-3 mt-0 bg-[#E6EAFF] p-1 capitalize w-fit help-text">
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
