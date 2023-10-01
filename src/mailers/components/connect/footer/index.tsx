/**
 * QuillForms Dependencies.
 */
import { Button } from '@wordpress/components';

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
		<div className="integration-connect-footer">
			<div className="integration-connect-footer__wrapper">
				<Button
					className="integration-connect-footer__cancel"
					onClick={close.onClick}
				>
					{close.label}
				</Button>
				<Button
					className="integration-connect-footer__save"
					onClick={save.onClick}
					disabled={save.disabled}
				>
					{save.label}
				</Button>
			</div>
		</div>
	);
};

export default Footer;
