/**
 * QuillSMTP dependencies
 */
import { registerAdminPage } from '@quillsmtp/navigation';

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
import Home from '../pages/home';
import EmailTest from '../pages/email-test';
import Logs from '../pages/logs';
import { Notices } from '../components';

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
			<Notices />
		</motion.div>
	);
};

registerAdminPage('home', {
	component: Home,
	path: '/',
	requiresInitialPayload: true,
});

registerAdminPage('email-test', {
	component: EmailTest,
	path: 'email-test',
});

registerAdminPage('logs', {
	component: Logs,
	path: 'logs',
});