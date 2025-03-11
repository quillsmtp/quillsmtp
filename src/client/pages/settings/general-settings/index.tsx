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
import { FaCheck } from "react-icons/fa6";
import OutlinedInput from '@mui/material/OutlinedInput';

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
	const [disableSummaryEmail, setDisableSummaryEmail] = useState<boolean>(
		initialPayload.disable_summary_email
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
				disable_summary_email: disableSummaryEmail,
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
			className="qsmtp-general-settings border border-[#E0E0E0]"
			sx={{ width: '800px', maxWidth: '100%', margin: '0 auto' }}
		>
			<div className="px-[20px] py-3 text-[24px] border-b mb-2 border-[#E0E0E0]">
				<div className="text-[#333333] font-[500] font-roboto">
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
					<FormControl fullWidth sx={{
						mb: 2, "& .MuiOutlinedInput-notchedOutline": {
							borderColor: "gray",
						},
						"&:hover > .MuiOutlinedInput-notchedOutline": {
							borderColor: "gray"
						}
					}}>
						<label className='font-roboto text-[#3858E9] mb-2 text-[16px] font-[500]'>{__('Default Connection', 'quillsmtp')}</label>
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
							input={<OutlinedInput />}
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
					<Alert severity="warning" sx={{
						mb: 2,
						color: "#333 !important",
						svg: {
							fill: "#333 !important"
						},
						div: {
							color: "#333 !important"
						}
					}}>
						{__(
							'You need to create at least one connection to use QuillSMTP.',
							'quillsmtp'
						)}
					</Alert>
				)}
				{size(connections) > 1 ? (
					<FormControl fullWidth sx={{
						mb: 2, "& .MuiOutlinedInput-notchedOutline": {
							borderColor: "gray",
						},
						"&:hover > .MuiOutlinedInput-notchedOutline": {
							borderColor: "gray"
						}
					}} >
						<label className='font-roboto text-[#3858E9] mb-2 text-[16px] font-[500]'>
							{__('Fallback Connection', 'quillsmtp')}
						</label>
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
							input={<OutlinedInput />}

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
							color: "#333 !important",
							svg: {
								fill: "#333 !important"
							},
							div: {
								color: "#333 !important"
							}
						}}
					>
						{__(
							'You need to create at least two connections to use fallback connection.',
							'quillsmtp'
						)}
					</Alert>
				)}
				<div style={{ margin: '20px 0' }}>
					<div className="switch-container">
						<div className={`switch ${disableSummaryEmail ? "checked" : ""}`} onClick={() => setDisableSummaryEmail((prev) => !prev)}>
							<div className="circle">{disableSummaryEmail ? <FaCheck className='text-[#3858E9]' /> : ""}</div>
						</div>
						<span className="font-roboto text-[#333333]">Disable Summary Email</span>
					</div>
					<FormHelperText sx={{ mb: 2 }} className='pt-3 text-[#333333] text-[16px] font-roboto leading-[20px]'>
						{__(
							'Enable This Option To Disable The Summary Email That Is Sent To The Site Administrator.',
							'quillsmtp'
						)}
					</FormHelperText>
				</div>
				<LoadingButton
					variant="contained"
					onClick={saveSettings}
					size='large'
					sx={{
						mt: 7
					}}
					loading={isSaving}
					loadingPosition="start"
					startIcon={<SaveIcon />}
					className='bg-[#333333] px-8 py-3 normal-case font-roboto font-[300]'
				>
					{__('Save Settings', 'quillsmtp')}
				</LoadingButton>
			</CardContent>
		</Card>
	);
};

export default GeneralSettings;
