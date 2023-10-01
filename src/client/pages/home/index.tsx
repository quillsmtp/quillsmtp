/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * QuillSMTP dependencies
 */
import { ConnectionProvider, Connection } from '@quillsmtp/connections';

/**
 * Internal dependencies
 */
import './style.scss';

const Home = () => {
	return (
		<div className="qsmtp-home-page">
			<ConnectionProvider>
				<Connection connectionId="default" />
			</ConnectionProvider>
		</div>
	);
};

export default Home;
