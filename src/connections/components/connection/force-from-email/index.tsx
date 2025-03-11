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
			{/* <FormControlLabel
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
			/> */}
			<div className="switch-container">
				<div className={`switch ${force_from_email ? "checked" : ""}`}
					onClick={() => updateTempConnection(connectionId, {
						force_from_email: !force_from_email,
					})}>
					<div className="circle">{force_from_email ? <FaCheck className='text-[#3858E9]' /> : ""}</div>
				</div>
				<span className="font-roboto font-[500]">Force From Email</span>
			</div>
			<FormHelperText sx={{ mt:"14px", ml:0 }} className='text-[#333333] text-[16px] font-roboto capitalize font-thin'>
				{__(
					'If enabled, the from name will be forced to the above name.',
					'quillsmtp'
				)}
			</FormHelperText>
		</FormControl>
	);
};

export default ForceFromEmail;
