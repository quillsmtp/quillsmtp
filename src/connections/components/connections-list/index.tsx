/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState, createPortal } from '@wordpress/element';
/**
 * External Dependencies
 */
import { map, size } from 'lodash';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Button from '@mui/material/Button';

/**
 * Internal Dependencies
 */
import './style.scss';
import ConnectionCard from '../connection-card';
import { Icon } from '@wordpress/components';

import { plusCircle } from '@wordpress/icons';
import SetUpWizard from '../setupwizard';
import ConfigAPI from '@quillsmtp/config';

const ConnectionsList: React.FC = () => {
	const { connectionsIds } = useSelect((select) => ({
		connectionsIds: select('quillSMTP/core').getConnectionsIds(),
	}));

	const [newConnectionId, setNewConnectionId] = useState('');
	const [setUpWizard, setSetUpWizard] = useState(false);
	const wpMailConfig = ConfigAPI.getWpMailConfig();
	const easySMTP = ConfigAPI.getEasySMTPConfig();
	const fluentSMTP = ConfigAPI.getFluentSMTPConfig();
	if (!connectionsIds) return null;
	const { addConnection, setInitialAccountData } =
		useDispatch('quillSMTP/core');

	const importFrom =
		(type: 'wpMailConfig' | 'easySMTP' | 'fluentSMTP') => () => {
			let data = null;

			switch (type) {
				case 'wpMailConfig':
					data = wpMailConfig;
					break;
				case 'easySMTP':
					data = easySMTP;
					break;
				case 'fluentSMTP':
					data = fluentSMTP;
					break;
			}

			if (!data) {
				return;
			}

			const {
				mailer,
				from_email,
				from_name,
				from_name_force,
				from_email_force,
			} = data;

			const randomId = () => Math.random().toString(36).substr(2, 9);
			const connectionId = randomId();
			setNewConnectionId(connectionId);
			setInitialAccountData(data[mailer]);
			addConnection(connectionId, {
				name: __('Connection #1', 'quillsmtp'),
				mailer,
				account_id: '',
				from_email,
				force_from_email: from_email_force,
				from_name,
				force_from_name: from_name_force,
			}, false);

			setTimeout(() => {
				setSetUpWizard(true);
			}, 100);
		};

	return (
		<Card
			className="qsmtp-connections-list-wrapper qsmtp-card"
			sx={{ width: '800px', maxWidth: '100%', margin: '0 auto' }}
		>
			<div className="qsmtp-card-header">
				<div className="qsmtp-card-header__title">
					{__('Connections', 'quillsmtp')}
				</div>
			</div>
			<CardContent>
				{size(connectionsIds) === 0 &&
					(wpMailConfig || fluentSMTP || easySMTP) && (
						<div
							className="qsmtp-connections-list__import"
							style={{
								marginTop: '20px',
								display: 'flex',
								gap: '10px',
							}}
						>
							{wpMailConfig && (
								<Button
									variant="contained"
									color="primary"
									onClick={importFrom('wpMailConfig')}
								>
									{__('Import from WP Mail', 'quillsmtp')}
								</Button>
							)}
							{easySMTP && (
								<Button
									variant="contained"
									color="primary"
									onClick={importFrom('easySMTP')}
								>
									{__(
										'Import from Easy Mail SMTP',
										'quillsmtp'
									)}
								</Button>
							)}
							{fluentSMTP && (
								<Button
									variant="contained"
									color="primary"
									onClick={importFrom('fluentSMTP')}
								>
									{__('Import from Fluent SMTP', 'quillsmtp')}
								</Button>
							)}
						</div>
					)}
				<div className="qsmtp-connections-list">
					<div className="qsmtp-connections-list__add">
						<Card
							className="qsmtp-connections-list__add-card qsmtp-connection-card"
							onClick={() => {
								const randomId = () =>
									Math.random().toString(36).substr(2, 9);

								const connectionId = randomId();
								setNewConnectionId(connectionId);
								addConnection(connectionId, {
									name: sprintf(
										__('Connection #%s', 'quillsmtp'),
										size(connectionsIds) + 1
									),
									mailer: '',
									account_id: '',
									from_email: '',
									force_from_email: false,
									from_name: '',
									force_from_name: false,
								}, false);

								setTimeout(() => {
									setSetUpWizard(true);
								}, 100);
							}}
						>
							<Icon icon={plusCircle} size={30} />
							{__('Add Connection', 'quillsmtp')}
						</Card>
					</div>
					{size(connectionsIds) > 0 && (
						<>
							{map(connectionsIds, (key, index) => {
								return (
									<ConnectionCard
										key={key}
										connectionId={key}
										index={index}
									/>
								);
							})}
						</>
					)}
					{setUpWizard &&
						newConnectionId &&
						createPortal(
							<SetUpWizard
								mode="add"
								connectionId={newConnectionId}
								setSetUpWizard={(value) => {
									setSetUpWizard(value);
									setInitialAccountData({});
								}}
								onSetupsComplete={() => {
									setSetUpWizard(false);
								}}
							/>,
							document.body
						)}
				</div>
			</CardContent>
		</Card>
	);
};

export default ConnectionsList;
