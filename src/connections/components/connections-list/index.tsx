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
import AddCircleIcon from '@mui/icons-material/AddCircle';
import EditIcon from '@mui/icons-material/Edit';
import { RiDeleteBinLine } from "react-icons/ri";

/**
 * Internal Dependencies
 */
import './style.scss';
import ConnectionCard from '../connection-card';
import { Icon } from '@wordpress/components';

import { plusCircle } from '@wordpress/icons';
import ControlPointRoundedIcon from '@mui/icons-material/ControlPointRounded';
import SetUpWizard from '../setupwizard';
import ConfigAPI from '@quillsmtp/config';
import { IconButton, Stack, styled, Table, TableBody, TableCell, tableCellClasses, TableContainer, TableHead, TableRow, Tooltip } from '@mui/material';
import { getMailerModules } from '@quillsmtp/mailers';
import ConnectionButtons from '../connection-buttons';

interface Column {
	id: 'provider' | 'name' | 'email' | 'actions';
	label: string;
	minWidth?: number;
	align?: 'right';
	format?: (value: number) => string;
}

const EnhancedTableHead = () => {
	return (
		<TableHead>
			<TableRow>
				{columns.map((column) => (
					<StyledTableCell
						key={column.id}
						component="th"
						scope="row"
						align="left"
					>
						{column.label}
					</StyledTableCell>
				))}
			</TableRow>
		</TableHead>
	);
};

const columns: readonly Column[] = [
	{
		id: 'provider',
		label: __('Provider', 'quillsmtp'),
		minWidth: 200,
	},
	{
		id: 'name',
		label: __('Connection Name', 'quillsmtp'),
		minWidth: 200,
	},
	{
		id: 'email',
		label: __('Sender', 'quillsmtp'),
		minWidth: 200,
	},
	{
		id: 'actions',
		label: __('Actions', 'quillsmtp'),
		minWidth: 200,
	},
];

const StyledTableCell = styled(TableCell)(({ theme }) => ({
	[`&.${tableCellClasses.head}`]: {
		backgroundColor: '#333333',
		color: theme.palette.common.white,
		fontWeight: '500',
		padding: '16px 30px'
	},
}));


const ConnectionsList: React.FC = () => {
	const { connectionsIds } = useSelect((select) => ({
		connectionsIds: select('quillSMTP/core').getConnectionsIds(),
	}));

	const mailerModules = getMailerModules();

	const connectionsDetails = useSelect((select) => {
		return connectionsIds.map((connectionId) => ({
			id: connectionId,
			connectionName: select("quillSMTP/core").getConnectionName(connectionId),
			email: select("quillSMTP/core").getConnectionFromEmail(connectionId) || "-",
			mailer: select("quillSMTP/core").getConnectionMailer(connectionId),
		}));
	});
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

	const [showTableView, setShowTableView] = useState(false);

	return (
		<Card
			className="border border-[#E0E0E0] pb-6"
			sx={{ width: '800px', maxWidth: '100%', margin: '0 auto' }}
		>
			<div className="px-[20px] py-3 text-[24px] border-b mb-2 border-[#E0E0E0] flex justify-between items-center">
				<div className="text-[#333333] font-[500] font-roboto">
					{__('Connections', 'quillsmtp')}
				</div>
				<Button
					className='text-white bg-[#3858E9] normal-case px-3 py-2 font-roboto hover:bg-white hover:text-[#3858E9]'
					onClick={() => setShowTableView(prev => !prev)}
				>
					{showTableView ? "Show Connections" : "Show Table"}
				</Button>
				{showTableView ? (
					<div>
						<Button className='text-white bg-[#3858E9] normal-case px-4 py-1 font-roboto hover:bg-white hover:text-[#3858E9] font-normal' onClick={() => {
							const randomId = () =>
								Math.random().toString(36).substr(2, 9);

							const connectionId = randomId();
							setNewConnectionId(connectionId);
							addConnection(connectionId, {
								name: sprintf(
									__('Connection Name #%s', 'quillsmtp'),
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
						}}>
							<ControlPointRoundedIcon className='pr-3 text-[40px]' />
							Add Connection
						</Button>
					</div>) : (<div></div>)}
			</div>
			<CardContent sx={{ padding: "20px 0" }}>
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
				{showTableView ? (

					<TableContainer sx={{ padding: "0px" }}>
						<Table
							sx={{ minWidth: 500 }}
							aria-label="custom pagination table"
							className='qsmtp-table'
						>
							<EnhancedTableHead />
							<TableBody>
								{connectionsDetails.map((conn) => (<TableRow key={conn.id}>
									<TableCell align='left' sx={{ padding: "10px 30px" }}>{conn.mailer && mailerModules[conn.mailer] && mailerModules[conn.mailer].icon && <img src={mailerModules[conn.mailer].icon} alt={conn.mailer} className='size-10' />}</TableCell>
									<TableCell align='left' sx={{ padding: "10px 30px" }}>{conn.connectionName}</TableCell>
									<TableCell align='left' sx={{ padding: "10px 30px" }}>{conn.email}</TableCell>
									<TableCell align="left" sx={{ padding: "10px 30px" }}>
										<Stack
											direction="row" gap={2} className='size-8'
										>
											<ConnectionButtons connectionId={conn.id}/>
											{/* <IconButton sx={{ padding: "0px" }} size="large">
												<EditIcon color="primary" className='rounded-full border border-[##E5E5E5] text-[#333333] p-1 hover:bg-[#333333] hover:text-white size-8' />
											</IconButton>
											<IconButton sx={{ size: "large", padding: "0px" }}>
												<RiDeleteBinLine className='rounded-full border border-[#E52747] border-opacity-20 text-[#E52747] p-1 hover:bg-[#E52747] hover:text-white size-8' />
											</IconButton> */}
										</Stack>
									</TableCell>
								</TableRow>
								))}
							</TableBody>
						</Table>
					</TableContainer>) : (

					<div className="qsmtp-connections-list">
						<div className="qsmtp-connections-list__add">
							<Card
								className="qsmtp-connections-list__add-card qsmtp-connection-card-add"
								onClick={() => {
									console.log("Button clicked!");
									const randomId = () =>
										Math.random().toString(36).substr(2, 9);

									const connectionId = randomId();
									console.log("Generated ID:", connectionId);
									setNewConnectionId(connectionId);
									addConnection(connectionId, {
										name: sprintf(
											__('Connection Name #%s', 'quillsmtp'),
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
										console.log("Opening SetUpWizard!");
										setSetUpWizard(true);
									}, 100);
								}}
							>
								<AddCircleIcon className='size-10' />
								{__('Add Connection', 'quillsmtp')}
							</Card>

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
						</div></div>)}
				<div>
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
				{/* </div> */}
			</CardContent>
		</Card>
	);
};

export default ConnectionsList;
