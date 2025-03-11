import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * External Dependencies
 */
import { DateRangePicker } from 'react-date-range';
import Popover from '@mui/material/Popover';
import { LoadingButton } from '@mui/lab';
import DateIcon from '@mui/icons-material/DateRange';
import FilterIcon from '@mui/icons-material/FilterAlt';
import { keys, map, isEmpty, size } from 'lodash';
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
import Logo from '../../assets/logo.svg';
/**
 * Internal Dependencies
 */
import './style.scss';
import WelcomePage from '../welcome-page';
import { GoMail } from "react-icons/go";
import { FaCheck } from "react-icons/fa";
import { IoClose } from "react-icons/io5";
import { MdArrowOutward } from "react-icons/md";
import { SiBrevo } from "react-icons/si";
import { IoIosArrowDown } from "react-icons/io";
import { IoIosArrowRoundUp } from "react-icons/io";
import { FormControl, MenuItem, Select } from '@mui/material';

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
	const [selectedValue, setSelectedValue] = useState<string>("value1")
	const [isLoading, setIsLoading] = useState<boolean>(false);
	const [isFiltering, setIsFiltering] = useState<boolean>(false);
	const [dateRange, setDateRange] = useState<any>({});
	const [isResolving, setIsResolving] = useState<boolean>(true);
	const [openDateRangePicker, setOpenDateRangePicker] =
		useState<boolean>(false);

	useEffect(() => {
		if (isResolving) {
			if (hasConnectionsFinishedResolution) {
				setIsResolving(false);
			}
		}
	}, [hasConnectionsFinishedResolution]);

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

	if (size(connections) == 0 || setUpWizard) {
		return (
			<WelcomePage
				setUpWizard={setUpWizard}
				setSetUpWizard={setSetUpWizard}
			/>
		);
	}

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

	// const data = {
	// 	labels: keys(logs.days),
	// 	datasets: [
	// 		{
	// 			label: logs.total,
	// 			data: map(
	// 				logs.days,
	// 				(count) => count
	// 			),
	// 			borderColor: "blue",
	// 			backgroundColor: "rgba(47, 128, 237, 0.2)",
	// 			tension: 0.4,
	// 		},
	// 		{
	// 			label: logs.success,
	// 			data: map(
	// 				logs.days,
	// 				(count) => count
	// 			),
	// 			borderColor: "green",
	// 			backgroundColor: "rgba(235, 87, 87, 0.2)",
	// 			tension: 0.4,
	// 		},
	// 		{
	// 			label: logs.failed,
	// 			data: map(
	// 				logs.days,
	// 				(count) => count
	// 			),
	// 			borderColor: "red",
	// 			backgroundColor: "rgba(39, 174, 96, 0.2)",
	// 			tension: 0.4,
	// 		},
	// 	],
	// };

	// const options = {
	// 	responsive: true,
	// 	interaction: {
	// 		mode: 'index' as const,
	// 		intersect: false,
	// 	},
	// 	plugins: {
	// 		legend: { position: "top" },
	// 		title: { display: true, text: "Total Emails" },
	// 	},
	// };

	// console.log("logs.days:", logs.days);
	// console.log("logs.success:", logs.success);

	const data = [
		{ date: '2024-03-01', total: 500, sent: 450, failed: 50 },
		{ date: '2024-03-02', total: 520, sent: 470, failed: 50 },
		{ date: '2024-03-03', total: 530, sent: 480, failed: 50 },
		{ date: '2024-03-04', total: 540, sent: 490, failed: 50 },
		{ date: '2024-03-05', total: 550, sent: 500, failed: 50 },
		{ date: '2024-03-06', total: 560, sent: 510, failed: 50 },
		{ date: '2024-03-07', total: 570, sent: 520, failed: 50 },
		{ date: '2024-03-08', total: 580, sent: 530, failed: 50 },
		{ date: '2024-03-09', total: 590, sent: 540, failed: 50 },
		{ date: '2024-03-10', total: 600, sent: 550, failed: 50 },
		{ date: '2024-03-11', total: 610, sent: 560, failed: 50 },
		{ date: '2024-03-12', total: 620, sent: 570, failed: 50 },
		{ date: '2024-03-13', total: 630, sent: 580, failed: 50 },
		{ date: '2024-03-14', total: 640, sent: 590, failed: 50 },
		{ date: '2024-03-15', total: 650, sent: 600, failed: 50 }
	];
	
	const chartData = {
		labels: data.map(d => d.date),
		datasets: [
			{
				label: 'Total Emails',
				data: data.map(d => d.total),
				borderColor: 'blue',
				backgroundColor: 'rgba(0, 0, 255, 0.2)',
				borderWidth: 2,
				fill: true,
			},
			{
				label: 'Sent Emails',
				data: data.map(d => d.sent),
				borderColor: 'green',
				backgroundColor: 'rgba(0, 255, 0, 0.2)',
				borderWidth: 2,
				fill: true,
			},
			{
				label: 'Failed Emails',
				data: data.map(d => d.failed),
				borderColor: 'red',
				backgroundColor: 'rgba(255, 0, 0, 0.2)',
				borderWidth: 3,
				fill: false,
				tension: 0.4 // Makes the line smoother for better visibility
			}
		]
	};

	return (
		<div className="qsmtp-wrapper">
			{/* <div className="qsmtp-left-side">
                <svg viewBox="0 0 512 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg" className="qsmtp-active">
                    <path d="M197.3 170.7h-160A37.4 37.4 0 010 133.3v-96A37.4 37.4 0 0137.3 0h160a37.4 37.4 0 0137.4 37.3v96a37.4 37.4 0 01-37.4 37.4zM37.3 32c-3 0-5.3 2.4-5.3 5.3v96c0 3 2.4 5.4 5.3 5.4h160c3 0 5.4-2.4 5.4-5.4v-96c0-3-2.4-5.3-5.4-5.3zm0 0M197.3 512h-160A37.4 37.4 0 010 474.7v-224a37.4 37.4 0 0137.3-37.4h160a37.4 37.4 0 0137.4 37.4v224a37.4 37.4 0 01-37.4 37.3zm-160-266.7c-3 0-5.3 2.4-5.3 5.4v224c0 3 2.4 5.3 5.3 5.3h160c3 0 5.4-2.4 5.4-5.3v-224c0-3-2.4-5.4-5.4-5.4zm0 0M474.7 512h-160a37.4 37.4 0 01-37.4-37.3v-96a37.4 37.4 0 0137.4-37.4h160a37.4 37.4 0 0137.3 37.4v96a37.4 37.4 0 01-37.3 37.3zm-160-138.7c-3 0-5.4 2.4-5.4 5.4v96c0 3 2.4 5.3 5.4 5.3h160c3 0 5.3-2.4 5.3-5.3v-96c0-3-2.4-5.4-5.3-5.4zm0 0M474.7 298.7h-160a37.4 37.4 0 01-37.4-37.4v-224A37.4 37.4 0 01314.7 0h160A37.4 37.4 0 01512 37.3v224a37.4 37.4 0 01-37.3 37.4zM314.7 32c-3 0-5.4 2.4-5.4 5.3v224c0 3 2.4 5.4 5.4 5.4h160c3 0 5.3-2.4 5.3-5.4v-224c0-3-2.4-5.3-5.3-5.3zm0 0" /></svg>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
                <svg viewBox="0 1 511 512" fill="currentColor">
                    <path d="M498.7 222.7L289.8 13.8a46.8 46.8 0 00-66.7 0L14.4 222.6l-.2.2A47.2 47.2 0 0047 303h8.3v153.7a55.2 55.2 0 0055.2 55.2h81.7a15 15 0 0015-15V376.5a25.2 25.2 0 0125.2-25.2h48.2a25.2 25.2 0 0125.1 25.2V497a15 15 0 0015 15h81.8a55.2 55.2 0 0055.1-55.2V303.1h7.7a47.2 47.2 0 0033.4-80.4zm-21.2 45.4a17 17 0 01-12.2 5h-22.7a15 15 0 00-15 15v168.7a25.2 25.2 0 01-25.1 25.2h-66.8V376.5a55.2 55.2 0 00-55.1-55.2h-48.2a55.2 55.2 0 00-55.2 55.2V482h-66.7a25.2 25.2 0 01-25.2-25.2V288.1a15 15 0 00-15-15h-23A17.2 17.2 0 0135.5 244L244.4 35a17 17 0 0124.2 0l208.8 208.8v.1a17.2 17.2 0 010 24.2zm0 0" /></svg>
                <svg viewBox="0 0 512 512" fill="currentColor">
                    <path d="M467 76H45a45 45 0 00-45 45v270a45 45 0 0045 45h422a45 45 0 0045-45V121a45 45 0 00-45-45zm-6.3 30L287.8 278a44.7 44.7 0 01-63.6 0L51.3 106h409.4zM30 384.9V127l129.6 129L30 384.9zM51.3 406L181 277.2l22 22c14.2 14.1 33 22 53.1 22 20 0 38.9-7.9 53-22l22-22L460.8 406H51.3zM482 384.9L352.4 256 482 127V385z" /></svg>
                <svg viewBox="0 0 512 512" fill="currentColor">
                    <path d="M272 512h-32c-26 0-47.2-21.1-47.2-47.1V454c-11-3.5-21.8-8-32.1-13.3l-7.7 7.7a47.1 47.1 0 01-66.7 0l-22.7-22.7a47.1 47.1 0 010-66.7l7.7-7.7c-5.3-10.3-9.8-21-13.3-32.1H47.1c-26 0-47.1-21.1-47.1-47.1v-32.2c0-26 21.1-47.1 47.1-47.1H58c3.5-11 8-21.8 13.3-32.1l-7.7-7.7a47.1 47.1 0 010-66.7l22.7-22.7a47.1 47.1 0 0166.7 0l7.7 7.7c10.3-5.3 21-9.8 32.1-13.3V47.1c0-26 21.1-47.1 47.1-47.1h32.2c26 0 47.1 21.1 47.1 47.1V58c11 3.5 21.8 8 32.1 13.3l7.7-7.7a47.1 47.1 0 0166.7 0l22.7 22.7a47.1 47.1 0 010 66.7l-7.7 7.7c5.3 10.3 9.8 21 13.3 32.1h10.9c26 0 47.1 21.1 47.1 47.1v32.2c0 26-21.1 47.1-47.1 47.1H454c-3.5 11-8 21.8-13.3 32.1l7.7 7.7a47.1 47.1 0 010 66.7l-22.7 22.7a47.1 47.1 0 01-66.7 0l-7.7-7.7c-10.3 5.3-21 9.8-32.1 13.3v10.9c0 26-21.1 47.1-47.1 47.1zM165.8 409.2a176.8 176.8 0 0045.8 19 15 15 0 0111.3 14.5V465c0 9.4 7.7 17.1 17.1 17.1h32.2c9.4 0 17.1-7.7 17.1-17.1v-22.2a15 15 0 0111.3-14.5c16-4.2 31.5-10.6 45.8-19a15 15 0 0118.2 2.3l15.7 15.7a17.1 17.1 0 0024.2 0l22.8-22.8a17.1 17.1 0 000-24.2l-15.7-15.7a15 15 0 01-2.3-18.2 176.8 176.8 0 0019-45.8 15 15 0 0114.5-11.3H465c9.4 0 17.1-7.7 17.1-17.1v-32.2c0-9.4-7.7-17.1-17.1-17.1h-22.2a15 15 0 01-14.5-11.2c-4.2-16.1-10.6-31.6-19-45.9a15 15 0 012.3-18.2l15.7-15.7a17.1 17.1 0 000-24.2l-22.8-22.8a17.1 17.1 0 00-24.2 0l-15.7 15.7a15 15 0 01-18.2 2.3 176.8 176.8 0 00-45.8-19 15 15 0 01-11.3-14.5V47c0-9.4-7.7-17.1-17.1-17.1h-32.2c-9.4 0-17.1 7.7-17.1 17.1v22.2a15 15 0 01-11.3 14.5c-16 4.2-31.5 10.6-45.8 19a15 15 0 01-18.2-2.3l-15.7-15.7a17.1 17.1 0 00-24.2 0l-22.8 22.8a17.1 17.1 0 000 24.2l15.7 15.7a15 15 0 012.3 18.2 176.8 176.8 0 00-19 45.8 15 15 0 01-14.5 11.3H47c-9.4 0-17.1 7.7-17.1 17.1v32.2c0 9.4 7.7 17.1 17.1 17.1h22.2a15 15 0 0114.5 11.3c4.2 16 10.6 31.5 19 45.8a15 15 0 01-2.3 18.2l-15.7 15.7a17.1 17.1 0 000 24.2l22.8 22.8a17.1 17.1 0 0024.2 0l15.7-15.7a15 15 0 0118.2-2.3z" />
                    <path d="M256 367.4c-61.4 0-111.4-50-111.4-111.4s50-111.4 111.4-111.4 111.4 50 111.4 111.4-50 111.4-111.4 111.4zm0-192.8a81.5 81.5 0 000 162.8 81.5 81.5 0 000-162.8z" /></svg>
                <svg viewBox="0 0 512 512" fill="currentColor">
                    <path d="M255.2 468.6H63.8a21.3 21.3 0 01-21.3-21.2V64.6c0-11.7 9.6-21.2 21.3-21.2h191.4a21.2 21.2 0 100-42.5H63.8A63.9 63.9 0 000 64.6v382.8A63.9 63.9 0 0063.8 511H255a21.2 21.2 0 100-42.5z" />
                    <path d="M505.7 240.9L376.4 113.3a21.3 21.3 0 10-29.9 30.3l92.4 91.1H191.4a21.2 21.2 0 100 42.6h247.5l-92.4 91.1a21.3 21.3 0 1029.9 30.3l129.3-127.6a21.3 21.3 0 000-30.2z" /></svg>
            </div> */}
			<div className="qsmtp-main-container">
				<div className="qsmtp-home-page">
					<div
						className="qsmtp-home-page__overview"
					>
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
												labelId="filter-select"
												id="filter-select"
												value={selectedValue}
												label="By Email Address"
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
												onChange={(event) => setSelectedValue(event.target.value)}
											>
												<MenuItem value="value1">Today</MenuItem>
												<MenuItem value="value2">Yesterday</MenuItem>
												<MenuItem value="value3">Last Week</MenuItem>
												<MenuItem value="value4">Last Month</MenuItem>
											</Select>
										</FormControl>
									</div>
									<div className='flex items-center gap-1 text-[#03A32C] text-[12px] bg-[#aaf2bc] rounded-full px-[18px] py-[5px]'>
										<IoIosArrowRoundUp className='text-[26px]' />
										<span className='font-roboto'>+20% from yesterday</span>
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
												labelId="filter-select"
												id="filter-select"
												value={selectedValue}
												label="By Email Address"
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
												onChange={(event) => setSelectedValue(event.target.value)}
											>
												<MenuItem value="value1">This Week</MenuItem>
												<MenuItem value="value2">Yesterday</MenuItem>
												<MenuItem value="value3">Last Week</MenuItem>
												<MenuItem value="value4">Last Month</MenuItem>
											</Select>
										</FormControl>
									</div>
									<div className='flex items-center gap-1 text-[#03A32C] text-[12px] bg-[#aaf2bc] rounded-full px-[18px] py-[5px]'>
										<IoIosArrowRoundUp className='text-[26px]' />
										<span className='font-roboto'>+20% from last week</span>
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
												labelId="filter-select"
												id="filter-select"
												value={selectedValue}
												label="By Email Address"
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
												onChange={(event) => setSelectedValue(event.target.value)}
											>
												<MenuItem value="value1">This Month</MenuItem>
												<MenuItem value="value2">Yesterday</MenuItem>
												<MenuItem value="value3">Last Week</MenuItem>
												<MenuItem value="value4">Last Month</MenuItem>
											</Select>
										</FormControl>
									</div>
									<div className='flex items-center gap-1 text-[#03A32C] text-[12px] bg-[#aaf2bc] rounded-full px-[18px] py-[5px]'>
										<IoIosArrowRoundUp className='text-[26px]' />
										<span className='font-roboto'>+20% less than last month</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					{!isLoading && (
						<div className="qsmtp-home-page__content">
							<div className="qsmtp-home-page__chart-wrap">
								<div className="qsmtp-home-page__chart__header">
									<h2 className='font-roboto font-[700] text-[24px]'>
										{__('Total Emails', 'quill-smtp')}
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
										{/* <LoadingButton
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
										</LoadingButton> */}
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
															item.selection
																.startDate,
														endDate:
															item.selection
																.endDate,
													});
												}}
												showSelectionPreview={true}
												moveRangeOnFirstSelection={
													false
												}
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
										<Line data={chartData} />
										{/* <Chart
											type="line"
											data={{
												labels: keys(logs.days),
												datasets: [
													{
														type: 'line',
														label: logs.total,
														data: map(
															logs.days,
															(count) => count
														),
														backgroundColor:
															'blue',
														tension: 0.4,
													},
													{
														type: 'line',
														label: logs.success,
														data: map(
															logs.days,
															(count) => count
														),
														backgroundColor:
															'green',
														tension: 0.4,
													},
													{
														type: 'line',
														label: logs.failed,
														data: map(
															logs.days,
															(count) => count
														),
														backgroundColor:
															'red',
														tension: 0.4,
													},
												],
											}}
											options={{
												plugins: {
													title: {
														display: true,
														text: 'Total Emails',
													},
												},
												responsive: true,
												interaction: {
													mode: 'index' as const,
													intersect: false,
												},
											}}
										/> */}
									</div>
								)}
							</div>
						</div>
					)}
					<div className='flex w-100 justify-center gap-8 px-8 pt-8 pb-10'>
						<div className='border-[1px] rounded-lg border-[#E5E5E5] w-1/3 bg-white'>
							<div className='flex items-center justify-between py-3 px-4 border-b border-[#E5E5E5]'>
								<h2 className='text-[20px] font-semibold font-roboto'>Top Sender</h2>
								<a href="/" className='flex items-center text-[#3858E9] gap-2 text-[15px] font-roboto'>All <MdArrowOutward /></a>
							</div>
							<div className=' text-[#333333] mx-3 my-6 bg-[#FAFAFA] py-3 px-4 flex justify-between font-roboto'>
								<span className='text-[16px]'>Md.Magdy.Sa@Gmail.Com</span>
								<span className='font-semibold text-[16px]'>490</span>

							</div>
							<div className=' text-[#333333] mx-3 my-6 bg-[#FAFAFA] py-3 px-4 flex justify-between font-roboto'>
								<span className='text-[16px]'>Md.Magdy.Sa@Gmail.Com</span>
								<span className='font-semibold text-[16px]'>490</span>

							</div>
							<div className=' text-[#333333] mx-3 my-6 bg-[#FAFAFA] py-3 px-4 flex justify-between font-roboto'>
								<span className='text-[16px]'>Md.Magdy.Sa@Gmail.Com</span>
								<span className='font-semibold text-[16px]'>490</span>

							</div>
							<div className=' text-[#333333] mx-3 bg-[#FAFAFA] py-3 px-4 flex justify-between font-roboto'>
								<span className='text-[16px]'>Md.Magdy.Sa@Gmail.Com</span>
								<span className='font-semibold text-[16px]'>490</span>

							</div>
						</div>
						<div className='border-[1px] rounded-lg border-[#E5E5E5] w-2/3 bg-white'>
							<div className='flex items-center justify-between py-3 px-4 bg-white'>
								<h2 className='text-[20px] font-semibold font-roboto'>Recent Logs</h2>
								<a href="/" className='flex items-center text-[#3858E9] gap-2 text-[15px] font-roboto'>View Logs <MdArrowOutward /></a>
							</div>
							<table className='w-full table-fixed text-[16px] font-roboto'>
								<thead>
									<tr className='text-white bg-[#333333] border-l-[0.5px] border-r-[0.5px] border-[#333333]'>
										<th className='font-normal text-start py-5 px-4 w-2/6'>From</th>
										<th className='font-normal text-start'>Provider</th>
										<th className='font-normal text-start'>Status</th>
										<th className='font-normal text-start'>Date</th>
									</tr>
								</thead>
								<tbody className='text-[#333333]'>
									<tr className='border-l-[0.5px] border-r-[0.5px] border-b-[0.5px] border-[#9E9E9E]'>
										<td className='py-5 px-4'>Md.Magdy.Sa@Gmail.Com</td>
										<td className='text-[#25a445] text-[26px]'><SiBrevo /></td>
										<td><span className='font-[400] text-[#03A32C] rounded-full bg-[#03A32C] bg-opacity-20 py-[8px] px-[28px] border-[0.5px] border-[#03A32C]'>Sent</span></td>
										<td>2025-02-03 05:07:18</td>
									</tr>
									<tr className='border-l-[0.5px] border-r-[0.5px] border-b-[0.5px] border-[#9E9E9E]'>
										<td className='py-5 px-4'>Md.Magdy.Sa@Gmail.Com</td>
										<td className='text-[#25a445] text-[26px]'><SiBrevo /></td>
										<td><span className='font-[400] text-[#03A32C] rounded-full bg-[#03A32C] bg-opacity-20 py-[8px] px-[28px] border-[0.5px] border-[#03A32C]'>Sent</span></td>
										<td>2025-02-03 05:07:18</td>
									</tr>
									<tr className='border-l-[0.5px] border-r-[0.5px] border-b-[0.5px] border-[#9E9E9E]'>
										<td className='py-5 px-4'>Md.Magdy.Sa@Gmail.Com</td>
										<td className='text-[#25a445] text-[26px]'><SiBrevo /></td>
										<td><span className='font-[400] text-[#E93838] rounded-full bg-[#E93838] bg-opacity-20 py-[8px] px-[25px] border-[0.5px] border-[#E93838]'>Failed</span></td>
										<td>2025-02-03 05:07:18</td>
									</tr>
									<tr className='border-l-[0.5px] border-r-[0.5px] border-b-[0.5px] border-[#9E9E9E] rounded-b-lg'>
										<td className='py-5 px-4 rounded-br-lg'>Md.Magdy.Sa@Gmail.Com</td>
										<td className='text-[#25a445] text-[26px]'><SiBrevo /></td>
										<td><span className='font-[400] text-[#03A32C] rounded-full bg-[#03A32C] bg-opacity-20 py-[8px] px-[28px] border-[0.5px] border-[#03A32C]'>Sent</span></td>
										<td className='rounded-br-lg'>2025-02-03 05:07:18</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
};

export default Home;
