/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import { LoadingButton } from '@mui/lab';
import Button from '@mui/material/Button';

interface Props {
	open: boolean;
	title: string;
	text: string;
	color:
		| 'primary'
		| 'secondary'
		| 'error'
		| 'info'
		| 'success'
		| 'warning'
		| undefined;
	confirmText: string | null;
	loading: boolean;
	onClose: () => void;
	onConfirm: () => void;
}

const AlertDialog: React.FC<Props> = (props) => {
	const {
		open,
		title,
		text,
		color,
		confirmText,
		loading,
		onClose,
		onConfirm,
	} = props;

	return (
		<Dialog open={open} onClose={onClose}>
			<DialogTitle>{title}</DialogTitle>
			<DialogContent>
				<DialogContentText>{text}</DialogContentText>
			</DialogContent>
			<DialogActions>
				<Button onClick={onClose}>{__('Cancel', 'quillsmtp')}</Button>
				<LoadingButton
					onClick={onConfirm}
					loading={loading}
					variant="contained"
					color={color}
				>
					{confirmText ? confirmText : __('Confirm', 'quillsmtp')}
				</LoadingButton>
			</DialogActions>
		</Dialog>
	);
};

export default AlertDialog;
