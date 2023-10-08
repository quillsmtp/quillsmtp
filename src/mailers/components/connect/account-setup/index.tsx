/**
 * External Dependencies.
 */
import TextField from '@mui/material/TextField';

/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { useSelect } from '@wordpress/data';

/**
 * Internal Dependencies
 */
import type { SetupFields } from '../types';
import './style.scss';

interface Props {
	Instructions: React.FC;
	fields: SetupFields;
	Controls: React.FC<{ submit: () => void }>;
	onFinish: (app: any) => void;
}

const Setup: React.FC<Props> = ({
	Instructions,
	fields,
	Controls,
	onFinish,
}) => {
	const { provider } = useSelect((select) => {
		return {
			provider: select('quillSMTP/core').getCurrentMailerProvider(),
		};
	});
	const [inputs, setInputs] = useState({});

	const submit = () => {
		apiFetch({
			path: `/qsmtp/v1/mailers/${provider.slug}/settings`,
			method: 'POST',
			data: {
				app: inputs,
			},
		})
			.then(() => {
				const app = {};
				for (const [key, field] of Object.entries(fields)) {
					if (field.check) {
						app[key] = inputs[key];
					}
				}
				onFinish(app);
			})
			.catch(() => {
				//console.log( 'Error: ', err );
			});
	};

	return (
		<div className="mailer-setup">
			<div className="mailer-setup__body">
				<div className="mailer-setup__instructions">
					<Instructions />
				</div>

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
					/>
				))}
			</div>

			<Controls submit={submit} />
		</div>
	);
};

export default Setup;
