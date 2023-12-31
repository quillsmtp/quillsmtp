/**
 * Internal dependencies
 */
import { MailerModuleSettings } from '@quillsmtp/mailers';

interface Props {
	connectionId: string;
	mailer: MailerModuleSettings;
	slug: string;
}

const MailerAccounts: React.FC<Props> = ({ connectionId, mailer, slug }) => {
	const Render = () => {
		const Component = mailer.render;

		/* @ts-ignore */
		return <Component connectionId={connectionId} slug={slug} />;
	};

	return (
		<div className="qsmtp-mailer-accounts">
			<Render />
		</div>
	);
};

export default MailerAccounts;
