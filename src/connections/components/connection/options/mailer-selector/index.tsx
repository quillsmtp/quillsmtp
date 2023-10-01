/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { getMailerModules } from '../../../../../mailers';

interface Props {
	connectionId: string;
}

const MailersSelector: React.FC<Props> = ({ connectionId }) => {
	const [selectedMailer, setSelectedMailer] = useState(null);
	const mailerModules = getMailerModules();
	console.log(mailerModules, 'mailerModules');

	return (
		<div>
			<h2>{__('Select Mailer')}</h2>
		</div>
	);
};

export default MailersSelector;
