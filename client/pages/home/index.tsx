/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { TabPanel } from '@wordpress/components';

/**
 * External Dependencies
 */
import { css } from '@emotion/css';

/**
 * Internal dependencies
 */
import './style.scss';

const Home = () => {
	const Tabs = {
		general: {
			title: __('General', 'quillsmtp'),
			render: <div>General</div>,
		},
	};

	return (
		<div className="qsmtp-settings-page">
			<div className="qsmtp-settings-page__body">
				<TabPanel
					className={css`
						.components-tab-panel__tabs-item {
							font-weight: normal;
						}
						.active-tab {
							font-weight: bold;
						}
					`}
					activeClass="active-tab"
					tabs={Object.entries(Tabs).map(([name, tab]) => {
						return {
							name,
							title: tab.title,
							className: 'tab-' + name,
						};
					})}
					initialTabName={Object.keys(Tabs)[0]}
				>
					{(tab) => (
						<div>
							{Tabs[tab.name]?.render ?? (
								<div>{__('Not Found', 'quillsmtp')}</div>
							)}
						</div>
					)}
				</TabPanel>
			</div>
		</div>
	);
};

export default Home;
