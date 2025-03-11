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
import { FaCheck } from 'react-icons/fa';

interface Props {
	connectionId: string;
}

const ForceFromName: React.FC<Props> = ({ connectionId }) => {
	const { force_from_name } = useSelect((select) => {
		return {
			force_from_name:
				select('quillSMTP/core').getTempConnectionForceFromName(
					connectionId
				),
		};
	});
	const { updateTempConnection } = useDispatch('quillSMTP/core');

	return (
		<FormControl sx={{ mb: 3 }}>
			{/* <FormControlLabel
				control={
					<Checkbox
						checked={force_from_name}
						onChange={() =>
							updateTempConnection(connectionId, {
								force_from_name: !force_from_name,
							})
						}
					/>
				}
				label={__('Force From Name', 'quillsmtp')}
			/> */}
			<div className="switch-container">
				<div className={`switch ${force_from_name ? "checked" : ""}`}
					onClick={() => updateTempConnection(connectionId, {
						force_from_name: !force_from_name,
					})}>
					<div className="circle">{force_from_name ? <FaCheck className='text-[#3858E9]' /> : ""}</div>
				</div>
				<span className="font-roboto font-[500] text-[#333333]">Force From Name</span>
			</div>
			<FormHelperText sx={{ mt: "14px", ml: 0 }} className='text-[#333333] text-[16px] font-roboto capitalize font-thin'>
				{__(
					'If enabled, the from name will be forced to the above name.',
					'quillsmtp'
				)}
			</FormHelperText>
		</FormControl>
	);
};

export default ForceFromName;
