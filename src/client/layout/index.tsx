/**
 * QuillSMTP dependencies
 */
import {
	getAdminPages,
	Router,
	Route,
	Switch,
	getHistory,
} from '@quillsmtp/navigation';
import ConfigAPI from '@quillsmtp/config';

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
import { keys, isEmpty } from 'lodash';
import { ThreeDots as Loader } from 'react-loader-spinner';
import { css } from '@emotion/css';

/**
 * Internal dependencies
 */
import { Controller } from './controller';
import { NavBar } from '../components';
import './style.scss';

export const Layout = (props) => {
	const { notices } = useSelect((select) => {
		return {
			notices: select('quillSMTP/core').getNotices(),
		};
	});

	const { deleteNotice } = useDispatch('quillSMTP/core');

	const [isLoading, setIsLoading] = useState(
		props.page.requiresInitialPayload
	);

	useEffect(() => {
		if (props.page.requiresInitialPayload) {
			apiFetch({
				path: `/qsmtp/v1/settings`,
				method: 'GET',
			}).then((res: any) => {
				setTimeout(() => {
					setIsLoading(false);
				}, 100);
				ConfigAPI.setInitialPayload(res);
			});
		}

		// Remove all notices on any page mount
		if (!isEmpty(notices)) {
			keys(notices).forEach((noticeId) => {
				deleteNotice(noticeId);
			});
		}
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
							<Loader color="#cb3b87" height={50} width={50} />
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
