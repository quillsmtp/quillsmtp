/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * QuillSMTP dependencies
 */
import { Connection } from '@quillsmtp/connections';

/**
 * Internal dependencies
 */
import './style.scss';

const Home = () => {
	return (
		<div className="qsmtp-home-page">
			<Connection connectionId="default" />
		</div>
	);
};

export default Home;
