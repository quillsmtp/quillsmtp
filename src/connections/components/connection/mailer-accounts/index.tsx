/**
 * Internal dependencies
 */
import { MailerModuleSettings } from '@quillsmtp/mailers';

interface Props {
	connectionId: string;
	mailer: MailerModuleSettings;
	slug: string;
	setStep?: (step: number) => void;
}

const MailerAccounts: React.FC<Props> = ({ connectionId, mailer, slug, setStep }) => {
	const Render = () => {
		const Component = mailer?.render;
		if (!Component) {
			return null;
		}
		/* @ts-ignore */
		return <Component connectionId={connectionId} slug={slug} setStep={setStep} />;
	};

	return (
		<div className="qsmtp-mailer-accounts">
			<Render />
		</div>
	);
};

export default MailerAccounts;
