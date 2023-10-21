/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * QuillSMTP dependencies
 */
import { ConnectionsList } from '@quillsmtp/connections';

/**
 * Internal dependencies
 */
import './style.scss';

const Home = () => {
	return (
		<div className="qsmtp-home-page">
			<ConnectionsList />
		</div>
	);
};

export default Home;
