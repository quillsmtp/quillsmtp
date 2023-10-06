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
	close: {
		label: string;
		onClick: () => void;
	};
}

const Footer: React.FC<Props> = ({ save, close }) => {
	return (
		<div className="mailer-connect-footer">
			<div className="mailer-connect-footer__wrapper">
				<Button
					className="mailer-connect-footer__cancel"
					onClick={close.onClick}
					variant="outlined"
				>
					{close.label}
				</Button>
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
