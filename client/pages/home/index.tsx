/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import ConnectionProvider from '../../connections/components/connection';
import Connection from '../../connections/components/connection/options';

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
