/**
 * External dependencies
 */
import Modal from '@mui/material/Modal';
import Box from '@mui/material/Box';

/**
 * Internal dependencies
 */
import { MailerModuleSettings } from '@quillsmtp/mailers';

interface Props {
	connectionId: string;
	slug: string;
	mailer: MailerModuleSettings;
	onClose: () => void;
}

const style = {
	position: 'absolute' as 'absolute',
	top: '50%',
	left: '50%',
	transform: 'translate(-50%, -50%)',
	width: 400,
	bgcolor: 'background.paper',
	border: '2px solid #000',
	boxShadow: 24,
	p: 4,
};

const MailerModal: React.FC<Props> = ({
	connectionId,
	slug,
	mailer,
	onClose,
}) => {
	const Render = () => {
		const Component = mailer.render;

		/* @ts-ignore */
		return <Component slug={slug} />;
	};
	return (
		<Modal className="qsmtp-mailer-modal" open={true} onClose={() => {}}>
			<Box sx={style}>
				<h2>{slug}</h2>
				<Render />
			</Box>
		</Modal>
	);
};

export default MailerModal;
