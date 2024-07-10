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
	showLockIcon: boolean;
}

const PageAvailability: React.FC<Props> = ({ showLockIcon }) => {
	const license = {
		status: 'invalid',
	};

	let content = <div></div>;

	if (license?.status !== 'valid') {
		content = (
			<div>
				{showLockIcon && (
					<Icon
						className="page-feature-availability-lock-icon"
						icon={lockIcon}
						size={120}
					/>
				)}

				<p
					className={css`
						font-size: 15px;
					`}
				>
					{__(
						"We're sorry this page is only available on PRO plan",
						'quillsmtp'
					)}
					<br />
					{__('Please upgrade your plan to unlock', 'quillsmtp')}
					<br />
					{__('this feature', 'quillsmtp')}
				</p>
				<a
					href="https://quillsmtp.com"
					target="_blank"
					className="page-feature-availability-upgrade-button"
				>
					{__('Upgrade to PRO!', 'quillsmtp')}
				</a>
			</div>
		);
		// else, no license or the license is invalid or the feature of page require higher plan.
	}

	return <div className="page-feature-availability">{content}</div>;
};

export default PageAvailability;
