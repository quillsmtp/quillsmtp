/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { addQueryArgs } from '@wordpress/url';

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
import { Button, Card, CardContent, Stack, Select, MenuItem, Tooltip } from '@mui/material';
import { css } from '@emotion/css';
import { ThreeDots as Loader } from 'react-loader-spinner';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import Modal from '@mui/material/Modal';
import LinearProgress from '@mui/material/LinearProgress';
import Typography from '@mui/material/Typography';
import { MdRefresh } from "react-icons/md";
import { PiExportThin } from "react-icons/pi";
import { RiDeleteBinLine } from "react-icons/ri";
import { MdOutlineRemoveRedEye } from "react-icons/md";
import DeleteIcon from '@mui/icons-material/Delete';
import ViewIcon from '@mui/icons-material/Visibility';
import DraftsIcon from '@mui/icons-material/Drafts';
/**
 * Internal Dependencies
 */
import { Log } from './types';
import './style.scss';
import { LoadingButton } from '@mui/lab';
import { Delete } from '@mui/icons-material';

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
	id: 'source' | 'level' | 'message' | 'datetime' | 'actions';
	label: string;
	minWidth?: number;
	align?: 'right';
	format?: (value: number) => string;
}

const Chip = styled(MuiChip)(() => ({
	height: 22,
}));

const TablePaginationActions = (props: TablePaginationActionsProps & { disabled?: boolean }) => {
	const theme = useTheme();
	const { count, page, rowsPerPage, onPageChange, disabled } = props;
	const totalPages = Math.ceil(props.count / props.rowsPerPage);

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
			{/* <IconButton
				onClick={handleFirstPageButtonClick}
				disabled={page === 0}
				aria-label="first page"
			>
				{theme.direction === 'rtl' ? (
					<LastPageIcon />
				) : (
					<FirstPageIcon />
				)}
			</IconButton> */}
			{/* <IconButton
				onClick={handleBackButtonClick}
				disabled={page === 1}
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
			</IconButton> */}
			<IconButton onClick={(event) => onPageChange(event, page - 1)} disabled={props.disabled || page === 1} aria-label="previous page">
				<KeyboardArrowLeft className='bg-[#333333] text-white rounded-full' />
			</IconButton>
			<IconButton
				onClick={(event) => onPageChange(event, page + 1)}
				disabled={props.disabled || page >= totalPages}
				aria-label="next page"
			>
				<KeyboardArrowRight className='bg-[#333333] text-white rounded-full' />
			</IconButton>
			{/* <IconButton
				onClick={handleLastPageButtonClick}
				disabled={page >= Math.ceil(count / rowsPerPage) - 1}
				aria-label="last page"
			>
				{theme.direction === 'rtl' ? (
					<FirstPageIcon />
				) : (
					<LastPageIcon />
				)}
			</IconButton> */}
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
		padding: '20px 30px'
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
	const { createNotice } = useDispatch('quillSMTP/core');
	const [deleteAll, setDeleteAll] = useState<boolean>(false);
	const [refreshLogs, setRefreshLogs] = useState<boolean>(false);
	const [isPreparingDownload, setIsPreparingDownload] = useState(false);
	const [progress, setProgress] = useState(0);
	const [deleteLogId, setDeleteLogId] = useState<number | null>(null); // null for no log to clear

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

	const totalPages = Math.ceil(count / perPage);

	const handlePageChange = (event) => {
		setPage(event.target.value + 1);
	};

	// Handle previous and next page buttons
	const handlePrevPage = () => {
		setPage(Math.max(page - 1, 1));
	};

	const handleNextPage = () => {
		setPage(Math.min(page + 1, totalPages));
	};

	const logsDelete = (id) => {
		if (isDeleting || !logs) return;
		setIsDeleting(true);
		apiFetch({
			path: `/qsmtp/v1/email-logs/${id}`,
			method: 'DELETE',
		})
			.then((res: any) => {
				if (res.success) {
					const newLogs = logs.filter((log) => log.log_id !== id);
					setLogs(newLogs);
					setIsDeleting(false);
					setDeleteLogId(null);
					createNotice({
						type: 'success',
						message: __('Log deleted successfully.', 'quillsmtp'),
					});
				} else {
					setIsDeleting(false);
					setDeleteLogId(null);
				}
			})
			.catch(() => {
				setIsDeleting(false);
				setDeleteLogId(null);
			});
	};

	const exportLogs = (offset = 0, file_id = null) => {
		setIsPreparingDownload(true);
		apiFetch({
			path: addQueryArgs(`/qsmtp/v1/logs/export`, {
				offset: offset,
				file_id: file_id,
				status: status,
			}),
			method: 'GET',
			parse: false,
		})
			.then((res: any) => res.json()).then((res: any) => {
				const response = res;
				const { status, offset, file_id, progress: exportProgress } = response;

				if (status === 'continue') {
					setProgress(exportProgress);
					exportLogs(offset, file_id);
				} else if (status === 'done') {
					donwloadFile(file_id);
				}
			}).catch(() => {
				setIsPreparingDownload(false);
				setProgress(0);
				createNotice({
					type: 'error',
					message: __('Error while exporting logs.', 'quillsmtp'),
				});
			});
	};

	const donwloadFile = (file_id) => {
		apiFetch({
			path: addQueryArgs(`/qsmtp/v1/logs/export`, {
				download: 'json',
				file_id: file_id,
			}),
			method: 'GET',
			parse: false,
		}).then((res: any) => res.blob()).then((blob) => {
			const url = window.URL.createObjectURL(blob);
			const a = document.createElement('a');
			a.href = url;
			const fileName = `quillsmtp-export.json`;
			a.download = fileName;
			document.body.appendChild(a);
			a.click();
			window.URL.revokeObjectURL(url);
			setProgress(100);
			setTimeout(() => {
				setIsPreparingDownload(false);
				setProgress(0);
			}, 0);
		}).catch(() => {
			setIsPreparingDownload(false);
			setProgress(0);
			createNotice({
				type: 'error',
				message: __('Error while downloading file.', 'quillsmtp'),
			});
		});
	};

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
			<div className="pl-0">
				<div className="font-roboto font-[500] text-[38px] text-[#333333] pb-2">
					{__('Debug', 'quillsmtp')}
				</div>
			</div>
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
					{/* <div className="qsmtp-debug__header">
						<Stack
							direction="row"
							spacing={1}
							justifyContent={'end'}
							className='pt-7'
						>
							<Button
								variant="contained"
								onClick={() => setRefreshLogs(!refreshLogs)}
								disabled={isLoading}
								color="primary"
								className='bg-[#333333] normal-case font-roboto'
							>
								<MdRefresh className='mr-2 text-[18px]' />
								{__('Refresh', 'quillsmtp')}
							</Button>
							{/* <Button
								variant="contained"
								onClick={() => setDeleteAll(true)}
								disabled={isLoading}
								color="error"
							>
								{__('Clear Logs', 'quillsmtp')}
							</Button> 
							<Button
								variant="outlined"
								onClick={() => exportLogs()}
								disabled={isPreparingDownload}
								color="primary"
								className='bg-[#3858E9] normal-case font-roboto px-8 py-2 text-white hover:text-black'
							>
								<PiExportThin className='mr-2 text-[18px]' />
								{__('Export logs', 'quillsmtp')}
							</Button>
							{isPreparingDownload && (
								<Modal
									open={isPreparingDownload}
									onClose={() => null}
									aria-labelledby="modal-modal-title"
									aria-describedby="modal-modal-description"
								>
									<Box
										sx={{
											position: 'absolute',
											top: '50%',
											left: '50%',
											transform: 'translate(-50%, -50%)',
											width: 400,
											bgcolor: 'background.paper',
											border: '2px solid #000',
											boxShadow: 24,
											p: 4,
										}}
									>
										<Box sx={{ width: '100%', mr: 1 }}>
											<LinearProgress variant="determinate" value={progress} />
										</Box>
										<Box sx={{ minWidth: 35 }}>
											<Typography
												variant="body2"
												sx={{ color: 'text.secondary' }}
											>{`${Math.round(progress)}%`}</Typography>
										</Box>
									</Box>
								</Modal>
							)}
						</Stack>
					</div> */}
					<Card className='mb-20 mt-10'>
						<CardContent className='border shadow-none'>
							<div className='border-b pb-3 mb-7 py-0 flex justify-between items-center'>
								<div>
									<Button
										variant="contained"
										onClick={() => setDeleteAll(true)}
										disabled={isLoading}
										color="error"
										sx={{
											backgroundColor: "transparent", // Make the background transparent
											border: "1px solid #E52747", // Set the border color
											color: "#E52747", // Default text color
											boxShadow: "none", // Remove shadow
											"&:hover": {
												backgroundColor: "#E52747", // Red background on hover
												color: "white", // White text on hover
												border: "1px solid #E52747", // Maintain border
											},
										}}
										className='normal-case'
									>
										<Delete className='mr-2 text-[18px]' />
										{__('Clear Logs', 'quillsmtp')}
									</Button>
								</div>
								<div>
									<Stack
										direction="row"
										spacing={1}
										justifyContent={'end'}
									>
										<Button
											variant="contained"
											onClick={() => setRefreshLogs(!refreshLogs)}
											disabled={isLoading}
											color="primary"
											className='bg-[#333333] normal-case font-roboto'
										>
											<MdRefresh className='mr-2 text-[18px]' />
											{__('Refresh', 'quillsmtp')}
										</Button>
										<Button
											variant="outlined"
											onClick={() => exportLogs()}
											disabled={isPreparingDownload}
											color="primary"
											className='bg-[#3858E9] normal-case font-roboto px-8 py-2 text-white hover:text-black'
										>
											<PiExportThin className='mr-2 text-[18px]' />
											{__('Export logs', 'quillsmtp')}
										</Button>
										{isPreparingDownload && (
											<Modal
												open={isPreparingDownload}
												onClose={() => null}
												aria-labelledby="modal-modal-title"
												aria-describedby="modal-modal-description"
											>
												<Box
													sx={{
														position: 'absolute',
														top: '50%',
														left: '50%',
														transform: 'translate(-50%, -50%)',
														width: 400,
														bgcolor: 'background.paper',
														border: '2px solid #000',
														boxShadow: 24,
														p: 4,
													}}
												>
													<Box sx={{ width: '100%', mr: 1 }}>
														<LinearProgress variant="determinate" value={progress} />
													</Box>
													<Box sx={{ minWidth: 35 }}>
														<Typography
															variant="body2"
															sx={{ color: 'text.secondary' }}
														>{`${Math.round(progress)}%`}</Typography>
													</Box>
												</Box>
											</Modal>
										)}
									</Stack>
								</div>
							</div>
							<TableContainer component={Paper} className=''>
								<Table
									sx={{ minWidth: 500 }}
									aria-label="custom pagination table"
									className='qsmtp-table'
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
													className="border-r-[0.5px] border-b-[0.5px] border-[#9E9E9E] cursor-pointer"
													onClick={() =>
														setModalLogId(log.log_id)
													}
												>

													<TableCell
														component="th"
														scope="row"
														className='font-roboto text-[16px] pl-[30px]'
													>
														{log.source}
													</TableCell>
													<TableCell align="left">
														<span className='font-roboto text-[16px] text-[#3858E9] bg-[#7a8ee8] py-[5px] px-[24px] rounded-full'>{getLogLevel(log.level)}</span>
													</TableCell>
													<TableCell align="left" className='font-roboto text-[16px]'>
														{log.message}
													</TableCell>
													<TableCell align="left" className='font-roboto text-[16px]'>
														{log.local_datetime}
													</TableCell>
													<TableCell align='left' className='flex items-center gap-3'>
														{/* <RiDeleteBinLine className='text-[#E93838] bg-[#ea9797] rounded-full hover:text-white hover:bg-[#E93838]' />
														<MdOutlineRemoveRedEye className='text-[#333333] bg-[#b9b7b7] rounded-full hover:text-white hover:bg-[#333333]' /> */}
														<Stack direction="row"
															spacing={1}>
															<Tooltip
																title={__(
																	'View',
																	'quillsmtp'
																)}
																placement="top"
															>
																<IconButton
																	aria-label={__(
																		'View log',
																		'quillsmtp'
																	)}
																	onClick={() =>
																		setModalLogId(
																			log.log_id
																		)
																	}
																	color="primary"
																>
																	<ViewIcon className='text-[#333333] bg-[#333333] bg-opacity-20 rounded-full p-[0.15rem]' fontSize='medium' />
																</IconButton>
															</Tooltip>
															<Tooltip
																title={__(
																	'Delete',
																	'quillsmtp'
																)}
																placement="top"
															>
																<IconButton
																	aria-label={__(
																		'Delete log',
																		'quillsmtp'
																	)}
																	onClick={() =>
																		setDeleteLogId(
																			log.log_id
																		)
																	}
																	color="error"
																>
																	<DeleteIcon className='text-[#E93838] bg-[#E93838] bg-opacity-20 rounded-full p-[0.15rem]' fontSize='medium' />
																</IconButton>
															</Tooltip>
														</Stack>
													</TableCell>
												</TableRow>
											))}
										{!isLoading && logs.length === 0 && (
											<TableRow>
												<TableCell colSpan={9} align='center'>
													<div className='flex flex-col items-center'>
														<DraftsIcon className='text-[#3858E9] opacity-20' sx={{ fontSize: "80px" }} />
														<span className='opacity-40 text-[16px] font-roboto'>No  Data</span>
													</div>
												</TableCell>
											</TableRow>
										)}
									</TableBody>
									{/* <TableFooter>
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
									</TableFooter> */}
								</Table>
							</TableContainer>
							<div className='pt-8 pb-4 flex gap-0 items-center justify-center'>
								<div className='flex items-center gap-2'>
									{totalPages > 0 ? (
										<Select
											value={page - 1}
											onChange={handlePageChange}
											sx={{ width: "fit", height: "25px", color: "white", backgroundColor: "#333333" }}
										>
											{[...Array(totalPages)].map((_, index) => (
												<MenuItem key={index} value={index}>
													{index + 1}
												</MenuItem>
											))}
										</Select>
									) : null}

									<span className='font-roboto text-[16px]'>
										{totalPages === 0 ? "0 of 0 pages" : `of ${totalPages} pages`}
									</span>
								</div>

								<TablePaginationActions
									count={count}
									page={page - 1}
									rowsPerPage={perPage}
									onPageChange={(_, newPage) => setPage(newPage + 1)}
									disabled={totalPages === 0} // Pass disabled prop to disable buttons
								/>
							</div>
						</CardContent>
					</Card>
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
