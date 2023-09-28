/**
 * QuillForms Dependencies.
 */
import { TextControl } from '@wordpress/components';

/**
 * WordPress Dependencies
 */
import { useState } from 'react';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal Dependencies
 */
import type { Provider, SetupFields } from '../../types';

interface Props {
	provider: Provider;
	Instructions: React.FC;
	fields: SetupFields;
	Controls: React.FC<{ submit: () => void }>;
	onFinish: (app: any) => void;
}

const Setup: React.FC<Props> = ({
	provider,
	Instructions,
	fields,
	Controls,
	onFinish,
}) => {
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
		<div className="mailers-setup">
			<div className="mailers-setup__body">
				<div className="mailers-setup__instructions">
					<Instructions />
				</div>

				{Object.entries(fields).map(([key, field]) => (
					<TextControl
						key={key}
						label={field.label}
						value={inputs[key] ?? ''}
						onChange={(value) =>
							setInputs({ ...inputs, [key]: value })
						}
					/>
				))}
			</div>

			<Controls submit={submit} />
		</div>
	);
};

export default Setup;
