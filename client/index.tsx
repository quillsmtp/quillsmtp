import { render } from '@wordpress/element';
import { doAction } from '@wordpress/hooks';
import '@wordpress/core-data';
import '@wordpress/notices';
import PageLayout from './layout';
import './mailers';
import './style.scss';
import configApi from './config';

const appRoot = document.getElementById('qsmtp-admin-root');
render(<PageLayout />, appRoot);

doAction('QuillSMTP.Admin.PluginsLoaded');

// @ts-ignore
window.qsmtp = {
	config: configApi,
};
