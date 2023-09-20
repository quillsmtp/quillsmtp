/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';

/**
 * External Dependencies
 */
import { parse } from 'qs';
import classnames from 'classnames';
import { motion } from 'framer-motion';

/**
 * Internal dependencies
 */
// import AdminNotices from '../admin-notices';
import { registerAdminPage } from '../navigation';
import Home from '../pages/home';

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
		<motion.div
			layoutScroll
			className={classnames('qsmtp-page-component-wrapper', {
				'has-sidebar': !page.template || page.template === 'default',
			})}
		>
			<page.component
				params={params}
				path={url}
				pathMatch={page.path}
				query={query}
			/>
			{/* <AdminNotices /> */}
		</motion.div>
	);
};

registerAdminPage('home', {
	component: Home,
	path: '/',
});
