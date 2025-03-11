/**
 * External Dependencies.
 */
import Button from '@mui/material/Button';

/**
 * Internal Dependencies.
 */
import './style.scss';
import { SaveOutlined } from '@mui/icons-material';

interface Props {
	save: {
		label: string;
		onClick: () => void;
		disabled: boolean;
	};
}

const Footer: React.FC<Props> = ({ save }) => {
	return (
		<div className="mailer-connect-footer">
			<div className="mailer-connect-footer__wrapper">
				<Button
					className="mailer-connect-footer__save text-white bg-[#333333] py-2 px-12 font-roboto normal-case"
					onClick={save.onClick}
					disabled={save.disabled}
					variant="contained"
				>
					<SaveOutlined className='mr-2'/>
					{save.label}
				</Button>
			</div>
		</div>
	);
};

export default Footer;
