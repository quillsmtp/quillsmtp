import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { SelectChangeEvent } from '@mui/material';
import { addQueryArgs } from '@wordpress/url';

/**
 * External Dependencies
 */
import { DateRangePicker } from 'react-date-range';
import Popover from '@mui/material/Popover';
import { LoadingButton } from '@mui/lab';
import DateIcon from '@mui/icons-material/DateRange';
import FilterIcon from '@mui/icons-material/FilterAlt';
import { keys, map, isEmpty, size, groupBy, orderBy } from 'lodash';
import {
	Chart as ChartJS,
	LinearScale,
	CategoryScale,
	BarElement,
	PointElement,
	LineElement,
	ArcElement,
	Legend,
	Tooltip,
	LineController,
	BarController,
} from 'chart.js';
import { Chart, Line } from 'react-chartjs-2';
import { ThreeDots as Loader } from 'react-loader-spinner';
import { css } from '@emotion/css';
import classnames from 'classnames';
import Skeleton from '@mui/material/Skeleton';
/**
 * Internal Dependencies
 */
import './style.scss';
import WelcomePage from '../welcome-page';
import { GoMail } from "react-icons/go";
import { FaCheck } from "react-icons/fa";
import { IoClose } from "react-icons/io5";
import { MdArrowOutward } from "react-icons/md";
import { IoIosArrowDown } from "react-icons/io";
import { IoIosArrowRoundUp } from "react-icons/io";
import { FormControl, MenuItem, Select, Box } from '@mui/material';
import { getMailerModules } from '@quillsmtp/mailers';

ChartJS.register(
	LinearScale,
	CategoryScale,
	BarElement,
	PointElement,
	LineElement,
	ArcElement,
	Legend,
	Tooltip,
	LineController,
	BarController
);

// Define types
interface Logs {
	total?: number;
	success?: number;
	failed?: number;
	days?: {
		[key: string]: number;
	};
}

interface TopSender {
	from: string;
	count: number;
}

interface RecentLog {
	log_id: number;
	from: string;
	provider: string;
	provider_name: string;
	status: string;
	local_datetime: string;
}

const Home = () => {
	const { connections } = useSelect((select) => ({
		connections: select('quillSMTP/core').getConnections(),
	}));

	const [setUpWizard, setSetUpWizard] = useState<boolean>(false);

	const { hasConnectionsFinishedResolution } = useSelect((select) => {
		const { hasFinishedResolution } = select('quillSMTP/core');

		return {
			hasConnectionsFinishedResolution:
				hasFinishedResolution('getConnections'),
		};
	});

	const [logs, setLogs] = useState<Logs>({});
	const [selectedFilter, setSelectedFilter] = useState<string>("today");
	const [isLoadingMetrics, setIsLoadingMetrics] = useState<boolean>(false);
	const [isLoadingChart, setIsLoadingChart] = useState<boolean>(false);
	const [isLoadingTopSenders, setIsLoadingTopSenders] = useState<boolean>(false);
	const [isLoadingRecentLogs, setIsLoadingRecentLogs] = useState<boolean>(false);

	const [dateRange, setDateRange] = useState<any>({});
	const [isResolving, setIsResolving] = useState<boolean>(true);
	const [openDateRangePicker, setOpenDateRangePicker] = useState<boolean>(false);
	const [isFiltering, setIsFiltering] = useState<boolean>(false);
	const [topSenders, setTopSenders] = useState<TopSender[]>([]);
	const [recentLogs, setRecentLogs] = useState<RecentLog[]>([]);
	const [percentageChange, setPercentageChange] = useState({
		total: 0,
		success: 0,
		failed: 0
	});

	useEffect(() => {
		if (isResolving) {
			if (hasConnectionsFinishedResolution) {
				setIsResolving(false);
			}
		}
	}, [hasConnectionsFinishedResolution]);

	const fetchDashboardData = () => {
		setIsLoadingMetrics(true);
		setIsLoadingChart(true);
		setIsLoadingTopSenders(true);
		setIsLoadingRecentLogs(true);

		apiFetch({
			path: `/qsmtp/v1/email-logs/dashboard`,
			method: 'GET',
		})
			.then((res: any) => {
				setLogs(res.chart_data);
				setTopSenders(res.top_senders);
				setRecentLogs(res.recent_logs);

				setPercentageChange({
					total: res.metrics[selectedFilter].percentage_change.total,
					success: res.metrics[selectedFilter].percentage_change.success,
					failed: res.metrics[selectedFilter].percentage_change.failed
				});

				setIsLoadingMetrics(false);
				setIsLoadingChart(false);
				setIsLoadingTopSenders(false);
				setIsLoadingRecentLogs(false);
			})
			.catch((_error) => {
				setIsLoadingMetrics(false);
				setIsLoadingChart(false);
				setIsLoadingTopSenders(false);
				setIsLoadingRecentLogs(false);
			});
	};

	const fetchMetricsData = (newSelectedValue: string) => {
		setIsLoadingMetrics(true);

		apiFetch({
			path: addQueryArgs('/qsmtp/v1/email-logs/metrics', {
				total: newSelectedValue,
				success: newSelectedValue,
				failed: newSelectedValue
			}),
			method: 'GET',
		})
			.then((res: any) => {
				setPercentageChange({
					total: res.metrics[newSelectedValue].percentage_change.total,
					success: res.metrics[newSelectedValue].percentage_change.success,
					failed: res.metrics[newSelectedValue].percentage_change.failed
				});

				setLogs(prevLogs => ({
					...prevLogs,
					total: res.current.total,
					success: res.current.success,
					failed: res.current.failed
				}));

				setIsLoadingMetrics(false);
			})
			.catch((_error) => {
				setIsLoadingMetrics(false);
			});
	};

	const fetchChartData = (startDate: string | null = null, endDate: string | null = null) => {
		setIsLoadingChart(true);
		setIsFiltering(true);

		apiFetch({
			path: addQueryArgs('/qsmtp/v1/email-logs/count', {
				start: startDate,
				end: endDate
			}),
			method: 'GET',
		})
			.then((res: any) => {
				setLogs(prevLogs => ({
					...prevLogs,
					days: res.days
				}));

				setIsLoadingChart(false);
				setIsFiltering(false);
			})
			.catch((_error) => {
				setIsLoadingChart(false);
				setIsFiltering(false);
			});
	};

	useEffect(() => {
		if (!hasConnectionsFinishedResolution) return;

		fetchDashboardData();
	}, [hasConnectionsFinishedResolution]);

	const handleFilterChange = (event: SelectChangeEvent) => {
		const value = event.target.value;

		setSelectedFilter(value);

		fetchMetricsData(value);
	};

	const getComparisonText = (timeframe: string): string => {
		switch (timeframe) {
			case 'today':
				return __('from yesterday', 'quillsmtp');
			case 'yesterday':
				return __('from the day before', 'quillsmtp');
			case 'thisWeek':
				return __('from last week', 'quillsmtp');
			case 'lastMonth':
				return __('from previous month', 'quillsmtp');
			default:
				return __('change', 'quillsmtp');
		}
	};

	const handleDateFilter = () => {
		if (!dateRange.startDate || !dateRange.endDate) return;

		setOpenDateRangePicker(false);

		setTimeout(() => {
			const startDate = dateRange.startDate?.toLocaleDateString();
			const endDate = dateRange.endDate?.toLocaleDateString();
			fetchChartData(startDate, endDate);
		}, 300);
	};

	if (size(connections) == 0 || setUpWizard) {
		return (
			<WelcomePage
				setUpWizard={setUpWizard}
				setSetUpWizard={setSetUpWizard}
			/>
		);
	}

	const chartData = {
		labels: logs.days ? Object.keys(logs.days) : [],
		datasets: [
			{
				label: __('Total Emails', 'quillsmtp'),
				data: logs.days ? Object.values(logs.days) : [],
				borderColor: '#3858E9',
				backgroundColor: 'rgba(56, 88, 233, 0.2)',
				borderWidth: 2,
				fill: true,
			},
			{
				label: __('Sent Emails', 'quillsmtp'),
				data: logs.days && logs.success && Object.keys(logs.days).length > 0
					? Object.keys(logs.days).map(() => {
						const avgSuccess = logs.success! / Object.keys(logs.days!).length;
						return Math.min(avgSuccess, logs.success!);
					})
					: [],
				borderColor: '#03A32C',
				backgroundColor: 'rgba(3, 163, 44, 0.2)',
				borderWidth: 2,
				fill: true,
			},
			{
				label: __('Failed Emails', 'quillsmtp'),
				data: logs.days && logs.failed && Object.keys(logs.days).length > 0
					? Object.keys(logs.days).map(() => {
						const avgFailed = logs.failed! / Object.keys(logs.days!).length;
						return Math.min(avgFailed, logs.failed!);
					})
					: [],
				borderColor: '#F35A5A',
				backgroundColor: 'rgba(243, 90, 90, 0.2)',
				borderWidth: 3,
				fill: false,
				tension: 0.4
			}
		]
	};

	const MetricsOverviewSkeleton = () => (
		<div className="qsmtp-home-page__overview__content">
			<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--total ml-5">
				<div className=''>
					<div className='rounded-full bg-[#e9ecfd] w-fit mb-10'>
						<Skeleton variant="circular" width={64} height={64} animation="wave" />
					</div>
					<h2 className='font-roboto'><Skeleton variant="text" width={100} animation="wave" /></h2>
					<p className='font-roboto'><Skeleton variant="text" width={60} height={32} animation="wave" sx={{ mb: 1 }} /></p>
				</div>
				<div className='filter-selection grid gap-[7.3rem]'>
					<div className='flex justify-end'>
						<Skeleton variant="rectangular" width={100} height={20} animation="wave" sx={{ borderRadius: '4px' }} />
					</div>
					<div className='flex items-center'>
						<Skeleton
							variant="rounded"
							width={150}
							height={28}
							animation="wave"
							sx={{
								borderRadius: '50px',
								bgcolor: 'rgba(56, 88, 233, 0.1)'
							}}
						/>
					</div>
				</div>
			</div>
			<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--succeeded">
				<div>
					<div className='rounded-full bg-[#d6f6df] w-fit mb-10'>
						<Skeleton variant="circular" width={64} height={64} animation="wave" />
					</div>
					<h2 className='font-roboto'><Skeleton variant="text" width={120} animation="wave" /></h2>
					<p className='font-roboto'><Skeleton variant="text" width={60} height={32} animation="wave" sx={{ mb: 1 }} /></p>
				</div>
				<div className='filter-selection grid gap-[7.3rem]'>
					<div className='flex justify-end'>
						<Skeleton variant="rectangular" width={100} height={20} animation="wave" sx={{ borderRadius: '4px' }} />
					</div>
					<div className='flex items-center'>
						<Skeleton
							variant="rounded"
							width={150}
							height={28}
							animation="wave"
							sx={{
								borderRadius: '50px',
								bgcolor: 'rgba(3, 163, 44, 0.1)'
							}}
						/>
					</div>
				</div>
			</div>
			<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--failed">
				<div>
					<div className='rounded-full bg-[#f9d5d5] w-fit mb-10'>
						<Skeleton variant="circular" width={64} height={64} animation="wave" />
					</div>
					<h2 className='font-roboto'><Skeleton variant="text" width={100} animation="wave" /></h2>
					<p className='font-roboto'><Skeleton variant="text" width={60} height={32} animation="wave" sx={{ mb: 1 }} /></p>
				</div>
				<div className='filter-selection grid gap-[7.3rem]'>
					<div className='flex justify-end'>
						<Skeleton variant="rectangular" width={100} height={20} animation="wave" sx={{ borderRadius: '4px' }} />
					</div>
					<div className='flex items-center'>
						<Skeleton
							variant="rounded"
							width={150}
							height={28}
							animation="wave"
							sx={{
								borderRadius: '50px',
								bgcolor: 'rgba(243, 90, 90, 0.1)'
							}}
						/>
					</div>
				</div>
			</div>
		</div>
	);

	const ChartSkeleton = () => (
		<div className="qsmtp-home-page__chart" style={{ padding: '20px', height: '300px' }}>
			<div className="flex flex-col w-full h-full">
				<div className="flex justify-between mb-4">
					<Skeleton variant="text" width={150} height={24} animation="wave" />
					<Skeleton variant="text" width={100} height={24} animation="wave" />
				</div>
				<div className="flex-1">
					<Skeleton variant="rectangular" width="100%" height="100%" animation="wave" sx={{ borderRadius: '8px', bgcolor: 'rgba(0, 0, 0, 0.05)' }} />
				</div>
			</div>
		</div>
	);

	const TopSendersSkeleton = () => (
		<>
			{[...Array(4)].map((_, index) => (
				<div key={index} className='text-[#333333] mx-3 my-6 bg-[#FAFAFA] py-3 px-4 flex justify-between font-roboto'>
					<Skeleton variant="text" width={index % 2 === 0 ? "60%" : "50%"} animation="wave" />
					<Skeleton variant="text" width="15%" animation="wave" />
				</div>
			))}
		</>
	);

	const RecentLogsSkeleton = () => (
		<tbody className='text-[#333333]'>
			{[...Array(4)].map((_, index) => (
				<tr key={index} className="border-l-[0.5px] border-r-[0.5px] border-b-[0.5px] border-[#9E9E9E]">
					<td className="py-5 px-4">
						<Skeleton variant="text" width={index % 2 === 0 ? "90%" : "75%"} animation="wave" />
					</td>
					<td className="text-center">
						<div className="flex justify-center">
							<Skeleton variant="circular" width={24} height={24} animation="wave" sx={{ margin: '0 auto' }} />
						</div>
					</td>
					<td className="text-center">
						<Skeleton
							variant="rounded"
							width="80%"
							height={35}
							animation="wave"
							sx={{
								borderRadius: '50px',
								margin: '0 auto',
								bgcolor: index % 2 === 0 ? 'rgba(3, 163, 44, 0.1)' : 'rgba(243, 90, 90, 0.1)'
							}}
						/>
					</td>
					<td>
						<Skeleton variant="text" width="70%" animation="wave" />
					</td>
				</tr>
			))}
		</tbody>
	);

	return (
		<div className="qsmtp-wrapper">
			<div className="qsmtp-main-container">
				<div className="qsmtp-home-page">
					<div
						className="qsmtp-home-page__overview"
					>
						{isLoadingMetrics ? (
							<MetricsOverviewSkeleton />
						) : (
							<div className="qsmtp-home-page__overview__content">
								<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--total ml-5">
									<div className=''>
										<div className='rounded-full bg-[#e9ecfd] w-fit mb-10'>
											<GoMail className='text-[#3858E9] size-16 p-4' />
										</div>
										<h2 className='font-roboto'>{__('All Emails', 'quillsmtp')}</h2>
										<p className='font-roboto'>{logs.total || 0}</p>
									</div>
									<div className='filter-selection grid gap-[7.3rem]'>
										<div className='flex justify-end'>
											<FormControl variant="standard" sx={{ width: "100px" }}>
												<Select
													labelId="filter-select-total"
													id="filter-select-total"
													value={selectedFilter}
													label={__('Time Period', 'quillsmtp')}
													sx={{
														color: "#A3A3A3", height: "20px", "&:hover": {
															backgroundColor: "white",
															color: "#3858E9",
														},
														"&.Mui-focused": {
															backgroundColor: "white !important",
															color: "#3858E9",
														}, ".MuiSelect-select": {
															backgroundColor: "transparent !important",
															color: "#A3A3A3"
														},
													}}
													onChange={(event) => handleFilterChange(event)}
												>
													<MenuItem value="today">
														{__('Today', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="yesterday">
														{__('Yesterday', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="thisWeek">
														{__('This Week', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="lastMonth">
														{__('Last Month', 'quillsmtp')}
													</MenuItem>
												</Select>
											</FormControl>
										</div>
										<div className='flex items-center gap-1 text-[#03A32C] text-[12px] bg-[#aaf2bc] rounded-full px-[18px] py-[5px]'>
											<IoIosArrowRoundUp className='text-[26px]' />
											<span className='font-roboto'>+{percentageChange.total}% {getComparisonText(selectedFilter)}</span>
										</div>
									</div>
								</div>
								<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--succeeded">
									<div>
										<div className='rounded-full bg-[#d6f6df] w-fit mb-10'>
											<FaCheck className='text-[#03A32C] size-16 p-4' />
										</div>
										<h2 className='font-roboto'>{__('Succeeded Emails', 'quillsmtp')}</h2>
										<p className='font-roboto'>{logs.success || 0}</p>
									</div>
									<div className='filter-selection grid gap-[7.3rem]'>
										<div className='flex justify-end'>
											<FormControl variant="standard" sx={{ width: "100px" }}>
												<Select
													labelId="filter-select-success"
													id="filter-select-success"
													value={selectedFilter}
													label={__('Time Period', 'quillsmtp')}
													sx={{
														color: "#A3A3A3", height: "20px", "&:hover": {
															backgroundColor: "white",
															color: "#3858E9",
														},
														"&.Mui-focused": {
															backgroundColor: "white !important",
															color: "#3858E9",
														}, ".MuiSelect-select": {
															backgroundColor: "transparent !important",
															color: "#A3A3A3"
														},
													}}
													onChange={(event) => handleFilterChange(event)}
												>
													<MenuItem value="today">
														{__('Today', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="yesterday">
														{__('Yesterday', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="thisWeek">
														{__('This Week', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="lastMonth">
														{__('Last Month', 'quillsmtp')}
													</MenuItem>
												</Select>
											</FormControl>
										</div>
										<div className='flex items-center gap-1 text-[#03A32C] text-[12px] bg-[#aaf2bc] rounded-full px-[18px] py-[5px]'>
											<IoIosArrowRoundUp className='text-[26px]' />
											<span className='font-roboto'>+{percentageChange.success}% {getComparisonText(selectedFilter)}</span>
										</div>
									</div>
								</div>
								<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--failed">
									<div>
										<div className='rounded-full bg-[#f9d5d5] w-fit mb-10'>
											<IoClose className='text-[#F35A5A] size-16 p-4' />
										</div>
										<h2 className='font-roboto'>{__('Failed Emails', 'quillsmtp')}</h2>
										<p className='font-roboto'>{logs.failed || 0}</p>
									</div>
									<div className='filter-selection grid gap-[7.3rem]'>
										<div className='flex justify-end'>
											<FormControl variant="standard" sx={{ width: "110px" }}>
												<Select
													labelId="filter-select-failed"
													id="filter-select-failed"
													value={selectedFilter}
													label={__('Time Period', 'quillsmtp')}
													sx={{
														color: "#A3A3A3", height: "20px", "&:hover": {
															backgroundColor: "white",
															color: "#3858E9",
														},
														"&.Mui-focused": {
															backgroundColor: "white !important",
															color: "#3858E9",
														}, ".MuiSelect-select": {
															backgroundColor: "transparent !important",
															color: "#A3A3A3"
														},
													}}
													onChange={(event) => handleFilterChange(event)}
												>
													<MenuItem value="today">
														{__('Today', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="yesterday">
														{__('Yesterday', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="thisWeek">
														{__('This Week', 'quillsmtp')}
													</MenuItem>
													<MenuItem value="lastMonth">
														{__('Last Month', 'quillsmtp')}
													</MenuItem>
												</Select>
											</FormControl>
										</div>
										<div className='flex items-center gap-1 text-[#03A32C] text-[12px] bg-[#aaf2bc] rounded-full px-[18px] py-[5px]'>
											<IoIosArrowRoundUp className='text-[26px]' />
											<span className='font-roboto'>{percentageChange.failed < 0
												? `${Math.abs(percentageChange.failed)}% ${__('less than', 'quillsmtp')} ${getComparisonText(selectedFilter).replace('from ', '')}`
												: `${percentageChange.failed}% ${__('more than', 'quillsmtp')} ${getComparisonText(selectedFilter).replace('from ', '')}`
											}</span>
										</div>
									</div>
								</div>
							</div>
						)}
					</div>
					<div className="qsmtp-home-page__content">
						<div className="qsmtp-home-page__chart-wrap">
							<div className="qsmtp-home-page__chart__header">
								<h2 className='font-roboto font-[700] text-[24px]'>
									{__('Total Emails', 'quillsmtp')}
								</h2>
								<div className="qsmtp-home-page__chart-section">
									<div
										className="qsmtp-home-page__chart-date-range"
										onClick={() =>
											setOpenDateRangePicker(true)
										}
									>
										<div className='flex items-center gap-3 border-r'>
											<span className="qsmtp-home-page__chart-date-range-label font-roboto text-[16px] py-2">
												{dateRange?.startDate?.toLocaleDateString() ||
													__(
														'Start Date',
														'quillsmtp'
													)}
											</span>
											<DateIcon className='text-[#3858E9]' />
										</div>
										<span className='text-[#3858E9] font-roboto text-[20px] px-9'>{__('To', 'quillsmtp')}</span>
										<div className='flex items-center gap-3 border-l'>
											<span className="qsmtp-home-page__chart-date-range-label font-roboto text-[16px] py-2">
												{dateRange?.endDate?.toLocaleDateString() ||
													__(
														'End Date',
														'quillsmtp'
													)}
											</span>
											<DateIcon className='text-[#3858E9]' />
										</div>
									</div>
									<Popover
										open={openDateRangePicker}
										onClose={() => setOpenDateRangePicker(false)}
										anchorReference="anchorPosition"
										anchorPosition={{
											top: window.innerHeight / 2 - 250,
											left: window.innerWidth / 2 - 300,
										}}
									>
										<div className="qsmtp-date-picker-container">
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
														startDate: dateRange?.startDate || new Date(),
														endDate: dateRange?.endDate || new Date(),
														key: 'selection',
													},
												]}
											/>
											<div className="qsmtp-date-filter-actions">
												<LoadingButton
													variant="contained"
													color="primary"
													onClick={handleDateFilter}
													loading={isFiltering}
													disabled={!dateRange.startDate || !dateRange.endDate}
													className="qsmtp-filter-button"
												>
													{__('Apply Filter', 'quillsmtp')}
												</LoadingButton>
											</div>
										</div>
									</Popover>
								</div>
							</div>
							{isLoadingChart ? (
								<ChartSkeleton />
							) : (
								<div
									className="qsmtp-home-page__chart"
									style={{
										padding: '20px',
									}}
								>
									{logs.days && Object.keys(logs.days).length > 0 ? (
										<Line data={chartData} />
									) : (
										<div className="qsmtp-no-data-message">
											<p>{__('No chart data available for the selected period.', 'quillsmtp')}</p>
										</div>
									)}
								</div>
							)}
						</div>
					</div>
					<div className='flex w-100 justify-center gap-8 px-8 pt-8 pb-10'>
						<div className='border-[1px] rounded-lg border-[#E5E5E5] w-1/3 bg-white'>
							<div className='flex items-center justify-between py-3 px-4 border-b border-[#E5E5E5]'>
								<h2 className='text-[20px] font-semibold font-roboto'>{__('Top Sender', 'quillsmtp')}</h2>
								<a href="/wp-admin/admin.php?page=quillsmtp#/logs" className='flex items-center text-[#3858E9] gap-2 text-[15px] font-roboto'>{__('All', 'quillsmtp')} <MdArrowOutward /></a>
							</div>
							{isLoadingTopSenders ? (
								<TopSendersSkeleton />
							) : (
								topSenders.length > 0 ? (
									topSenders.map((sender, index) => (
										<div key={index} className='text-[#333333] mx-3 my-6 bg-[#FAFAFA] py-3 px-4 flex justify-between font-roboto'>
											<span className='text-[16px]'>{sender.from}</span>
											<span className='font-semibold text-[16px]'>{sender.count}</span>
										</div>
									))
								) : (
									<div className='text-[#333333] mx-3 my-6 py-3 px-4 flex justify-center font-roboto'>
										<span className='text-[16px]'>{__('No data available', 'quillsmtp')}</span>
									</div>
								)
							)}
						</div>
						<div className='border-[1px] rounded-lg border-[#E5E5E5] w-2/3 bg-white'>
							<div className='flex items-center justify-between py-3 px-4 bg-white'>
								<h2 className='text-[20px] font-semibold font-roboto'>{__('Recent Logs', 'quillsmtp')}</h2>
								<a href="/wp-admin/admin.php?page=quillsmtp#/logs" className='flex items-center text-[#3858E9] gap-2 text-[15px] font-roboto'>{__('View Logs', 'quillsmtp')} <MdArrowOutward /></a>
							</div>
							<table className='w-full table-fixed text-[16px] font-roboto'>
								<thead>
									<tr className='text-white bg-[#333333] border-l-[0.5px] border-r-[0.5px] border-[#333333]'>
										<th className='font-normal text-start py-5 px-4 w-2/6'>{__('From', 'quillsmtp')}</th>
										<th className='font-normal text-start'>{__('Provider', 'quillsmtp')}</th>
										<th className='font-normal text-start'>{__('Status', 'quillsmtp')}</th>
										<th className='font-normal text-start'>{__('Date', 'quillsmtp')}</th>
									</tr>
								</thead>
								{isLoadingRecentLogs ? (
									<RecentLogsSkeleton />
								) : (
									<tbody className='text-[#333333]'>
										{recentLogs.length > 0 ? (
											recentLogs.map((log, index) => (
												<tr key={log.log_id} className={`border-l-[0.5px] border-r-[0.5px] border-b-[0.5px] border-[#9E9E9E] ${index === recentLogs.length - 1 ? 'rounded-b-lg' : ''}`}>
													<td className={`py-5 px-4 ${index === recentLogs.length - 1 ? 'rounded-bl-lg' : ''}`}>{log.from}</td>
													<td className='text-left'>
														{log.provider && getMailerModules()[log.provider] && getMailerModules()[log.provider].icon && (
															<img
																src={getMailerModules()[log.provider].icon}
																alt={log.provider}
																className='w-[50px] h-[50px]'
															/>
														)}
													</td>
													<td>
														<span className={`font-[400] rounded-full py-[8px] px-[28px] border-[0.5px] ${log.status === 'succeeded' ? 'text-[#03A32C] bg-[#03A32C] bg-opacity-20 border-[#03A32C]' : 'text-[#E93838] bg-[#E93838] bg-opacity-20 border-[#E93838] px-[25px]'}`}>
															{log.status === 'succeeded' ? __('Sent', 'quillsmtp') : __('Failed', 'quillsmtp')}
														</span>
													</td>
													<td className={`${index === recentLogs.length - 1 ? 'rounded-br-lg' : ''}`}>{log.local_datetime}</td>
												</tr>
											))
										) : (
											<tr>
												<td colSpan={4} className="text-center py-5">
													{__('No recent logs available', 'quillsmtp')}
												</td>
											</tr>
										)}
									</tbody>
								)}
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
};

export default Home;
