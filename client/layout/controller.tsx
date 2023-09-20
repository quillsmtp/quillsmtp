/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';

/**
 * External Dependencies
 */
import { parse } from 'qs';
import { motion } from 'framer-motion';

/**
 * Internal dependencies
 */
// import AdminNotices from '../admin-notices';
import { registerAdminPage } from '../navigation';
import Home from '../pages/home';
import EmailTest from '../pages/email-test';
import Logs from '../pages/logs';
import AdminNotices from '../admin-notices';

export const Controller = ({ page, match, location }) => {
	useEffect(() => {
		window.document.documentElement.scrollTop = 0;
	}, []);

	const getQuery = (searchString) => {
		if (!searchString) {
			return {};
		}

		const search = searchString.substring(1);
		return parse(search);
	};

	const { url, params } = match;
	const query = getQuery(location.search);

	return (
		// Using motion div with layoutScroll to reevaluate positions when the user scrolls.
		<motion.div layoutScroll className="qsmtp-page-component-wrapper">
			<page.component
				params={params}
				path={url}
				pathMatch={page.path}
				query={query}
			/>
			<AdminNotices />
		</motion.div>
	);
};

registerAdminPage('home', {
	component: Home,
	path: '/',
});

registerAdminPage('email-test', {
	component: EmailTest,
	path: 'email-test',
});

registerAdminPage('logs', {
	component: Logs,
	path: 'logs',
});
