/**
 * External dependencies
 */
import Modal from '@mui/material/Modal';
import Box from '@mui/material/Box';
import { css } from '@emotion/css';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { MailerModuleSettings } from '@quillsmtp/mailers';

interface Props {
	mailer: MailerModuleSettings;
	open: boolean;
	onClose: () => void;
}

const style = {
	position: 'absolute' as 'absolute',
	top: '50%',
	left: '50%',
	transform: 'translate(-50%, -50%)',
	width: 700,
	maxWidth: '100%',
	bgcolor: '#f0f0f1',
	border: '1px solid #aeaeae',
	boxShadow: 24,
	boxSizing: 'border-box' as 'border-box',
};

const MailerModal: React.FC<Props> = ({ mailer, open, onClose }) => {
	const { icon } = mailer;
	const Render = () => {
		const Component = mailer.render;

		/* @ts-ignore */
		return <Component onClose={onClose} />;
	};

	const header = (
		<div
			className={classnames(
				'mailer-modal-header',
				css`
					display: flex;
					align-items: center;
					justify-content: center;
					padding: 1rem;
					background: #fff;
					font-family: Roboto, sans-serif;
					font-weight: 300;
					font-size: 20px;
					svg,
					img {
						width: 40px;
						height: 40px;
						margin-right: 0.5rem;
					}
				`
			)}
		>
			<img src={icon} /> {mailer.title}
		</div>
	);

	return (
		<Modal className="qsmtp-mailer-modal" open={open} onClose={onClose}>
			<Box sx={style}>
				{header}
				<Render />
			</Box>
		</Modal>
	);
};

export default MailerModal;
