import { render } from '@wordpress/element';
import { doAction } from '@wordpress/hooks';
import '@quillsmtp/store';
import '@wordpress/core-data';
import '@wordpress/notices';
import '@quillsmtp/mailers';
import PageLayout from './layout';
import './style.scss';
const appRoot = document.getElementById('qsmtp-admin-root');
render(<PageLayout />, appRoot);

doAction('QuillSMTP.Admin.PluginsLoaded');
