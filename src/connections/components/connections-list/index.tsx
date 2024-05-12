/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState, createPortal } from "@wordpress/element";
/**
 * External Dependencies
 */
import { map, size } from 'lodash';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';

/**
 * Internal Dependencies
 */
import Connection from '../connection';
import './style.scss';
import ConnectionCard from '../connection-card';
import { Icon } from '@wordpress/components';

import { plusCircle } from '@wordpress/icons';
import SetUpWizard from '../setupwizard';

const ConnectionsList: React.FC = () => {
	const { connectionsIds } = useSelect((select) => ({
		connectionsIds: select('quillSMTP/core').getConnectionsIds(),
	}));

	const [newConnectionId, setNewConnectionId] = useState('');
	const [setUpWizard, setSetUpWizard] = useState(false);

	if (!connectionsIds) return null;
	const { addConnection } = useDispatch('quillSMTP/core');

	return (
		<Card className="qsmtp-connections-list-wrapper qsmtp-card" sx={{ width: '800px', maxWidth: '100%', margin: '0 auto' }}>
			<div className="qsmtp-card-header">
				<div className="qsmtp-card-header__title">
					{__('Connections', 'quillsmtp')}
				</div>
			</div>
			<CardContent>
				<div className="qsmtp-connections-list">

					<div className="qsmtp-connections-list__add">
						<Card
							className="qsmtp-connections-list__add-card qsmtp-connection-card"
							onClick={() => {
								const randomId = () => Math.random().toString(36).substr(2, 9);

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
								});

								setTimeout(() => {
									setSetUpWizard(true);
								}, 100);

							}}
						>
							<Icon
								icon={plusCircle}
								size={30}
							/>
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
							}
							)}
						</>
					)}

					{setUpWizard && newConnectionId && createPortal(
						<SetUpWizard
							mode="add"
							connectionId={newConnectionId}
							setSetUpWizard={setSetUpWizard}
							onSetupsComplete={() => {
								setSetUpWizard(false);

							}}
						/>,
						document.body
					)}
				</div>



			</CardContent>
		</Card >
	);
};

export default ConnectionsList;
