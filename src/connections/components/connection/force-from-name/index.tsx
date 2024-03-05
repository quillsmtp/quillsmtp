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

const ForceFromName: React.FC<Props> = ({ connectionId }) => {
	const { force_from_name } = useSelect((select) => {
		return {
			force_from_name:
				select('quillSMTP/core').getConnectionForceFromName(
					connectionId
				),
		};
	});
	const { updateConnection } = useDispatch('quillSMTP/core');

	return (
		<FormControl sx={{ mb: 3 }}>
			<FormControlLabel
				control={
					<Checkbox
						checked={force_from_name}
						onChange={() =>
							updateConnection(connectionId, {
								force_from_name: !force_from_name,
							})
						}
					/>
				}
				label={__('Force From Name', 'quillsmtp')}
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

export default ForceFromName;
