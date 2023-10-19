/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal Dependencies
 */
import type { ConnectMainAccounts } from '../../../types';
import Credentials from './credentials';
import Oauth from './oauth';

interface Props {
	data: ConnectMainAccounts;
	onAdding?: (status: boolean) => void;
	onAdded: (id: string, account: { name: string }) => void;
}

const AccountAuth: React.FC<Props> = ({ data, onAdding, onAdded }) => {
	return (
		<div className="mailer-auth">
			{data.auth.type === 'oauth' ? (
				<Oauth
					labels={data.labels}
					Instructions={data.auth.Instructions}
					onAdded={onAdded}
				/>
			) : (
				<Credentials
					labels={data.labels}
					fields={data.auth.fields}
					Instructions={data.auth.Instructions}
					onAdding={onAdding}
					onAdded={onAdded}
				/>
			)}
		</div>
	);
};

export default AccountAuth;
