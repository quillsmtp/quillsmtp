import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { useSelect } from "@wordpress/data";

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
    Legend,
    Tooltip,
    LineController,
    BarController,
} from 'chart.js';
import { Chart } from 'react-chartjs-2';
import { ThreeDots as Loader } from 'react-loader-spinner';
import { css } from '@emotion/css';
import classnames from 'classnames';
import Logo from "../../assets/logo.svg";
/**
 * Internal Dependencies
 */
import './style.scss';
import WelcomePage from '../welcome-page';

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
const Home = () => {

    const { connections } = useSelect((select) => ({
        connections: select('quillSMTP/core').getConnections(),
    }));

    const { hasConnectionsFinishedResolution } = useSelect((select) => {
        const { hasFinishedResolution } = select('quillSMTP/core');

        return {
            hasConnectionsFinishedResolution:
                hasFinishedResolution('getConnections'),
        };
    });


    const [logs, setLogs] = useState<Logs>({});
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

    if (size(connections) == 0) {
        return (
            <WelcomePage />
        )
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

                <div className='qsmtp-home-page'>
                    <div
                        className={classnames(
                            'qsmtp-home-page__overview',
                            css`
								
								.qsmtp-home-page__overview__content {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    margin-top: 50px;
								}

								.qsmtp-home-page__overview__content__item {
									background: #536ad9;
									padding: 20px;
									display: flex;
									flex-direction: column;
									justify-content: center;
									align-items: center;
									h2 {
										color: #fff;
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
										color: #fff;
									}

									&--total {
                                        color: #fff;
									}

									&--succeeded {
                                        color: #fff;
										background: radial-gradient(circle, #28a344 0%, #54b250 100%);
									}

									&--failed {
                                        color: #fff;
										background: radial-gradient(circle, #ff3c44 0%, #ff2828 100%)
									}
								}
							`
                        )}
                    >

                        <div className="qsmtp-home-page__overview__content">
                            <div className="qsmtp-home-page__overview__content__item qsmtp-card qsmtp-home-page__overview__content__item--total">
                                <h2>
                                    {__('Total Emails', 'quillsmtp')}
                                </h2>
                                <p>{logs.total || 0}</p>
                            </div>
                            <div className="qsmtp-home-page__overview__content__item qsmtp-card qsmtp-home-page__overview__content__item--succeeded">
                                <h2>
                                    {__('Succeeded Emails', 'quillsmtp')}
                                </h2>
                                <p>{logs.success || 0}</p>
                            </div>
                            <div className="qsmtp-home-page__overview__content__item qsmtp-card qsmtp-home-page__overview__content__item--failed">
                                <h2>
                                    {__('Failed Emails', 'quillsmtp')}
                                </h2>
                                <p>{logs.failed || 0}</p>
                            </div>
                        </div>
                    </div>
                    {!isLoading && (
                        <div className="qsmtp-home-page__content">
                            <div className="qsmtp-home-page__chart-wrap">
                                <div className="qsmtp-home-page__chart__header">
                                    <h2 style={{ color: "#fff" }}>{__('Sending Stats', 'quill-smtp')}</h2>
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
                        </div>
                    )}
                </div>
            </div>
        </div>
    )
}

export default Home;