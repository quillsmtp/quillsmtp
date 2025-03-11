/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * External Dependencies
 */
import TextField from '@mui/material/TextField';
import { FormHelperText } from '@mui/material';

interface Props {
	connectionId: string;
}

const FromName: React.FC<Props> = ({ connectionId }) => {
	const { from_name } = useSelect((select) => {
		return {
			from_name:
				select('quillSMTP/core').getTempConnectionFromName(connectionId),
		};
	});
	const { updateTempConnection } = useDispatch('quillSMTP/core');

	return (
		<div className='w-[82%]'>
			<label className='font-roboto text-[#333333] mb-4 text-[18px] font-semibold'>{__('From Email', 'quillsmtp')}</label>
			<TextField
				autoComplete='new-password'
				sx={{ my: 1 }}
				label={__('From Name', 'quillsmtp')}
				value={from_name}
				onChange={(e) =>
					updateTempConnection(connectionId, {
						from_name: e.target.value,
					})
				}
				variant="outlined"
				fullWidth
			/>
			<FormHelperText sx={{ mb: 2 }} className='text-[#333333] text-[14px] font-roboto capitalize'>
				{__(
					'If left blank, the default WordPress from email will be used.',
					'quillsmtp'
				)}
			</FormHelperText>
		</div>
	);
};

export default FromName;
