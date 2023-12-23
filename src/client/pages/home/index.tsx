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
import Button from '@mui/material/Button';
import DateIcon from '@mui/icons-material/DateRange';
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
	const [dateRange, setDateRange] = useState<any>({});
	const [openDateRangePicker, setOpenDateRangePicker] =
		useState<boolean>(false);

	useEffect(() => {
		if (isLoading) return;
		setIsLoading(true);
		// Start date: past 7 days
		const startDate = new Date();
		startDate.setDate(startDate.getDate() - 7);
		// End date: today
		const endDate = new Date();
		endDate.setDate(endDate.getDate() - 1);
		const defaultStart = startDate.toLocaleDateString();
		const defaultEnd = endDate.toLocaleDateString();
		const start = dateRange.start || defaultStart;
		const end = dateRange.end || defaultEnd;
		let path = `/qsmtp/v1/logs/count?start=${start}&end=${end}`;
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
		if (isLoading) return;
		setIsLoading(true);
		const startDate = dateRange.startDate?.toLocaleDateString();
		const endDate = dateRange.endDate?.toLocaleDateString();
		let path = `/qsmtp/v1/logs/count?start=${startDate}&end=${endDate}`;
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
	};

	return (
		<div className="qsmtp-home-page">
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
										dateRange?.startDate || new Date(),
									endDate: dateRange?.endDate || new Date(),
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
							labels: keys(logs),
							datasets: [
								{
									type: 'line',
									label: 'byDate',
									data: map(logs, (count) => count),
									backgroundColor: '#f44336',
									borderColor: '#f44336',
									borderWidth: 1,
								},
								{
									type: 'bar',
									label: 'Cumulative',
									data: map(logs, (count) => count),
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
	);
};

export default Home;
