/**
 * External dependencies
 */
import { filter } from 'lodash';

/**
 * WordPress dependencies
 */
import { SnackbarList, NoticeList } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

const Notices = () => {
	// @ts-expect-error
	const { notices } = useSelect((select) => {
		return {
			// @ts-expect-error
			notices: select('core/notices').getNotices(),
		};
	});

	const { removeNotice } = useDispatch('core/notices');

	const snackbarNotices = filter(notices, {
		type: 'snackbar',
		// @ts-expect-error
	}) as Readonly<NoticeList.Notice[]>;

	useEffect(() => {
		if (snackbarNotices.length > 2) {
			snackbarNotices
				.slice(0, snackbarNotices.length - 2)
				.forEach((notice) => removeNotice(notice.id));
		}
	}, [snackbarNotices]);

	return (
		<>
			<SnackbarList
				notices={snackbarNotices}
				className="admin-components-admin-notices__snackbar"
				onRemove={removeNotice}
			/>
		</>
	);
};

export default Notices;
