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

// @ts-ignore
const PageAvailability: React.FC<Props> = ({ showLockIcon }) => {
	return <div></div>;
};

export default PageAvailability;
