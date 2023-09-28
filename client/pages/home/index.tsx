/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * External Dependencies
 */
import { Tabs, Tab } from '@mui/material';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';
import General from './general';
import Mailer from './mailer';

const Home = () => {
	const [value, setValue] = useState(0);
	const handleChange = (event: React.SyntheticEvent, newValue: number) => {
		setValue(newValue);
	};

	const TabsObj = {
		mailer: {
			title: __('Mailer', 'quillsmtp'),
			render: <Mailer />,
		},
		general: {
			title: __('General', 'quillsmtp'),
			render: <General />,
		},
	};

	return (
		<div className="qsmtp-settings-page">
			<div className="qsmtp-settings-page__body">
				<Tabs value={value} onChange={handleChange}>
					{Object.keys(TabsObj).map((key, index) => {
						return (
							<Tab
								key={key}
								label={TabsObj[key].title}
								className={classnames(
									'qsmtp-settings-page__tab',
									{
										'qsmtp-settings-page__tab--active':
											value === index,
									}
								)}
							/>
						);
					})}
				</Tabs>
				{Object.keys(TabsObj).map((key, index) => {
					return (
						<div
							key={key}
							className={classnames(
								'qsmtp-settings-page__tab-content',
								{
									'qsmtp-settings-page__tab-content--active':
										value === index,
								}
							)}
							hidden={value !== index}
						>
							{TabsObj[key].render}
						</div>
					);
				})}
			</div>
		</div>
	);
};

export default Home;
