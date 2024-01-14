/**
 * QuillForms Dependencies
 */
import ConfigApi from '@quillsmtp/config';

/**
 * WordPress Dependencies
 */
import { Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import { css } from '@emotion/css';

/**
 * Internal Dependencies
 */
import lockIcon from './lock-icon';
import './style.scss';

interface Props {
	mailerSlug: string;
	showLockIcon: boolean;
}

const MailerFeatureAvailability: React.FC<Props> = ({
	mailerSlug,
	showLockIcon,
}) => {
	const license = {
		status: 'invalid',
	};
	const mailer = ConfigApi.getStoreMailers()[mailerSlug];

	let content = <div></div>;

	if (license?.status !== 'valid') {
		content = (
			<div>
				{showLockIcon && (
					<Icon
						className="mailer-feature-availability-lock-icon"
						icon={lockIcon}
						size={120}
					/>
				)}

				<p
					className={css`
						font-size: 15px;
					`}
				>
					{__("We're sorry", 'quillsmtp')}, {mailer.name}{' '}
					{__('is', 'quillsmtp')}{' '}
					{__('only available on PRO plan', 'quillsmtp')}.
					<br />
					{__('Please upgrade your plan to unlock', 'quillsmtp')}
					<br />
					{__('this feature', 'quillsmtp')}
				</p>
				<a
					href="https://quillsmtp.com"
					target="_blank"
					className="mailer-feature-availability-upgrade-button"
				>
					{__('Upgrade to PRO!', 'quillsmtp')}
				</a>
			</div>
		);
		// else, no license or the license is invalid or the feature of mailer require higher plan.
	}

	return <div className="mailer-feature-availability">{content}</div>;
};

export default MailerFeatureAvailability;
