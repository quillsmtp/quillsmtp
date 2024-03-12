/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';

/**
 * External Dependencies
 */
import { DateRangePicker } from 'react-date-range';
import Popover from '@mui/material/Popover';
import { LoadingButton } from '@mui/lab';
import DateIcon from '@mui/icons-material/DateRange';
import FilterIcon from '@mui/icons-material/FilterAlt';
import { keys, map, isEmpty } from 'lodash';
import {
	Chart as ChartJS,
	LinearScale,
	CategoryScale,
	BarElement,
	PointElement,
	LineElement,
	Legend,
	Tooltip,
	LineController,
	BarController,
} from 'chart.js';
import { Chart } from 'react-chartjs-2';
import { ThreeDots as Loader } from 'react-loader-spinner';
import { css } from '@emotion/css';
import classnames from 'classnames';

/**
 * Internal Dependencies
 */
import './style.scss';

ChartJS.register(
	LinearScale,
	CategoryScale,
	BarElement,
	PointElement,
	LineElement,
	Legend,
	Tooltip,
	LineController,
	BarController
);

interface Logs {
	[date: string]: number;
}

const Home: React.FC = () => {
	const [logs, setLogs] = useState<Logs>({});
	const [isLoading, setIsLoading] = useState<boolean>(false);
	const [isFiltering, setIsFiltering] = useState<boolean>(false);
	const [dateRange, setDateRange] = useState<any>({});
	const [openDateRangePicker, setOpenDateRangePicker] =
		useState<boolean>(false);

	useEffect(() => {
		if (isLoading) return;
		setIsLoading(true);
		const startDate = new Date();
		startDate.setDate(startDate.getDate() - 7);
		const endDate = new Date();
		endDate.setDate(endDate.getDate());
		const start = startDate.toLocaleDateString();
		const end = endDate.toLocaleDateString();
		let path = `/qsmtp/v1/email-logs/count?start=${start}&end=${end}`;
		apiFetch({
			path: path,
			method: 'GET',
		})
			.then((res: any) => {
				setLogs(res);
				setIsLoading(false);
			})
			.catch(() => {
				setIsLoading(false);
			});
	}, []);

	const filterLogsByDate = () => {
		if (isFiltering || !dateRange.startDate || !dateRange.endDate) return;
		setIsFiltering(true);
		const startDate = dateRange.startDate?.toLocaleDateString();
		const endDate = dateRange.endDate?.toLocaleDateString();
		let path = `/qsmtp/v1/email-logs/count?start=${startDate}&end=${endDate}`;
		apiFetch({
			path: path,
			method: 'GET',
		})
			.then((res: any) => {
				setLogs(res);
				setIsFiltering(false);
			})
			.catch(() => {
				setIsFiltering(false);
			});
	};

	return (
		<div className="qsmtp-home-page">
			{isLoading && (
				<div
					className={css`
						display: flex;
						flex-wrap: wrap;
						width: 100%;
						min-height: 100vh;
						justify-content: center;
						align-items: center;
					`}
				>
					<Loader color="#8640e3" height={50} width={50} />
				</div>
			)}
			{!isLoading && (
				<div className="qsmtp-home-page__content">
					<div className="qsmtp-home-page__chart-wrap">
						<div className="qsmtp-home-page__chart__header">
							<h2>{__('Sending Stats', 'quill-smtp')}</h2>
							<div className="qsmtp-home-page__chart-section">
								<div
									className="qsmtp-home-page__chart-date-range"
									onClick={() => setOpenDateRangePicker(true)}
								>
									<div>
										<DateIcon />
										<span className="qsmtp-home-page__chart-date-range-label">
											{dateRange?.startDate?.toLocaleDateString() ||
												__('Start Date', 'quillsmtp')}
										</span>
									</div>
									<span>{__('To', 'quillsmtp')}</span>
									<div>
										<DateIcon />
										<span className="qsmtp-home-page__chart-date-range-label">
											{dateRange?.endDate?.toLocaleDateString() ||
												__('End Date', 'quillsmtp')}
										</span>
									</div>
								</div>
								<LoadingButton
									variant="outlined"
									onClick={filterLogsByDate}
									loading={isFiltering}
									loadingPosition="start"
									startIcon={<FilterIcon />}
									sx={{
										marginLeft: '10px',
									}}
								>
									{__('Filter', 'quillsmtp')}
								</LoadingButton>
								<Popover
									open={openDateRangePicker}
									onClose={() =>
										setOpenDateRangePicker(false)
									}
									anchorReference="anchorPosition"
									anchorPosition={{
										top: 200,
										left: 400,
									}}
								>
									<DateRangePicker
										onChange={(item) => {
											setDateRange({
												startDate:
													item.selection.startDate,
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
						</div>
						{!isEmpty(logs) && (
							<div
								className="qsmtp-home-page__chart"
								style={{
									padding: '20px',
								}}
							>
								<Chart
									type="bar"
									data={{
										labels: keys(logs.days),
										datasets: [
											{
												type: 'line',
												label: 'byDate',
												data: map(
													logs.days,
													(count) => count
												),
												backgroundColor: '#f44336',
												borderColor: '#f44336',
												borderWidth: 1,
											},
											{
												type: 'bar',
												label: 'Cumulative',
												data: map(
													logs.days,
													(count) => count
												),
												backgroundColor: '#2196f3',
												borderColor: '#2196f3',
												borderWidth: 1,
											},
										],
									}}
									options={{
										plugins: {
											title: {
												display: true,
												text: 'Chart.js Bar Chart - Stacked',
											},
										},
										responsive: true,
										interaction: {
											mode: 'index' as const,
											intersect: false,
										},
										scales: {
											x: {
												stacked: true,
											},
											y: {
												stacked: true,
											},
										},
									}}
								/>
							</div>
						)}
					</div>
					<div
						className={classnames(
							'qsmtp-home-page__overview',
							css`
								background-color: #fff;
								.qsmtp-home-page__overview__header {
									margin-bottom: 20px;
									background-color: #f7fafc;
									color: #697386;
									padding: 10px 20px;
									display: flex;
									align-items: center;
									justify-content: space-between;
									h2 {
										color: #697386;
										margin-right: 20px;
									}
								}
								.qsmtp-home-page__overview__content {
									display: grid;
									grid-gap: 20px;
								}

								.qsmtp-home-page__overview__content__item {
									background-color: #f7fafc;
									padding: 20px;
									display: flex;
									flex-direction: column;
									justify-content: center;
									align-items: center;
									h2 {
										color: #697386;
										font-size: 18px;
										font-weight: 500;
										margin-bottom: 10px;
										display: flex;
										align-items: center;
										.icon {
											margin-right: 10px;
											font-size: 20px;
										}
									}
									p {
										font-size: 30px;
										font-weight: 600;
										color: #697386;
									}

									&--total {
										background-color: #e6f6ff;
									}

									&--succeeded {
										background-color: #f0fff4;
									}

									&--failed {
										background-color: #fff5f5;
									}
								}
							`
						)}
					>
						<div className="qsmtp-home-page__overview__header">
							<h2>{__('Overview', 'quillsmtp')}</h2>
						</div>
						<div className="qsmtp-home-page__overview__content">
							<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--total">
								<h2>
									<span className="icon">✉️</span>
									{__('Total Emails', 'quillsmtp')}
								</h2>
								<p>{logs.total || 0}</p>
							</div>
							<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--succeeded">
								<h2>
									<span className="icon">✅</span>
									{__('Succeeded', 'quillsmtp')}
								</h2>
								<p>{logs.success || 0}</p>
							</div>
							<div className="qsmtp-home-page__overview__content__item qsmtp-home-page__overview__content__item--failed">
								<h2>
									<span className="icon">❌</span>
									{__('Failed', 'quillsmtp')}
								</h2>
								<p>{logs.failed || 0}</p>
							</div>
						</div>
					</div>
				</div>
			)}
		</div>
	);
};

export default Home;
