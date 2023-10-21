/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * External Dependencies
 */
import { map, keys, size } from 'lodash';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';

/**
 * Internal Dependencies
 */
import Connection from '../connection';
import './style.scss';

const ConnectionsList: React.FC = () => {
	const { connections } = useSelect((select) => ({
		connections: select('quillSMTP/core').getConnections(),
	}));

	if (!connections) return null;
	const { addConnection } = useDispatch('quillSMTP/core');
	const randomId = () => Math.random().toString(36).substr(2, 9);

	return (
		<Card sx={{ width: '800px', maxWidth: '100%', margin: '0 auto' }}>
			<div className="qsmtp-connections-list-header">
				<div className="qsmtp-connections-list-header__title">
					{__('Connections', 'quillsmtp')}
				</div>
			</div>
			<CardContent>
				{size(connections) > 0 && (
					<div className="qsmtp-connections-list">
						{map(keys(connections), (key, index) => {
							return (
								<Connection
									key={key}
									connectionId={key}
									index={index}
								/>
							);
						})}
					</div>
				)}

				<div className="qsmtp-connections-list__add">
					<Button
						variant="contained"
						onClick={() => {
							const connectionId = randomId();
							addConnection(connectionId, {
								name: __('New Connection', 'quillsmtp'),
								mailer: '',
								account_id: '',
								from_email: '',
								force_from_email: false,
								from_name: '',
								force_from_name: false,
							});
						}}
						startIcon={<AddIcon />}
					>
						{__('Add Connection', 'quillsmtp')}
					</Button>
				</div>
			</CardContent>
		</Card>
	);
};

export default ConnectionsList;
