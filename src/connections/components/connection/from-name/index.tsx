/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * External Dependencies
 */
import TextField from '@mui/material/TextField';

interface Props {
	connectionId: string;
}

const FromName: React.FC<Props> = ({ connectionId }) => {
	const { from_name } = useSelect((select) => {
		return {
			from_name:
				select('quillSMTP/core').getConnectionFromName(connectionId),
		};
	});
	const { updateConnection } = useDispatch('quillSMTP/core');

	return (
		<TextField
			autoComplete='new-password'
			sx={{ mb: 2 }}
			label={__('From Name', 'quillsmtp')}
			value={from_name}
			onChange={(e) =>
				updateConnection(connectionId, {
					from_name: e.target.value,
				})
			}
			variant="outlined"
			fullWidth
			helperText={__(
				'If left blank, the default WordPress from name will be used.',
				'quillsmtp'
			)}
		/>
	);
};

export default FromName;
