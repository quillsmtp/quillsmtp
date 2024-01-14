/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';

/**
 * External Dependencies
 */
import React from 'react';
import { useTheme } from '@mui/material/styles';
import { styled } from '@mui/material/styles';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell, { tableCellClasses } from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableFooter from '@mui/material/TableFooter';
import TablePagination from '@mui/material/TablePagination';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import IconButton from '@mui/material/IconButton';
import FirstPageIcon from '@mui/icons-material/FirstPage';
import KeyboardArrowLeft from '@mui/icons-material/KeyboardArrowLeft';
import KeyboardArrowRight from '@mui/icons-material/KeyboardArrowRight';
import LastPageIcon from '@mui/icons-material/LastPage';
import Box from '@mui/material/Box';
import TableHead from '@mui/material/TableHead';
import MuiChip from '@mui/material/Chip';
import { Button, Stack } from '@mui/material';
import { css } from '@emotion/css';
import { ThreeDots as Loader } from 'react-loader-spinner';
import Checkbox from '@mui/material/Checkbox';
import FormControlLabel from '@mui/material/FormControlLabel';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';

/**
 * Internal Dependencies
 */
import { Log } from './types';
import './style.scss';

interface TablePaginationActionsProps {
	count: number;
	page: number;
	rowsPerPage: number;
	onPageChange: (
		event: React.MouseEvent<HTMLButtonElement>,
		newPage: number
	) => void;
}

interface Column {
	id: 'id' | 'source' | 'level' | 'message' | 'datetime';
	label: string;
	minWidth?: number;
	align?: 'right';
	format?: (value: number) => string;
}

const Chip = styled(MuiChip)(() => ({
	height: 22,
}));

const TablePaginationActions = (props: TablePaginationActionsProps) => {
	const theme = useTheme();
	const { count, page, rowsPerPage, onPageChange } = props;

	const handleFirstPageButtonClick = (
		event: React.MouseEvent<HTMLButtonElement>
	) => {
		onPageChange(event, 0);
	};

	const handleBackButtonClick = (
		event: React.MouseEvent<HTMLButtonElement>
	) => {
		onPageChange(event, page - 1);
	};

	const handleNextButtonClick = (
		event: React.MouseEvent<HTMLButtonElement>
	) => {
		onPageChange(event, page + 1);
	};

	const handleLastPageButtonClick = (
		event: React.MouseEvent<HTMLButtonElement>
	) => {
		onPageChange(event, Math.max(0, Math.ceil(count / rowsPerPage) - 1));
	};

	return (
		<Box sx={{ flexShrink: 0, ml: 2.5 }}>
			<IconButton
				onClick={handleFirstPageButtonClick}
				disabled={page === 0}
				aria-label="first page"
			>
				{theme.direction === 'rtl' ? (
					<LastPageIcon />
				) : (
					<FirstPageIcon />
				)}
			</IconButton>
			<IconButton
				onClick={handleBackButtonClick}
				disabled={page === 0}
				aria-label="previous page"
			>
				{theme.direction === 'rtl' ? (
					<KeyboardArrowRight />
				) : (
					<KeyboardArrowLeft />
				)}
			</IconButton>
			<IconButton
				onClick={handleNextButtonClick}
				disabled={page >= Math.ceil(count / rowsPerPage) - 1}
				aria-label="next page"
			>
				{theme.direction === 'rtl' ? (
					<KeyboardArrowLeft />
				) : (
					<KeyboardArrowRight />
				)}
			</IconButton>
			<IconButton
				onClick={handleLastPageButtonClick}
				disabled={page >= Math.ceil(count / rowsPerPage) - 1}
				aria-label="last page"
			>
				{theme.direction === 'rtl' ? (
					<FirstPageIcon />
				) : (
					<LastPageIcon />
				)}
			</IconButton>
		</Box>
	);
};

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
		id: 'id',
		label: __('ID', 'quillsmtp'),
		minWidth: 200,
	},
	{
		id: 'source',
		label: __('Source', 'quillsmtp'),
		minWidth: 200,
	},
	{
		id: 'level',
		label: __('Level', 'quillsmtp'),
		minWidth: 200,
	},
	{
		id: 'message',
		label: __('Message', 'quillsmtp'),
		minWidth: 200,
	},
	{
		id: 'datetime',
		label: __('Date', 'quillsmtp'),
		minWidth: 200,
	},
];

const StyledTableCell = styled(TableCell)(({ theme }) => ({
	[`&.${tableCellClasses.head}`]: {
		backgroundColor: '#8640e3',
		color: theme.palette.common.white,
		fontWeight: 'bold',
	},
}));

const LoadingRows = (props: { colSpan: number; count: number }) => {
	let rows: JSX.Element[] = [];

	for (let i = 0; i < props.count; i++) {
		rows.push(
			<TableRow key={i}>
				<TableCell colSpan={props.colSpan}>
					<div className="qsmtp-debug__loading-row-bar" />
				</TableCell>
			</TableRow>
		);
	}

	return <>{rows}</>;
};

const Debug: React.FC = () => {
	const [page, setPage] = useState<number>(1);
	const [perPage, setPerPage] = useState<number>(10);
	const [count, setCount] = useState<number>(0); // total count of logs
	const [isLoading, setIsLoading] = useState<boolean>(false);
	const [isDeleting, setIsDeleting] = useState<boolean>(false); // null for no log to clear
	const [logs, setLogs] = useState<Log[] | null>(null); // null for loading, false for error empty array for empty list
	const [modalLogId, setModalLogId] = useState<number | null>(null); // null for no log to show
	const [selectedLogs, setSelectedLogs] = useState<number[]>([]);
	const { createNotice } = useDispatch('quillSMTP/core');
	const [deleteAll, setDeleteAll] = useState<boolean>(false);
	const [refreshLogs, setRefreshLogs] = useState<boolean>(false);

	useEffect(() => {
		setIsLoading(true);
		let path = `/qsmtp/v1/logs?page=${page}&per_page=${perPage}`;
		apiFetch({
			path: path,
			method: 'GET',
		})
			.then((res: any) => {
				setLogs(res.items);
				setCount(res.total_items);
				setIsLoading(false);
			})
			.catch(() => {
				setLogs(null);
				setCount(0);
				setIsLoading(false);
			});
	}, [page, perPage, refreshLogs]);

	const logsClear = () => {
		if (isDeleting) return;
		setIsDeleting(true);
		apiFetch({
			path: `/qsmtp/v1/logs`,
			method: 'DELETE',
		}).then(() => {
			setPage(1);
			setLogs([]);
			setDeleteAll(false);
			createNotice({
				type: 'success',
				message: __('Logs cleared successfully.', 'quillsmtp'),
			});
		});
	};

	const logsExport = () => {
		apiFetch({
			path: `/qsmtp/v1/logs?export=json`,
			method: 'GET',
			parse: false,
		})
			.then((res: any) => res.blob())
			.then((blob) => {
				const url = window.URL.createObjectURL(blob);
				const a = document.createElement('a');
				a.style.display = 'none';
				a.href = url;
				a.download = 'Logs_Export.json';
				document.body.appendChild(a);
				a.click();
				window.URL.revokeObjectURL(url);
			});
	};

	const getLogLevel = (level) => {
		switch (level) {
			case 'info':
				return <Chip label={__('Info', 'quillsmtp')} color="info" />;
			case 'emergency':
				return (
					<Chip label={__('Emergency', 'quillsmtp')} color="error" />
				);
			case 'alert':
				return <Chip label={__('Alert', 'quillsmtp')} color="error" />;
			case 'critical':
				return (
					<Chip label={__('Critical', 'quillsmtp')} color="error" />
				);
			case 'error':
				return <Chip label={__('Error', 'quillsmtp')} color="error" />;
			case 'warning':
				return (
					<Chip label={__('Warning', 'quillsmtp')} color="warning" />
				);
			case 'notice':
				return <Chip label={__('Notice', 'quillsmtp')} color="info" />;
			case 'info':
				return <Chip label={__('Info', 'quillsmtp')} color="info" />;
			case 'debug':
				return (
					<Chip label={__('Debug', 'quillsmtp')} color="default" />
				);
			default:
				return (
					<Chip label={__('Debug', 'quillsmtp')} color="default" />
				);
		}
	};

	const getLogById = (id) => {
		if (logs === null) return null;
		const log = logs.find((log) => log.log_id === id);
		if (!log) return null;
		return log;
	};

	return (
		<div className="qsmtp-debug">
			{logs === null && (
				<div
					className={css`
						display: flex;
						flex-wrap: wrap;
						width: 100%;
						height: 100px;
						justify-content: center;
						align-items: center;
					`}
				>
					<Loader color="#8640e3" height={50} width={50} />
				</div>
			)}
			{logs !== null && (
				<div
					className="qsmtp-debug__wrap"
					style={{
						position: 'relative',
					}}
				>
					<div className="qsmtp-debug__header">
						<Stack
							direction="row"
							spacing={1}
							justifyContent={'center'}
						>
							<Button
								variant="contained"
								onClick={() => setRefreshLogs(!refreshLogs)}
								disabled={isLoading}
								color="primary"
							>
								{__('Refresh', 'quillsmtp')}
							</Button>
							<Button
								variant="contained"
								onClick={() => setDeleteAll(true)}
								disabled={isLoading}
								color="error"
							>
								{__('Clear Logs', 'quillsmtp')}
							</Button>
							<Button
								variant="contained"
								onClick={() => logsExport()}
								disabled={isLoading}
								color="primary"
							>
								{__('Export', 'quillsmtp')}
							</Button>
						</Stack>
					</div>
					<TableContainer component={Paper}>
						<Table
							sx={{ minWidth: 500 }}
							aria-label="custom pagination table"
						>
							<EnhancedTableHead />
							<TableBody>
								{isLoading && (
									<LoadingRows colSpan={6} count={perPage} />
								)}
								{!isLoading &&
									logs.map((log) => (
										<TableRow
											key={log.log_id}
											className={css`
												cursor: pointer;
												&:hover {
													background-color: #f5f5f5;
												}
											`}
											onClick={() =>
												setModalLogId(log.log_id)
											}
										>
											<TableCell
												component="th"
												scope="row"
												padding="checkbox"
											>
												<FormControlLabel
													sx={{
														margin: '0',
													}}
													control={
														<Checkbox
															color="primary"
															checked={selectedLogs.includes(
																log.log_id
															)}
															onChange={(e) => {
																if (
																	e.target
																		.checked
																) {
																	setSelectedLogs(
																		[
																			...selectedLogs,
																			log.log_id,
																		]
																	);
																} else {
																	setSelectedLogs(
																		selectedLogs.filter(
																			(
																				id
																			) =>
																				id !==
																				log.log_id
																		)
																	);
																}
															}}
															inputProps={{
																'aria-label':
																	__(
																		'select log',
																		'quillsmtp'
																	),
															}}
														/>
													}
													label={''}
												/>
											</TableCell>
											<TableCell
												component="th"
												scope="row"
											>
												{log.source}
											</TableCell>
											<TableCell align="left">
												{getLogLevel(log.level)}
											</TableCell>
											<TableCell align="left">
												{log.message}
											</TableCell>
											<TableCell align="left">
												{log.local_datetime}
											</TableCell>
										</TableRow>
									))}
								{!isLoading && logs.length === 0 && (
									<TableRow>
										<TableCell colSpan={6} align="center">
											{__('No logs found', 'quillsmtp')}
										</TableCell>
									</TableRow>
								)}
							</TableBody>
							<TableFooter>
								<TableRow>
									<TablePagination
										rowsPerPageOptions={[5, 10, 25, 100]}
										colSpan={6}
										count={count}
										rowsPerPage={perPage}
										page={page - 1}
										SelectProps={{
											inputProps: {
												'aria-label': 'rows per page',
											},
											native: true,
										}}
										onPageChange={(
											// @ts-ignore
											event: any,
											newPage: number
										) => {
											setPage(newPage + 1);
										}}
										onRowsPerPageChange={(event: any) => {
											setPerPage(
												parseInt(event.target.value, 10)
											);
											setPage(1);
										}}
										ActionsComponent={
											TablePaginationActions
										}
									/>
								</TableRow>
							</TableFooter>
						</Table>
					</TableContainer>
					{logs && logs.length > 0 && (
						<Button
							variant="outlined"
							onClick={() => setDeleteAll(true)}
							disabled={isLoading}
							color="error"
							sx={{
								position: 'absolute',
								bottom: '5px',
								left: '5px',
							}}
						>
							{__('Clear Logs', 'quillsmtp')}
						</Button>
					)}
				</div>
			)}
			{modalLogId !== null && (
				<Dialog
					open={modalLogId !== null}
					onClose={() => setModalLogId(null)}
					aria-labelledby="alert-dialog-title"
					aria-describedby="alert-dialog-description"
					fullWidth
					maxWidth="md"
				>
					<DialogTitle id="alert-dialog-title">
						{__('Log Details', 'quillsmtp')}
					</DialogTitle>
					<DialogContent>
						<DialogContentText id="alert-dialog-description">
							<pre>
								{JSON.stringify(
									getLogById(modalLogId),
									null,
									2
								)}
							</pre>
						</DialogContentText>
					</DialogContent>
					<DialogActions>
						<Button
							onClick={() => setModalLogId(null)}
							color="primary"
						>
							{__('Close', 'quillsmtp')}
						</Button>
					</DialogActions>
				</Dialog>
			)}
			{deleteAll && (
				<Dialog
					open={deleteAll}
					onClose={() => setDeleteAll(false)}
					aria-labelledby="alert-dialog-title"
					aria-describedby="alert-dialog-description"
				>
					<DialogTitle id="alert-dialog-title">
						{__('Clear Logs', 'quillsmtp')}
					</DialogTitle>
					<DialogContent>
						<DialogContentText id="alert-dialog-description">
							{__(
								'Are you sure you want to clear all logs?',
								'quillsmtp'
							)}
						</DialogContentText>
					</DialogContent>
					<DialogActions>
						<Button
							onClick={() => setDeleteAll(false)}
							color="primary"
						>
							{__('Cancel', 'quillsmtp')}
						</Button>
						<Button onClick={logsClear} color="error" autoFocus>
							{__('Clear', 'quillsmtp')}
						</Button>
					</DialogActions>
				</Dialog>
			)}
		</div>
	);
};

export default Debug;
