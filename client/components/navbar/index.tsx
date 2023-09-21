/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import { forEach, map } from 'lodash';

/**
 * Internal dependencies.
 */
import {
	getAdminPages,
	NavLink,
	withRouter,
	matchPath,
} from '../../navigation';
import './style.scss';

const clean = (str) => {
	return str.replace('quillsmtp', '').replace('&path=', '');
};

const matchesRegesiteredRoutes = (path) => {
	let ret = false;
	forEach(getAdminPages(), (page) => {
		const match = matchPath(path, {
			path: page.path,
			exact: true,
			strict: false,
		});
		ret = true;
		return;
	});
	return ret;
};

const NavBar = () => {
	return (
		<div className="qsmtp-navbar">
			<div className="qsmtp-navbar__inner">
				<div className="qsmtp-navbar__logo">
					<h2>
						<NavLink to="/" exact>
							{__('Quill SMTP', 'quillsmtp')}
						</NavLink>
					</h2>
				</div>
				<div className="qsmtp-navbar__links">
					{/* @ts-ignore */}
					{map(qsmtpAdmin.submenuPages, (page, index) => {
						if (matchesRegesiteredRoutes('/' + clean(page[2]))) {
							return (
								<NavLink
									key={`page-${index}`}
									isActive={(match, location) => {
										if (
											location.pathname ===
												clean(page[2]) ||
											location.pathname ===
												'/' + clean(page[2])
										) {
											return true;
										}
									}}
									activeClassName="selected"
									className="qsmtp-navbar__link"
									to={`/admin.php?page=${page[2]}`}
								>
									{page[0]}
								</NavLink>
							);
						} else {
							return (
								<a
									href={`/admin.php?page=${page[2]}`}
									className="qsmtp-navbar__link"
									target="_blank"
								/>
							);
						}
					})}
				</div>
			</div>
		</div>
	);
};

export default withRouter(NavBar);
