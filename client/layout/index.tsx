/**
 * WordPress Dependencies
 */
import { SlotFillProvider } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * External dependencies
 */
import { ThreeDots as Loader } from 'react-loader-spinner';
import { css } from '@emotion/css';

/**
 * Internal dependencies
 */
import {
	getAdminPages,
	Router,
	Route,
	Switch,
	getHistory,
} from '../navigation';
import { Controller } from './controller';
import { NavBar } from '../components';
import './style.scss';
import ConfigAPI from '../config';

export const Layout = (props) => {
	// @ts-expect-error
	const { notices } = useSelect((select) => {
		return {
			// @ts-expect-error
			notices: select('core/notices').getNotices(),
		};
	});

	const { removeNotice } = useDispatch('core/notices');

	const [isLoading, setIsLoading] = useState(
		props.page.requiresInitialPayload
	);

	useEffect(() => {
		if (props.page.requiresInitialPayload) {
			apiFetch({
				path: `/qsmtp/v1/settings`,
				method: 'GET',
			}).then((res: any) => {
				console.log(res);

				setTimeout(() => {
					setIsLoading(false);
				}, 100);
				ConfigAPI.setInitialPayload(res);
			});
		}

		// Remove all notices on any page mount
		notices.forEach((notice) => {
			removeNotice(notice.id);
		});
	}, []);

	return (
		<SlotFillProvider>
			<div className="quillsmtp-layout">
				<NavBar />
				<div className="quillsmtp-layout__main">
					{isLoading ? (
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
					) : (
						<Controller {...props} />
					)}
				</div>
			</div>
		</SlotFillProvider>
	);
};

const _PageLayout = () => {
	return (
		<>
			<Router history={getHistory()}>
				<Switch>
					{Object.values(getAdminPages()).map((page) => {
						return (
							<Route
								key={page.path}
								path={page.path}
								exact={page.exact}
								render={(props) => (
									<Layout page={page} {...props} />
								)}
							/>
						);
					})}
				</Switch>
			</Router>
		</>
	);
};

export default _PageLayout;
