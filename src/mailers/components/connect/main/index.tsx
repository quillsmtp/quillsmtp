/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ConnectMain } from '../../types';
import AccountSelector from './account-selector';

interface Props {
	connectionId: string;
	main: ConnectMain;
}

const Main: React.FC<Props> = ({ connectionId, main }) => {
	return (
		<div className="mailer-connect-main">
			<div className="mailer-connect-main__wrapper">
				<AccountSelector connectionId={connectionId} main={main} />
			</div>
		</div>
	);
};

export default Main;
