/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * Internal dependencies
 */
import { MailerModuleSettings } from '@quillsmtp/mailers';
import { Connect, SettingsRender } from '@quillsmtp/mailers';
import { useEffect } from 'react';

interface Props {
	connectionId: string;
	mailer: MailerModuleSettings;
	slug: string;
	setStep?: (step: number) => void;
}

const MailerAccounts: React.FC<Props> = ({
	connectionId,
	mailer,
	slug,
	setStep,
}) => {
	useEffect(() => {
		if (!mailer) {
			return;
		}

		if ('phpmailer' === slug && setStep) {
			setStep(4);
		}
	}, [mailer]);

	const Render = () => {
		const connectParameters = mailer?.connectParameters;
		if (!connectParameters === null) {
			return null;
		}

		if (isEmpty(connectParameters)) {
			return (
				<SettingsRender
					slug={slug}
					connectionId={connectionId}
					setStep={setStep}
				/>
			);
		}

		/* @ts-ignore */
		return <Connect connectionId={connectionId} {...connectParameters} />;
	};

	return (
		<div className="qsmtp-mailer-accounts">
			<Render />
		</div>
	);
};

export default MailerAccounts;
