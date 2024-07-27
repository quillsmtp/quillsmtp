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
import { styled, alpha } from '@mui/material/styles';
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
import DeleteIcon from '@mui/icons-material/Delete';
import ViewIcon from '@mui/icons-material/Visibility';
import MuiChip from '@mui/material/Chip';
import { Button, Stack } from '@mui/material';
import { css } from '@emotion/css';
import { ThreeDots as Loader } from 'react-loader-spinner';
import SearchIcon from '@mui/icons-material/Search';
import DateIcon from '@mui/icons-material/DateRange';
import InputBase from '@mui/material/InputBase';
import { DateRangePicker } from 'react-date-range';
import classnames from 'classnames';
import Popover from '@mui/material/Popover';
import Checkbox from '@mui/material/Checkbox';
import FormControlLabel from '@mui/material/FormControlLabel';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import Tooltip from '@mui/material/Tooltip';
import ResendIcon from '@mui/icons-material/Refresh';

/**
 * Internal Dependencies
 */
import { Log } from './types';
import LogModal from './log-modal';
import './style.scss';
import AlertDialog from './alert-dialog';

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
	id: 'subject' | 'to' | 'level' | 'datetime' | 'actions';
	label: string;
	minWidth?: number;
	align?: 'right';
	format?: (value: number) => string;
}

const Search = styled('div')(({ theme }) => ({
	position: 'relative',
	borderRadius: theme.shape.borderRadius,
	backgroundColor: alpha(theme.palette.common.white, 0.15),
	'&:hover': {
		backgroundColor: alpha(theme.palette.common.white, 0.25),
	},
	marginRight: theme.spacing(2),
	marginLeft: 0,
	width: '100%',
	[theme.breakpoints.up('sm')]: {
		marginLeft: theme.spacing(3),
		width: 'auto',
	},
}));

const SearchIconWrapper = styled('div')(({ theme }) => ({
	padding: theme.spacing(0, 2),
	height: '100%',
	position: 'absolute',
	pointerEvents: 'none',
	display: 'flex',
	alignItems: 'center',
	justifyContent: 'center',
}));

const StyledInputBase = styled(InputBase)(({ theme }) => ({
	color: 'inherit',
	'& .MuiInputBase-input': {
		backgroundColor: 'transparent',
		minHeight: 'auto',
		padding: theme.spacing(1, 1, 1, 0),
		// vertical padding + font size from searchIcon
		paddingLeft: `calc(1em + ${theme.spacing(4)})`,
		transition: theme.transitions.create('width'),
		width: '100%',
		[theme.breakpoints.up('md')]: {
			width: '20ch',
		},
	},
}));

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

interface EnhancedTableProps {
	numSelected: number;
	onSelectAllClick: (event: React.ChangeEvent<HTMLInputElement>) => void;
	rowCount: number;
}

const EnhancedTableHead = (props: EnhancedTableProps) => {
	const { onSelectAllClick, numSelected, rowCount } = props;

	return (
		<TableHead>
			<TableRow>
				<StyledTableCell padding="checkbox">
					<FormControlLabel
						sx={{
							margin: '0',
						}}
						control={
							<Checkbox
								color="primary"
								indeterminate={
									numSelected > 0 && numSelected < rowCount
								}
								checked={
									rowCount > 0 && numSelected === rowCount
								}
								onChange={onSelectAllClick}
								inputProps={{
									'aria-label': __(
										'select all logs',
										'quillsmtp'
									),
								}}
							/>
						}
						label={''}
					/>
				</StyledTableCell>
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
	{ id: 'subject', label: __('Subject', 'quillsmtp'), minWidth: 100 },
	{ id: 'to', label: __('To', 'quillsmtp'), minWidth: 100 },
	{ id: 'level', label: __('Status', 'quillsmtp'), minWidth: 100 },
	{ id: 'datetime', label: __('Date', 'quillsmtp'), minWidth: 100 },
	{ id: 'actions', label: __('Actions', 'quillsmtp'), minWidth: 100 },
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
					<div className="qsmtp-logs__loading-row-bar" />
				</TableCell>
			</TableRow>
		);
	}

	return <>{rows}</>;
};

const Logs: React.FC = () => {
	const [page, setPage] = useState<number>(1);
	const [perPage, setPerPage] = useState<number>(10);
	const [count, setCount] = useState<number>(0); // total count of logs
	const [isLoading, setIsLoading] = useState<boolean>(false);
	const [deleteLogId, setDeleteLogId] = useState<number | null>(null); // null for no log to clear
	const [isDeleting, setIsDeleting] = useState<boolean>(false); // null for no log to clear
	const [logs, setLogs] = useState<Log[] | null>(null); // null for loading, false for error empty array for empty list
	const [modalLogId, setModalLogId] = useState<number | null>(null); // null for no log to show
	const [currentFilter, setCurrentFilter] = useState<string>('all');
	const [openDateRangePicker, setOpenDateRangePicker] =
		useState<boolean>(false);
	const [dateRange, setDateRange] = useState<any>({});
	const [search, setSearch] = useState<string>('');
	const [selectedLogs, setSelectedLogs] = useState<number[]>([]);
	const { createNotice } = useDispatch('quillSMTP/core');
	const [deleteAll, setDeleteAll] = useState<boolean>(false);
	const [selectedAction, setSelectedAction] = useState<string>('');
	const [deleteSelected, setDeleteSelected] = useState<boolean>(false);
	const [isDeletingSelected, setIsDeletingSelected] =
		useState<boolean>(false);
	const [isResending, setIsResending] = useState<boolean>(false);
	const [refreshLogs, setRefreshLogs] = useState<boolean>(false);

	useEffect(() => {
		setIsLoading(true);
		let path = `/qsmtp/v1/email-logs?page=${page}&per_page=${perPage}`;
		if (currentFilter !== 'all') {
			path += `&status=${currentFilter}`;
		}
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
	}, [page, perPage, currentFilter, refreshLogs]);

	const filterLogsByDate = () => {
		if (!dateRange?.startDate || !dateRange?.endDate || isLoading) return;
		setIsLoading(true);
		const startDate = dateRange.startDate.toLocaleDateString();
		const endDate = dateRange.endDate.toLocaleDateString();

		apiFetch({
			path: `/qsmtp/v1/email-logs?start_date=${startDate}&end_date=${endDate}&page=${page}&per_page=${perPage}`,
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
	};

	const searchLogs = () => {
		if (!search || isLoading) return;
		setIsLoading(true);

		apiFetch({
			path: `/qsmtp/v1/email-logs?search=${search}&page=${page}&per_page=${perPage}`,
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
	};

	const logsClear = () => {
		if (isDeleting) return;
		setIsDeleting(true);
		apiFetch({
			path: `/qsmtp/v1/email-logs`,
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

	const handleDeleteSelected = () => {
		if (isDeletingSelected || !logs) return;
		setIsDeletingSelected(true);
		apiFetch({
			path: `/qsmtp/v1/email-logs`,
			method: 'DELETE',
			body: JSON.stringify({
				ids: selectedLogs,
			}),
		})
			.then((res: any) => {
				if (res.success) {
					const newLogs = logs.filter(
						(log) => !selectedLogs.includes(log.log_id)
					);
					setLogs(newLogs);
					setIsDeletingSelected(false);
					setSelectedLogs([]);
					setDeleteSelected(false);
					createNotice({
						type: 'success',
						message: __('Logs deleted successfully.', 'quillsmtp'),
					});
				} else {
					setIsDeletingSelected(false);
					setSelectedLogs([]);
					setDeleteSelected(false);
				}
			})
			.catch(() => {
				setIsDeletingSelected(false);
				setSelectedLogs([]);
				setDeleteSelected(false);
			});
	};

	const applyAction = () => {
		if (selectedAction === 'delete') {
			setDeleteSelected(true);
		} else if (selectedAction === 'resend') {
			resendLogs(selectedLogs);
		}
	};

	const getLogLevel = (level) => {
		switch (level) {
			case 'failed':
				return <Chip label={__('Failed', 'quillsmtp')} color="error" />;
			case 'succeeded':
				return <Chip label={__('Sent', 'quillsmtp')} color="success" />;
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

	const statusFilters: { label: string; value: string }[] = [
		{
			label: __('All', 'quillsmtp'),
			value: 'all',
		},
		{
			label: __('Successfull', 'quillsmtp'),
			value: 'succeeded',
		},
		{
			label: __('Failed', 'quillsmtp'),
			value: 'failed',
		},
	];

	const handleSelectAll = (e) => {
		if (!logs) return;

		if (e.target.checked) {
			const ids = logs.map((log) => log.log_id);
			setSelectedLogs(ids);
		} else {
			setSelectedLogs([]);
		}
	};

	const resendLogs = (ids) => {
		if (ids.length === 0 || isResending) return;
		setIsResending(true);
		const formData = new FormData();
		formData.append('ids', ids);

		apiFetch({
			path: `/qsmtp/v1/email-logs/resend`,
			method: 'POST',
			body: formData,
		})
			.then((res: any) => {
				if (res.success) {
					setRefreshLogs(!refreshLogs);
					setSelectedLogs([]);
				} else {
					createNotice({
						type: 'error',
						message: res.message,
					});
				}
				setIsResending(false);
			})
			.catch((e) => {
				createNotice({
					type: 'error',
					message: e.message,
				});
				setIsResending(false);
			});
	};

	return (
		<div className="qsmtp-logs">
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
					className="qsmtp-logs__wrap"
					style={{
						position: 'relative',
					}}
				>
					<div className="qsmtp-logs__header">

						<div className="qsmtp-logs__header-section">
							<h2>{__('Logs', 'quillsmtp')}</h2>
							<div className="qsmtp-logs__header-filters">
								{statusFilters.map((filter) => (
									<div
										className={classnames(
											'qsmtp-logs__header-filter',
											{
												'qsmtp-logs__header-filter--active':
													filter.value ===
													currentFilter,
											}
										)}
										key={filter.value}
										onClick={() =>
											setCurrentFilter(filter.value)
										}
									>
										{filter.label}
									</div>
								))}
							</div>
						</div>
						<div className="qsmtp-logs__header-section">
							<div
								className="qsmtp-logs__header-date-range"
								onClick={() => setOpenDateRangePicker(true)}
							>
								<div>
									<DateIcon />
									<span className="qsmtp-logs__header-date-range-label">
										{dateRange?.startDate?.toLocaleDateString() ||
											__('Start Date', 'quillsmtp')}
									</span>
								</div>
								<span>{__('To', 'quillsmtp')}</span>
								<div>
									<DateIcon />
									<span className="qsmtp-logs__header-date-range-label">
										{dateRange?.endDate?.toLocaleDateString() ||
											__('End Date', 'quillsmtp')}
									</span>
								</div>
							</div>
							<Button
								sx={{
									marginLeft: '10px',
								}}
								variant="outlined"
								onClick={() => filterLogsByDate()}
							>
								{__('Filter', 'quillsmtp')}
							</Button>
							<Popover
								open={openDateRangePicker}
								onClose={() => setOpenDateRangePicker(false)}
								anchorReference="anchorPosition"
								anchorPosition={{
									top: 200,
									left: 400,
								}}
							>
								<DateRangePicker
									onChange={(item) => {
										setDateRange({
											startDate: item.selection.startDate,
											endDate: item.selection.endDate,
										});
									}}
									showSelectionPreview={true}
									moveRangeOnFirstSelection={false}
									months={2}
									ranges={[
										{
											startDate:
												dateRange?.startDate ||
												new Date(),
											endDate:
												dateRange?.endDate ||
												new Date(),
											key: 'selection',
										},
									]}
								/>
							</Popover>
						</div>
						<div className="qsmtp-logs__header-section">
							<Search>
								<SearchIconWrapper>
									<SearchIcon />
								</SearchIconWrapper>
								<StyledInputBase
									placeholder={__('Search…', 'quillsmtp')}
									inputProps={{
										'aria-label': 'search',
									}}
									value={search}
									onChange={(e) => setSearch(e.target.value)}
								/>
							</Search>
							<Button
								sx={{
									marginLeft: '10px',
								}}
								variant="outlined"
								onClick={() => searchLogs()}
							>
								{__('Search', 'quillsmtp')}
							</Button>
						</div>
					</div>
					<TableContainer component={Paper}>
						<Table
							sx={{ minWidth: 500 }}
							aria-label="custom pagination table"
							className='qsmtp-table'
						>
							<EnhancedTableHead
								numSelected={selectedLogs.length}
								onSelectAllClick={handleSelectAll}
								rowCount={
									logs?.length < perPage
										? logs?.length
										: perPage
								}
							/>
							<TableBody>
								{(isLoading || isResending) && (
									<LoadingRows colSpan={6} count={perPage} />
								)}
								{!isLoading &&
									!isResending &&
									logs.map((log) => (
										<TableRow key={log.log_id}>
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
												{log.subject}
											</TableCell>
											<TableCell align="left">
												{log.recipients.to}
											</TableCell>
											<TableCell align="left">
												{getLogLevel(log.status)}
											</TableCell>
											<TableCell align="left">
												{log.local_datetime}
											</TableCell>
											<TableCell align="left">
												<Stack
													direction="row"
													spacing={1}
												>
													<Tooltip
														title={
															log.status ===
																'failed'
																? __(
																	'Retry',
																	'quillsmtp'
																)
																: __(
																	'Resend',
																	'quillsmtp'
																)
														}
														placement="top"
													>
														<Button
															variant="outlined"
															onClick={() =>
																resendLogs([
																	log.log_id,
																])
															}
															disabled={
																isResending
															}
															color={
																log.status ===
																	'failed'
																	? 'error'
																	: 'info'
															}
															startIcon={
																<ResendIcon />
															}
															size="small"
														>
															{log?.resend_count
																? `(${log?.resend_count})`
																: ''}{' '}
															{log.status ===
																'failed'
																? __(
																	'Retry',
																	'quillsmtp'
																)
																: __(
																	'Resend',
																	'quillsmtp'
																)}
														</Button>
													</Tooltip>
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
															<ViewIcon />
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
															<DeleteIcon />
														</IconButton>
													</Tooltip>
												</Stack>
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
			{deleteLogId !== null && (
				<AlertDialog
					open={deleteLogId !== null}
					title={__('Delete Log', 'quillsmtp')}
					text={__(
						'Are you sure you want to delete this log?',
						'quillsmtp'
					)}
					color="error"
					confirmText={__('Delete', 'quillsmtp')}
					loading={isDeleting}
					onClose={() => setDeleteLogId(null)}
					onConfirm={() => logsDelete(deleteLogId)}
				/>
			)}
			{deleteAll && (
				<AlertDialog
					open={deleteAll}
					title={__('Clear Logs', 'quillsmtp')}
					text={__(
						'Are you sure you want to clear all logs?',
						'quillsmtp'
					)}
					color="error"
					confirmText={__('Clear', 'quillsmtp')}
					loading={isDeleting}
					onClose={() => setDeleteAll(false)}
					onConfirm={() => logsClear()}
				/>
			)}
			{deleteSelected && (
				<AlertDialog
					open={deleteSelected}
					title={__('Delete Selected Logs', 'quillsmtp')}
					text={__(
						'Are you sure you want to delete selected logs?',
						'quillsmtp'
					)}
					color="error"
					confirmText={__('Delete', 'quillsmtp')}
					loading={isDeletingSelected}
					onClose={() => setDeleteSelected(false)}
					onConfirm={() => handleDeleteSelected()}
				/>
			)}
			{modalLogId !== null && (
				<LogModal
					log={getLogById(modalLogId)}
					open={modalLogId !== null}
					onClose={() => setModalLogId(null)}
				/>
			)}

			{selectedLogs.length > 0 && (
				<div className="qsmtp-logs__apply-actions-section">
					<FormControl

						sx={{
							minWidth: 120,
							"& .MuiOutlinedInput-notchedOutline": {
								borderColor: "white",
							},
							"&:hover > .MuiOutlinedInput-notchedOutline": {
								borderColor: "white"
							}
						}}
						size="small"
					>
						<InputLabel id="demo-simple-select-label">
							{__('Action', 'quillsmtp')}
						</InputLabel>
						<Select
							labelId="demo-simple-select-label"
							id="qsmtp-logs__apply-actions-select"
							value={selectedAction}
							label={__('Action', 'quillsmtp')}
							onChange={(event: SelectChangeEvent) =>
								setSelectedAction(
									event.target.value
								)
							}
							sx={{
								//color
								'& .MuiSelect-select': {
									color: '#fff',
									borderColor: '#fff'
								},
							}}
						>
							<MenuItem value={'delete'}>
								{__('Delete Selected', 'quillsmtp')}
							</MenuItem>
							<MenuItem value={'resend'}>
								{__('Resend Selected', 'quillsmtp')}
							</MenuItem>
						</Select>
					</FormControl>
					<Button
						sx={{
							marginLeft: '10px',
						}}
						variant="outlined"
						onClick={() => applyAction()}
						disabled={isDeleting}
						color="primary"
					>
						{__('Apply', 'quillsmtp')}
					</Button>
				</div>
			)}
		</div>
	);
};

export default Logs;
