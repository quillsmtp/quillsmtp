/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { useDispatch } from '@wordpress/data';

/**
 * External Dependencies
 */
import { css } from '@emotion/css';
import { ThreeDots as Loader } from 'react-loader-spinner';
import TextField from '@mui/material/TextField';
import Checkbox from '@mui/material/Checkbox';
import LoadingButton from '@mui/lab/LoadingButton';
import SaveIcon from '@mui/icons-material/Save';
import Box from '@mui/material/Box';
import FormControlLabel from '@mui/material/FormControlLabel';
import { FormControl, FormHelperText } from '@mui/material';

// TODO: Add types
type Settings = {
	from_email: string;
	force_from_email: boolean;
	from_name: string;
	force_from_name: boolean;
} | null;

const General = () => {
	const [settings, setSettings] = useState<Settings>(null);
	const [isSaving, setIsSaving] = useState(false);
	const { createErrorNotice, createSuccessNotice } =
		useDispatch('core/notices');

	useEffect(() => {
		apiFetch({ path: 'qsmtp/v1/settings?groups=general' })
			.then((data: any) => {
				setSettings(data.general);
			})
			.catch(() => {
				setSettings(null);
			});
	}, []);

	const saveSettings = () => {
		setIsSaving(true);
		apiFetch({
			path: 'qsmtp/v1/settings',
			method: 'POST',
			data: settings,
		})
			.then((data: any) => {
				createSuccessNotice(__('✅ Settings saved', 'quillsmtp'), {
					type: 'snackbar',
					isDismissible: true,
				});
				setIsSaving(false);
			})
			.catch(() => {
				createErrorNotice(__('⛔ Something went wrong', 'quillsmtp'), {
					type: 'snackbar',
					isDismissible: true,
				});
				setIsSaving(false);
			});
	};

	return (
		<div className="qsmtp-general-settings-tab">
			{settings === null && (
				<div
					className={css`
						display: flex;
						flex-wrap: wrap;
						width: 100%;
						height: 100px;
						justify-content: center;
						align-items: center;
					`}
				>
					<Loader color="#8640e3" height={50} width={50} />
				</div>
			)}
			{settings !== null && (
				<>
					<Box
						sx={{
							display: 'flex',
							flexDirection: 'column',
							mt: 2,
							mb: 2,
						}}
						component="div"
					>
						<TextField
							label={__('From Email', 'quillsmtp')}
							value={settings.from_email}
							onChange={(e) => {
								setSettings({
									...settings,
									from_email: e.target.value,
								});
							}}
							variant="outlined"
							fullWidth
							sx={{ mb: 2, width: '700px', maxWidth: '100%' }}
							helperText={__(
								'If left blank, the default WordPress from email will be used.',
								'quillsmtp'
							)}
						/>
						<FormControl sx={{ mb: 3 }}>
							<FormControlLabel
								control={
									<Checkbox
										checked={settings.force_from_email}
										onChange={(e) => {
											setSettings({
												...settings,
												force_from_email:
													!settings.force_from_email,
											});
										}}
									/>
								}
								label={__('Force From Email', 'quillsmtp')}
							/>
							<FormHelperText>
								{__(
									'If enabled, the from email will be forced to the above email.',
									'quillsmtp'
								)}
							</FormHelperText>
						</FormControl>
						<TextField
							sx={{ mb: 2, width: '700px', maxWidth: '100%' }}
							label={__('From Name', 'quillsmtp')}
							value={settings.from_name}
							onChange={(e) => {
								setSettings({
									...settings,
									from_name: e.target.value,
								});
							}}
							variant="outlined"
							fullWidth
							helperText={__(
								'If left blank, the default WordPress from name will be used.',
								'quillsmtp'
							)}
						/>
						<FormControl sx={{ mb: 3 }}>
							<FormControlLabel
								control={
									<Checkbox
										checked={settings.force_from_name}
										onChange={(e) => {
											setSettings({
												...settings,
												force_from_name:
													!settings.force_from_name,
											});
										}}
									/>
								}
								label={__('Force From Name', 'quillsmtp')}
							/>
							<FormHelperText>
								{__(
									'If enabled, the from name will be forced to the above name.',
									'quillsmtp'
								)}
							</FormHelperText>
						</FormControl>
						<div>
							<LoadingButton
								loading={isSaving}
								onClick={saveSettings}
								variant="contained"
								color="primary"
								startIcon={<SaveIcon />}
							>
								{__('Save', 'quillsmtp')}
							</LoadingButton>
						</div>
					</Box>
				</>
			)}
		</div>
	);
};

export default General;
