/**
 * External Dependencies.
 */
import Button from '@mui/material/Button';

/**
 * Internal Dependencies.
 */
import './style.scss';

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
					className="mailer-connect-footer__save"
					onClick={save.onClick}
					disabled={save.disabled}
					variant="contained"
				>
					{save.label}
				</Button>
			</div>
		</div>
	);
};

export default Footer;
