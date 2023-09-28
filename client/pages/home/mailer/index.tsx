/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * External Dependencies
 */
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import FormControl from '@mui/material/FormControl';
import FormLabel from '@mui/material/FormLabel';

const Mailer = () => {
	const [mailer, setMailer] = useState('wp_mail');

	return (
		<div className="qsmtp-mailer-settings-tab">
			<FormControl component="fieldset" sx={{ mt: 2, mb: 2 }}>
				<FormLabel component="legend">
					{__('Mailer', 'quillsmtp')}
				</FormLabel>
				<RadioGroup
					row
					aria-label="mailer"
					name="mailer"
					value={mailer}
					onChange={(e) => setMailer(e.target.value)}
				>
					<FormControlLabel
						value="default"
						control={<Radio />}
						label={__('Default', 'quillsmtp')}
					/>
					<FormControlLabel
						value="sendlayer"
						control={<Radio />}
						label={__('SendLayer', 'quillsmtp')}
					/>
				</RadioGroup>
			</FormControl>
		</div>
	);
};

export default Mailer;
