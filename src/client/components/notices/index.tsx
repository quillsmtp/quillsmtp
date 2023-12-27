/**
 * External dependencies
 */
import React from 'react';
import { keys, isEmpty } from 'lodash';
import Snackbar from '@mui/material/Snackbar';
import MuiAlert, { AlertProps } from '@mui/material/Alert';

/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect } from 'react';

/**
 * Internal dependencies
 */
import './style.scss';

const Alert = React.forwardRef<HTMLDivElement, AlertProps>(
	function Alert(props, ref) {
		return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
	}
);

const Notices = () => {
	const { notices } = useSelect((select) => {
		return {
			notices: select('quillSMTP/core').getNotices(),
		};
	});

	const { deleteNotice } = useDispatch('quillSMTP/core');

	useEffect(() => {
		// Remove first notice if notice more than 2
		if (!isEmpty(notices) && keys(notices).length > 2) {
			deleteNotice(keys(notices)[0]);
		}
	}, [notices]);

	return (
		<>
			{keys(notices).map((noticeId) => {
				const notice = notices[noticeId];

				return (
					<Snackbar
						key={noticeId}
						open={true}
						autoHideDuration={notice?.duration || 6000}
						anchorOrigin={
							notice?.anchorOrigin || {
								vertical: 'bottom',
								horizontal: 'center',
							}
						}
						onClose={() => deleteNotice(noticeId)}
					>
						<Alert
							severity={notice.type}
							onClose={() => deleteNotice(noticeId)}
						>
							{notice.message}
						</Alert>
					</Snackbar>
				);
			})}
		</>
	);
};

export default Notices;
