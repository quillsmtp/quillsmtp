/**
 * Internal dependencies
 */
import { MailerModuleSettings } from '@quillsmtp/mailers';

interface Props {
	connectionId: string;
	mailer: MailerModuleSettings;
}

const MailerAccounts: React.FC<Props> = ({ connectionId, mailer }) => {
	const Render = () => {
		const Component = mailer.render;

		/* @ts-ignore */
		return <Component connectionId={connectionId} />;
	};

	return (
		<div className="qsmtp-mailer-accounts">
			<Render />
		</div>
	);
};

export default MailerAccounts;
