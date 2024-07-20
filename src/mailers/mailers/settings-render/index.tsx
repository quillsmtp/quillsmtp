/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * External Dependencies
 */
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import IconButton from '@mui/material/IconButton';
import CloseIcon from '@mui/icons-material/Close';

/**
 * Internal Dependencies.
 */
import ConfigAPI from '@quillsmtp/config';
import MailerFeatureAvailability from './mailer-feature-availability';
import { css } from "@emotion/css";

const SettingsRender: React.FC<{ slug: string; connectionId: string }> = ({
	slug,
	connectionId,
	setStep
}) => {
	const { getStoreMailers } = ConfigAPI;
	const mailers = getStoreMailers();
	const mailer = mailers[slug];
	const { getTempConnectionMailer } = useSelect((select) => ({
		getTempConnectionMailer: select('quillSMTP/core').getTempConnectionMailer,
	}));
	const mailerSlug = getTempConnectionMailer(connectionId);
	const { updateTempConnection } = useDispatch('quillSMTP/core');

	return (
		<Dialog
			open={mailerSlug === slug}
			onClose={() => {
				setStep(2)
				updateTempConnection(connectionId, {
					mailer: '',
					account_id: '',
				});
			}}

		>
			<DialogTitle
				sx={{
					display: 'flex',
					justifyContent: 'space-between',
					alignItems: 'center',
					background:
						'linear-gradient(42deg, rgb(235, 54, 221), rgb(238, 142, 22))',
					color: '#fff',
					fontWeight: 'bold',
					marginBottom: '20px',
					'& .MuiSvgIcon-root': {
						color: '#fff',
					},
				}}
			>
				{mailer.name + __(' is a PRO feature', 'quillsmtp')}
				<IconButton
					aria-label="close"
					onClick={() => {
						updateTempConnection(connectionId, {
							mailer: '',
							account_id: '',
						});
						setStep(2);
					}}
				>
					<CloseIcon />
				</IconButton>
			</DialogTitle>
			<DialogContent>
				<MailerFeatureAvailability
					mailerSlug={slug}
					showLockIcon={true}
				/>
			</DialogContent>
		</Dialog>
	);
};

export default SettingsRender;
