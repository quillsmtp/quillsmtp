/**
 * WordPress Dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import { isEmpty } from 'lodash';
import React from 'react';
import { ThreeDots as Loader } from 'react-loader-spinner';
import { css } from '@emotion/css';
import Button from '@mui/material/Button';
import IconButton from '@mui/material/IconButton';
import EditIcon from '@mui/icons-material/Edit';
import CircularProgress from '@mui/material/CircularProgress';
import { Tooltip } from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogTitle from '@mui/material/DialogTitle';
import { LoadingButton } from '@mui/lab';
import Snackbar from '@mui/material/Snackbar';
import MuiAlert, { AlertProps } from '@mui/material/Alert';

/**
 * Internal Dependencies
 */
import type { Setup as SetupType } from '../../../types';
import { EditApp } from '../../account-edit';
import './style.scss';

const Alert = React.forwardRef<HTMLDivElement, AlertProps>(
	function Alert(props, ref) {
		return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
	}
);

interface Props {
	connectionId: string;
	setup: SetupType;
}

const App: React.FC<Props> = ({ connectionId, setup }) => {
	// context.
	const { connection, getMailerApp } = useSelect((select) => {
		return {
			connection: select('quillSMTP/core').getConnection(connectionId),
			getMailerApp: select('quillSMTP/core').getMailerApp,
		};
	});
	const { setupApp, setupAccounts } = useDispatch('quillSMTP/core');

	// state.
	const app = getMailerApp(connection.mailer);
	const [isLoaded, setIsLoaded] = useState(false);
	const [disconnectModal, setDisconnectModal] = useState(false);
	const [disconnecting, setDisconnecting] = useState(false);
	const [disconnectError, setDisconnectError] = useState(false);
	const [disconnectSuccess, setDisconnectSuccess] = useState(false);
	const [editApp, setEditApp] = useState(false);
	const [editingApp, setEditingApp] = useState(false);

	// load app data.
	useEffect(() => {
		if (!isEmpty(app)) {
			setIsLoaded(true);
			return;
		}
		apiFetch({
			path: `/qsmtp/v1/mailers/${connection.mailer}/settings`,
			method: 'GET',
		})
			.then((res: any) => {
				setupApp(connection.mailer, res.app);
				setIsLoaded(true);
			})
			.catch((err) => {
				console.error(err);
			});
	}, []);

	// for ts. won't be reached normally.
	if (!setup) return null;

	let $field: any = false;
	for (const [id, field] of Object.entries(setup.fields)) {
		if (field.check) {
			$field = {
				label: field.label,
				value: app[id] ?? '',
			};
		}
	}

	const disconnect = () => {
		if (disconnecting) return;
		setDisconnecting(true);
		apiFetch({
			path: `/qsmtp/v1/mailers/${connection.mailer}/settings`,
			method: 'DELETE',
			parse: false,
		})
			.then(() => {
				setDisconnecting(false);
				setDisconnectModal(false);
				setDisconnectSuccess(true);
				setupApp(connection.mailer, {});
				setupAccounts(connection.mailer, {});
			})
			// @ts-ignore
			.catch((err) => {
				setDisconnectError(true);
				setDisconnecting(false);
			});
	};

	if (!isLoaded) {
		return (
			<div
				className={css`
					display: flex;
					flex-wrap: wrap;
					width: 100%;
					justify-content: center;
					align-items: center;
				`}
			>
				<Loader color="#8640e3" height={50} width={50} />
			</div>
		);
	}

	return (
		<div className="mailer-settings-main-app">
			<b>{__('App Settings', 'quillsmtp')}:</b>
			<div className="mailer-settings-main-app__content">
				<div>
					{$field && (
						<>
							{$field.label}:{' '}
							<span style={{ wordBreak: 'break-all' }}>
								{$field.value}
							</span>
						</>
					)}
				</div>
				<div
					className={css`
						margin-right: 10px;
					`}
				>
					<Tooltip title={__('Edit', 'quillsmtp')}>
						<IconButton
							onClick={() => setEditApp(true)}
							color={editApp ? 'primary' : 'default'}
						>
							{editingApp ? (
								<CircularProgress size={20} />
							) : (
								<EditIcon />
							)}
						</IconButton>
					</Tooltip>
				</div>
				<div>
					<Tooltip title={__('Disconnect', 'quillsmtp')}>
						<IconButton
							onClick={() => setDisconnectModal(true)}
							disabled={disconnecting}
							color="error"
						>
							<DeleteIcon />
						</IconButton>
					</Tooltip>
				</div>
			</div>
			{editApp && (
				<div
					className={css`
						margin-bottom: 20px;
					`}
				>
					<EditApp
						connectionId={connectionId}
						fields={setup.fields}
						values={app}
						onEditing={setEditingApp}
						isEditing={editingApp}
						onCancel={() => setEditApp(false)}
					/>
				</div>
			)}
			{disconnectError && (
				<Snackbar
					open={disconnectError}
					autoHideDuration={6000}
					onClose={() => setDisconnectError(false)}
				>
					<Alert severity="error">
						{__('Error on disconnecting the app!', 'quillsmtp')}
					</Alert>
				</Snackbar>
			)}
			{disconnectSuccess && (
				<Snackbar
					open={disconnectSuccess}
					autoHideDuration={6000}
					onClose={() => setDisconnectSuccess(false)}
				>
					<Alert severity="success">
						{__('App deleted successfully!', 'quillsmtp')}
					</Alert>
				</Snackbar>
			)}
			{disconnectModal && (
				<Dialog
					open={disconnectModal}
					onClose={() => setDisconnectModal(false)}
				>
					<DialogTitle>
						{__('Disconnect App', 'quillsmtp')}
					</DialogTitle>
					<DialogContent>
						<div>
							{__(
								'Are you sure you want to delete the app settings',
								'quillsmtp'
							)}{' '}
							<b>{__('with all accounts', 'quillsmtp')}</b>
						</div>
						<br />
						<div>
							{__(
								'Are you sure you want to proceed?',
								'quillsmtp'
							)}
						</div>
					</DialogContent>
					<DialogActions>
						<Button
							onClick={() => setDisconnectModal(false)}
							disabled={disconnecting}
						>
							{__('Cancel', 'quillsmtp')}
						</Button>
						<LoadingButton
							onClick={disconnect}
							loading={disconnecting}
							variant="contained"
							color="error"
						>
							{__('Disconnect', 'quillsmtp')}
						</LoadingButton>
					</DialogActions>
				</Dialog>
			)}
		</div>
	);
};

export default App;
