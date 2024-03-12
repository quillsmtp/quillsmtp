/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';
import { getMailerModules } from '@quillsmtp/mailers';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * External dependencies
 */
import { size, map, keys } from 'lodash';
import InputLabel from '@mui/material/InputLabel';
import MenuItem from '@mui/material/MenuItem';
import FormControl from '@mui/material/FormControl';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import Alert from '@mui/material/Alert';
import LoadingButton from '@mui/lab/LoadingButton';
import SaveIcon from '@mui/icons-material/Save';
import FormControlLabel from '@mui/material/FormControlLabel';
import Switch from '@mui/material/Switch';
import FormHelperText from '@mui/material/FormHelperText';

/**
 * Internal dependencies
 */
import './style.scss';

const GeneralSettings: React.FC = () => {
	const initialPayload = ConfigAPI.getInitialPayload();
	const isMultisite = ConfigAPI.getIsMultisite();
	const isMainSite = ConfigAPI.getIsMainSite();
	const [isSaving, setIsSaving] = useState(false);
	const [defaultConnection, setDefaultConnection] = useState(
		initialPayload.default_connection || ''
	);
	const [fallbackConnection, setFallbackConnection] = useState(
		initialPayload.fallback_connection || ''
	);
	const [globalSettings, setGlobalSettings] = useState<boolean>(
		initialPayload.global_network_settings
	);
	const { connections } = useSelect((select) => {
		return {
			connections: select('quillSMTP/core').getConnections(),
		};
	});
	const mailersModules = getMailerModules();

	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	const saveSettings = () => {
		setIsSaving(true);
		apiFetch({
			path: '/qsmtp/v1/settings',
			method: 'POST',
			data: {
				default_connection: defaultConnection,
				fallback_connection: fallbackConnection,
				global_network_settings: globalSettings,
			},
		}).then((res: any) => {
			if (res.success) {
				ConfigAPI.setInitialPayload({
					...ConfigAPI.getInitialPayload(),
					default_connection: defaultConnection,
					fallback_connection: fallbackConnection,
				});
				createNotice({
					type: 'success',
					message: __('Settings saved successfully.', 'quillsmtp'),
				});
			} else {
				createNotice({
					type: 'error',
					message: __('Failed to save settings.', 'quillsmtp'),
				});
			}
			setIsSaving(false);
		});
	};

	return (
		<Card
			className="qsmtp-general-settings"
			sx={{ width: '800px', maxWidth: '100%', margin: '0 auto' }}
		>
			<div className="qsmtp-general-settings-header">
				<div className="qsmtp-general-settings-header__title">
					{__('General', 'quillsmtp')}
				</div>
			</div>
			<CardContent>
				{isMultisite && isMainSite && (
					<>
						<FormControlLabel
							control={
								<Switch
									checked={globalSettings}
									name="global_network_settings"
									onChange={(event) => {
										setGlobalSettings(event.target.checked);
									}}
								/>
							}
							label={__('Global Network Settings', 'quillsmtp')}
						/>
						<FormHelperText sx={{ mb: 2 }}>
							{__(
								'Enable this option to use the same settings across all sites on the network.',
								'quillsmtp'
							)}
						</FormHelperText>
					</>
				)}
				{size(connections) > 0 ? (
					<FormControl fullWidth sx={{ mb: 2 }}>
						<InputLabel id="qsmtp-general-settings-default-connection-label">
							{__('Default Connection', 'quillsmtp')}
						</InputLabel>
						<Select
							labelId="qsmtp-general-settings-default-connection-label"
							id="qsmtp-general-settings-default-connection"
							value={
								connections[defaultConnection]
									? defaultConnection
									: keys(connections)[0]
							}
							label={__('Default Connection', 'quillsmtp')}
							onChange={(event: SelectChangeEvent) => {
								setDefaultConnection(event.target.value);
							}}
						>
							{map(keys(connections), (key) => {
								return (
									<MenuItem value={key} key={key}>
										{`${connections[key].name}`}
										{connections[key].mailer && (
											<>
												{' '}
												-{' '}
												{
													mailersModules[
														connections[key].mailer
													]?.title
												}
											</>
										)}
									</MenuItem>
								);
							})}
						</Select>
					</FormControl>
				) : (
					<Alert severity="warning" sx={{ mb: 2 }}>
						{__(
							'You need to create at least one connection to use QuillSMTP.',
							'quillsmtp'
						)}
					</Alert>
				)}
				{size(connections) > 1 ? (
					<FormControl fullWidth sx={{ mb: 2 }}>
						<InputLabel id="qsmtp-general-settings-connections-label">
							{__('Fallback Connection', 'quillsmtp')}
						</InputLabel>
						<Select
							labelId="qsmtp-general-settings-connections-label"
							id="qsmtp-general-settings-connections"
							value={
								connections[fallbackConnection]
									? fallbackConnection
									: ''
							}
							label={__('Fallback Connection', 'quillsmtp')}
							onChange={(event: SelectChangeEvent) => {
								setFallbackConnection(event.target.value);
							}}
						>
							{/* None Menu Item */}
							<MenuItem value="" key="">
								{__('None', 'quillsmtp')}
							</MenuItem>
							{map(keys(connections), (key) => {
								return (
									<MenuItem
										key={key}
										value={key}
										disabled={
											key ===
											initialPayload.default_connection
										}
									>
										{`${connections[key].name}`}
										{connections[key].mailer && (
											<>
												{' '}
												-{' '}
												{
													mailersModules[
														connections[key].mailer
													]?.title
												}
											</>
										)}
									</MenuItem>
								);
							})}
						</Select>
					</FormControl>
				) : (
					<Alert
						severity="warning"
						sx={{
							mb: 2,
						}}
					>
						{__(
							'You need to create at least two connections to use fallback connection.',
							'quillsmtp'
						)}
					</Alert>
				)}
				<LoadingButton
					variant="contained"
					onClick={saveSettings}
					loading={isSaving}
					loadingPosition="start"
					startIcon={<SaveIcon />}
				>
					{__('Save Settings', 'quillsmtp')}
				</LoadingButton>
			</CardContent>
		</Card>
	);
};

export default GeneralSettings;
