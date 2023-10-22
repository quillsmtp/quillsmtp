/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';

/**
 * External Dependencies
 */
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
import DeleteIcon from '@mui/icons-material/Delete';
import ViewIcon from '@mui/icons-material/Visibility';
import Chip from '@mui/material/Chip';
import { Stack } from '@mui/material';
import { css } from '@emotion/css';
import { ThreeDots as Loader } from 'react-loader-spinner';

/**
 * Internal Dependencies
 */
import './style.scss';

interface Log {
	log_id: number;
	level: string;
	message: string;
	source: string;
	datetime: string;
	local_datetime: string;
	context: {
		code: string;
		connection_id: string;
		connection_name: string;
		provider: string;
		email_details: {
			from: string;
			to: string;
			cc: string;
			bcc: string;
			reply_to: string;
			subject: string;
			headers: string;
			plain: string;
			html: string;
			attachments: string;
		};
	};
}

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

const columns: readonly Column[] = [
	{ id: 'subject', label: __('Subject', 'quillsmtp'), minWidth: 100 },
	{ id: 'to', label: __('To', 'quillsmtp'), minWidth: 100 },
	{ id: 'level', label: __('Level', 'quillsmtp'), minWidth: 100 },
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

	const [logs, setLogs] = useState<Log[] | null>(null); // null for loading, false for error empty array for empty list
	const [modalLogId, setModalLogId] = useState<number | null>(null); // null for no log to show

	useEffect(() => {
		setIsLoading(true);
		apiFetch({
			path: `/qsmtp/v1/logs?page=${page}&per_page=${perPage}`,
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
	}, [page, perPage]);

	const logsClear = () => {
		apiFetch({
			path: `/qsmtp/v1/logs`,
			method: 'DELETE',
		}).then(() => {
			setPage(1);
			setLogs(null);
		});
	};

	const getLogLevel = (level) => {
		switch (level) {
			case 'error':
				return <Chip label={__('Error', 'quillforms')} color="error" />;
			case 'info':
				return (
					<Chip label={__('Sent', 'quillforms')} color="success" />
				);
			default:
				return (
					<Chip label={__('Debug', 'quillforms')} color="default" />
				);
		}
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
				<>
					<TableContainer component={Paper}>
						<Table
							sx={{ minWidth: 500 }}
							aria-label="custom pagination table"
						>
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
							<TableBody>
								{isLoading && (
									<LoadingRows colSpan={6} count={perPage} />
								)}
								{!isLoading &&
									logs.map((log) => (
										<TableRow key={log.context.code}>
											<TableCell
												component="th"
												scope="row"
											>
												{
													log.context.email_details
														.subject
												}
											</TableCell>
											<TableCell align="left">
												{log.context.email_details.to}
											</TableCell>
											<TableCell align="left">
												{getLogLevel(log.level)}
											</TableCell>
											<TableCell align="left">
												{log.local_datetime}
											</TableCell>
											<TableCell align="left">
												<Stack
													direction="row"
													spacing={1}
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
													<IconButton
														aria-label={__(
															'Delete log',
															'quillsmtp'
														)}
														onClick={() => {}}
														color="error"
													>
														<DeleteIcon />
													</IconButton>
												</Stack>
											</TableCell>
										</TableRow>
									))}
							</TableBody>
							<TableFooter>
								<TableRow>
									<TablePagination
										rowsPerPageOptions={[10, 25, 100]}
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
				</>
			)}
		</div>
	);
};

export default Logs;
