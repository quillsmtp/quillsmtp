/**
 * QuillSMTP dependencies
 */
import {
	getAdminPages,
	NavLink,
	withRouter,
	matchPath,
} from '@quillsmtp/navigation';

/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import { forEach, map } from 'lodash';
import { TfiKey } from "react-icons/tfi";
import { HiOutlineBellAlert } from "react-icons/hi2";



/**
 * Internal dependencies.
 */
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
		<>
			<div className="qsmtp-header">
				<div className="qsmtp-logo">
					<img
						width="150"
						src={qsmtpAdmin?.assetsBuildUrl + 'assets/logo.png'}
						alt="logo"
					/>
				</div>
				<div className='flex'>
				<NavLink
					className="qsmtp-header-link"
					to="/admin.php?page=quillsmtp"
					exact
					isActive={(match, location) => {
						if (
							location.pathname === '' ||
							location.pathname === '/' + ''
						) {
							return true;
						}
					}}
				>
					<svg viewBox="0 1 511 512" fill="currentColor">
						<path d="M498.7 222.7L289.8 13.8a46.8 46.8 0 00-66.7 0L14.4 222.6l-.2.2A47.2 47.2 0 0047 303h8.3v153.7a55.2 55.2 0 0055.2 55.2h81.7a15 15 0 0015-15V376.5a25.2 25.2 0 0125.2-25.2h48.2a25.2 25.2 0 0125.1 25.2V497a15 15 0 0015 15h81.8a55.2 55.2 0 0055.1-55.2V303.1h7.7a47.2 47.2 0 0033.4-80.4zm-21.2 45.4a17 17 0 01-12.2 5h-22.7a15 15 0 00-15 15v168.7a25.2 25.2 0 01-25.1 25.2h-66.8V376.5a55.2 55.2 0 00-55.1-55.2h-48.2a55.2 55.2 0 00-55.2 55.2V482h-66.7a25.2 25.2 0 01-25.2-25.2V288.1a15 15 0 00-15-15h-23A17.2 17.2 0 0135.5 244L244.4 35a17 17 0 0124.2 0l208.8 208.8v.1a17.2 17.2 0 010 24.2zm0 0" />
					</svg>
					{__('Home', 'quillsmtp')}
				</NavLink>
				<NavLink
					className="qsmtp-header-link"
					to={`/admin.php?page=quillsmtp&path=settings`}
					exact
					isActive={(match, location) => {
						if (location.pathname === 'settings') {
							return true;
						}
					}}
				>
					<svg viewBox="0 0 512 512" fill="currentColor">
						<path d="M272 512h-32c-26 0-47.2-21.1-47.2-47.1V454c-11-3.5-21.8-8-32.1-13.3l-7.7 7.7a47.1 47.1 0 01-66.7 0l-22.7-22.7a47.1 47.1 0 010-66.7l7.7-7.7c-5.3-10.3-9.8-21-13.3-32.1H47.1c-26 0-47.1-21.1-47.1-47.1v-32.2c0-26 21.1-47.1 47.1-47.1H58c3.5-11 8-21.8 13.3-32.1l-7.7-7.7a47.1 47.1 0 010-66.7l22.7-22.7a47.1 47.1 0 0166.7 0l7.7 7.7c10.3-5.3 21-9.8 32.1-13.3V47.1c0-26 21.1-47.1 47.1-47.1h32.2c26 0 47.1 21.1 47.1 47.1V58c11 3.5 21.8 8 32.1 13.3l7.7-7.7a47.1 47.1 0 0166.7 0l22.7 22.7a47.1 47.1 0 010 66.7l-7.7 7.7c5.3 10.3 9.8 21 13.3 32.1h10.9c26 0 47.1 21.1 47.1 47.1v32.2c0 26-21.1 47.1-47.1 47.1H454c-3.5 11-8 21.8-13.3 32.1l7.7 7.7a47.1 47.1 0 010 66.7l-22.7 22.7a47.1 47.1 0 01-66.7 0l-7.7-7.7c-10.3 5.3-21 9.8-32.1 13.3v10.9c0 26-21.1 47.1-47.1 47.1zM165.8 409.2a176.8 176.8 0 0045.8 19 15 15 0 0111.3 14.5V465c0 9.4 7.7 17.1 17.1 17.1h32.2c9.4 0 17.1-7.7 17.1-17.1v-22.2a15 15 0 0111.3-14.5c16-4.2 31.5-10.6 45.8-19a15 15 0 0118.2 2.3l15.7 15.7a17.1 17.1 0 0024.2 0l22.8-22.8a17.1 17.1 0 000-24.2l-15.7-15.7a15 15 0 01-2.3-18.2 176.8 176.8 0 0019-45.8 15 15 0 0114.5-11.3H465c9.4 0 17.1-7.7 17.1-17.1v-32.2c0-9.4-7.7-17.1-17.1-17.1h-22.2a15 15 0 01-14.5-11.2c-4.2-16.1-10.6-31.6-19-45.9a15 15 0 012.3-18.2l15.7-15.7a17.1 17.1 0 000-24.2l-22.8-22.8a17.1 17.1 0 00-24.2 0l-15.7 15.7a15 15 0 01-18.2 2.3 176.8 176.8 0 00-45.8-19 15 15 0 01-11.3-14.5V47c0-9.4-7.7-17.1-17.1-17.1h-32.2c-9.4 0-17.1 7.7-17.1 17.1v22.2a15 15 0 01-11.3 14.5c-16 4.2-31.5 10.6-45.8 19a15 15 0 01-18.2-2.3l-15.7-15.7a17.1 17.1 0 00-24.2 0l-22.8 22.8a17.1 17.1 0 000 24.2l15.7 15.7a15 15 0 012.3 18.2 176.8 176.8 0 00-19 45.8 15 15 0 01-14.5 11.3H47c-9.4 0-17.1 7.7-17.1 17.1v32.2c0 9.4 7.7 17.1 17.1 17.1h22.2a15 15 0 0114.5 11.3c4.2 16 10.6 31.5 19 45.8a15 15 0 01-2.3 18.2l-15.7 15.7a17.1 17.1 0 000 24.2l22.8 22.8a17.1 17.1 0 0024.2 0l15.7-15.7a15 15 0 0118.2-2.3z" />
						<path d="M256 367.4c-61.4 0-111.4-50-111.4-111.4s50-111.4 111.4-111.4 111.4 50 111.4 111.4-50 111.4-111.4 111.4zm0-192.8a81.5 81.5 0 000 162.8 81.5 81.5 0 000-162.8z" />
					</svg>
					{__('Settings ', 'quillsmtp')}
				</NavLink>
				<NavLink
					className="qsmtp-header-link"
					to="/admin.php?page=quillsmtp&path=email-test"
					exact
					isActive={(match, location) => {
						if (location.pathname === 'email-test') {
							return true;
						}
					}}
				>
					<svg viewBox="0 0 512 512" fill="currentColor">
						<path d="M467 76H45a45 45 0 00-45 45v270a45 45 0 0045 45h422a45 45 0 0045-45V121a45 45 0 00-45-45zm-6.3 30L287.8 278a44.7 44.7 0 01-63.6 0L51.3 106h409.4zM30 384.9V127l129.6 129L30 384.9zM51.3 406L181 277.2l22 22c14.2 14.1 33 22 53.1 22 20 0 38.9-7.9 53-22l22-22L460.8 406H51.3zM482 384.9L352.4 256 482 127V385z" />
					</svg>
					{__('Email Test', 'quillsmtp')}
				</NavLink>
				<NavLink
					to="/admin.php?page=quillsmtp&path=logs"
					className="qsmtp-header-link"
					exact
					isActive={(match, location) => {
						if (location.pathname === 'logs') {
							return true;
						}
					}}
				>
					<svg
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg"
						fill="currentColor"
					>
						<path d="M10 13a2 2 0 110-4 2 2 0 010 4zm0-2.5a.5.5 0 100 1 .5.5 0 000-1z" />
						<path d="M20.3 11.8h-8.8a.8.8 0 010-1.6h8.8a.8.8 0 010 1.6zM8.5 11.8H3.7a.8.8 0 010-1.6h4.8a.8.8 0 010 1.6zM15 19a2 2 0 110-4 2 2 0 010 4zm0-2.5a.5.5 0 100 1 .5.5 0 000-1z" />
						<path d="M20.3 17.8h-3.8a.8.8 0 010-1.6h3.8a.8.8 0 010 1.6zM13.5 17.8H3.7a.8.8 0 010-1.6h9.8a.8.8 0 010 1.6z" />
						<path d="M21.3 23H2.6A2.8 2.8 0 010 20.2V3.9C0 2.1 1.2 1 2.8 1h18.4C22.9 1 24 2.2 24 3.8v16.4c0 1.6-1.2 2.8-2.8 2.8zM2.6 2.5c-.6 0-1.2.6-1.2 1.3v16.4c0 .7.6 1.3 1.3 1.3h18.4c.7 0 1.3-.6 1.3-1.3V3.9c0-.7-.6-1.3-1.3-1.3z" />
						<path d="M23.3 6H.6a.8.8 0 010-1.5h22.6a.8.8 0 010 1.5z" />
					</svg>
					Logs
				</NavLink>
				<NavLink
					to="/admin.php?page=quillsmtp&path=debug"
					className="qsmtp-header-link"
					exact
					isActive={(match, location) => {
						if (location.pathname === 'debug') {
							return true;
						}
					}}
				>
					<svg fill="currentColor" viewBox="0 0 24 24">
						<path d="M0 0h24v24H0z" fill="none"></path><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg>
					Debug
				</NavLink>
				<NavLink
					to="/admin.php?page=quillsmtp&path=alerts"
					className="qsmtp-header-link"
					exact
					isActive={(match, location) => {
						if (location.pathname === 'alerts') {
							return true;
						}
					}}
				>
					<svg fill="currentColor" height="24" width="24" viewBox="0 0 64 64"><g id="BELL_1_" enable-background="new"><g id="BELL"><g id="BELL"><g><g><path d="M52,45c-1.657,0-3-1.343-3-3V22c0-7.732-6.268-14-14-14c0-1.657-1.343-3-3-3s-3,1.343-3,3c-7.732,0-14,6.268-14,14v20
				c0,1.657-1.343,3-3,3s-3,1.343-3,3s1.343,3,3,3h40c1.657,0,3-1.343,3-3S53.657,45,52,45z M32,60c3.314,0,6-2.686,6-6H26
				C26,57.314,28.686,60,32,60z"><path d="M52,45c-1.657,0-3-1.343-3-3V22c0-7.732-6.268-14-14-14c0-1.657-1.343-3-3-3s-3,1.343-3,3c-7.732,0-14,6.268-14,14v20
				c0,1.657-1.343,3-3,3s-3,1.343-3,3s1.343,3,3,3h40c1.657,0,3-1.343,3-3S53.657,45,52,45z M32,60c3.314,0,6-2.686,6-6H26
				C26,57.314,28.686,60,32,60z"></path></path></g></g></g></g></g></svg>
					Alerts
				</NavLink>
				</div>
				<NavLink
					to="/admin.php?page=quillsmtp&path=license"
					className="qstmp-header-license"
					exact
					isActive={(match, location) => {
						if (location.pathname === 'license') {
							return true;
						}
					}}
				>
					<TfiKey className='size-12'/>
					License
				</NavLink>


			</div>
		</>
	);
};

export default withRouter(NavBar);
