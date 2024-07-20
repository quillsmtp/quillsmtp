/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * External Dependencies
 */
import Checkbox from '@mui/material/Checkbox';
import FormControlLabel from '@mui/material/FormControlLabel';
import { FormControl, FormHelperText } from '@mui/material';

interface Props {
	connectionId: string;
}

const ForceFromEmail: React.FC<Props> = ({ connectionId }) => {
	const { force_from_email } = useSelect((select) => {
		return {
			force_from_email:
				select('quillSMTP/core').getTempConnectionForceFromEmail(
					connectionId
				),
		};
	});
	const { updateTempConnection } = useDispatch('quillSMTP/core');

	return (
		<FormControl sx={{ mb: 3 }}>
			<FormControlLabel
				control={
					<Checkbox
						checked={force_from_email}
						onChange={() =>
							updateTempConnection(connectionId, {
								force_from_email: !force_from_email,
							})
						}
					/>
				}
				label={__('Force From Email', 'quillsmtp')}
			/>
			<FormHelperText>
				{__(
					'If enabled, the from name will be forced to the above name.',
					'quillsmtp'
				)}
			</FormHelperText>
		</FormControl>
	);
};

export default ForceFromEmail;
