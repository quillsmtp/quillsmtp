/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';

/**
 * External dependencies
 */
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import LoadingButton from '@mui/lab/LoadingButton';
import SaveIcon from '@mui/icons-material/Save';

/**
 * Internal dependencies
 */
import './style.scss';
import configApi from '@quillsmtp/config';
import { Alert, TextField } from '@mui/material';

const License = () => {
	const license = configApi.getLicense();
	const pluginData = configApi.getProPluginData();
	const [count, setCount] = useState(0); // counter used for force update.
	const [licenseKey, setLicenseKey] = useState('');
	const [isUpdating, setIsUpdating] = useState(false);
	const [isDeactivating, setIsDeactivating] = useState(false);
	const [isActivating, setIsActivating] = useState(false);
	const [isInstalling, setIsInstalling] = useState(false);
	const [isActivatingPlugin, setIsActivatingPlugin] = useState(false);


	// dispatch notices.
	const { createNotice } = useDispatch('quillSMTP/core');

	const activate = () => {
		if (isDeactivating || isUpdating || isActivating) return;
		setIsActivating(true);
		const data = new FormData();
		data.append('action', 'quillsmtp_license_activate');
		data.append('_nonce', window['qsmtpAdmin'].license_nonce);
		data.append('license_key', licenseKey?.trim());

		fetch(`${window['qsmtpAdmin'].adminUrl}admin-ajax.php`, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		})
			.then((res) => res.json())
			.then((res) => {
				if (res.success) {
					configApi.setLicense(res.data);
					setCount(count + 1);
					createNotice({
						type: 'success',
						message: __(
							'License activated successfully.',
							'quillsmtp'
						),
					});

					if (!pluginData.is_installed) {
						installPlugin();
					} else if (pluginData.is_installed && !pluginData.is_active) {
						activatePlugin();
					}
				} else {
					createNotice({
						type: 'error',
						message: res.data,
					});
				}
				setIsActivating(false);
			})
			// @ts-ignore
			.catch((err) => {
				setIsActivating(false);
				createNotice({
					type: 'error',
					message: __('Something went wrong', 'quillsmtp'),
				});
			});
	};

	const update = () => {
		if (isDeactivating || isUpdating || isActivating) return;
		setIsUpdating(true);
		const data = new FormData();
		data.append('action', 'quillsmtp_license_update');
		data.append('_nonce', window['qsmtpAdmin'].license_nonce);

		fetch(`${window['qsmtpAdmin'].adminUrl}admin-ajax.php`, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		})
			.then((res) => res.json())
			.then((res) => {
				if (res.success) {
					configApi.setLicense(res.data);
					setCount(count + 1);
					createNotice({
						type: 'success',
						message: __(
							'License updated successfully.',
							'quillsmtp'
						),
					});
				} else {
					createNotice({
						type: 'error',
						message: res.data,
					});
				}
				setIsUpdating(false);
			})
			// @ts-ignore
			.catch((err) => {
				createNotice({
					type: 'error',
					message: __('Something went wrong', 'quillsmtp'),
				});
				setIsUpdating(false);
			});
	};

	const deactivate = () => {
		if (isDeactivating || isUpdating || isActivating) return;
		setIsDeactivating(true);
		const data = new FormData();
		data.append('action', 'quillsmtp_license_deactivate');
		data.append('_nonce', window['qsmtpAdmin'].license_nonce);

		fetch(`${window['qsmtpAdmin'].adminUrl}admin-ajax.php`, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		})
			.then((res) => res.json())
			.then((res) => {
				if (res.success) {
					configApi.setLicense(false);
					setCount(count + 1);
					createNotice({
						type: 'success',
						message: __(
							'License deactivated successfully.',
							'quillsmtp'
						),
					});
				} else {
					createNotice({
						type: 'error',
						message: res.data,
					});
				}

				setIsDeactivating(false);
			})
			// @ts-ignore
			.catch((err) => {
				createNotice({
					type: 'error',
					message: __('Something went wrong', 'quillsmtp'),
				});

				setIsDeactivating(false);
			});
	};

	const installPlugin = () => {
		if (isDeactivating || isUpdating || isActivating) return;
		setIsInstalling(true);
		const data = new FormData();
		data.append('action', 'quillsmtp_install_pro');
		data.append('_nonce', window['qsmtpAdmin'].license_nonce);

		fetch(`${window['qsmtpAdmin'].adminUrl}admin-ajax.php`, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		})
			.then((res) => res.json())
			.then((res) => {
				if (res.success) {
					setCount(count + 1);
					configApi.setProPluginData({
						...configApi.getProPluginData(),
						is_installed: true,
					});
					activatePlugin();
				} else {
					createNotice({
						type: 'error',
						message: res.data,
					});
				}
				setIsInstalling(false);
			})
			// @ts-ignore
			.catch((err) => {
				createNotice({
					type: 'error',
					message: __('Something went wrong', 'quillsmtp'),
				});
				setIsInstalling(false);
			});
	};

	const activatePlugin = () => {
		if (isDeactivating || isUpdating || isActivating || isInstalling) return;
		setIsActivatingPlugin(true);
		const data = new FormData();
		data.append('action', 'quillsmtp_activate_pro');
		data.append('_nonce', window['qsmtpAdmin'].license_nonce);

		fetch(`${window['qsmtpAdmin'].adminUrl}admin-ajax.php`, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		})
			.then((res) => res.json())
			.then((res) => {
				if (res.success) {
					setCount(count + 1);
					createNotice({
						type: 'success',
						message: __(
							'Plugin activated successfully.',
							'quillsmtp'
						),
					});
					configApi.setProPluginData({
						...configApi.getProPluginData(),
						is_active: true,
					});
				} else {
					createNotice({
						type: 'error',
						message: res.data,
					});
				}
				setIsActivatingPlugin(false);
			})
			// @ts-ignore
			.catch((err) => {
				createNotice({
					type: 'error',
					message: __('Something went wrong', 'quillsmtp'),
				});
				setIsActivatingPlugin(false);
			});
	};

	return (
		<div className="qsmtp-license-page">
			<Card
				className="qsmtp-license-settings qsmtp-card"
				style={{ width: '800px', maxWidth: '100%', margin: '0 auto' }}
			>
				<div className="qsmtp-card-header">
					<div className="qsmtp-license-settings-header__title">
						{__('License', 'quillsmtp')}
					</div>
				</div>
				<CardContent>
					{license ? (
						<div>
							<table>
								<tbody>
									<tr>
										<td>{__('Status', 'quillsmtp')}</td>
										<td>
											<span
												className={
													license.status === 'valid'
														? 'quillsmtp-license-valid'
														: 'quillsmtp-license-invalid'
												}
											>
												{license.status_label}
											</span>
										</td>
									</tr>
									<tr>
										<td>{__('Plan', 'quillsmtp')}</td>
										<td>{license.plan_label}</td>
									</tr>
									<tr>
										<td>{__('Expires', 'quillsmtp')}</td>
										<td>{license.expires}</td>
									</tr>
									<tr>
										<td>
											{__('Last update', 'quillsmtp')}
										</td>
										<td>{license.last_update}</td>
									</tr>
									<tr>
										<td>{__('Last check', 'quillsmtp')}</td>
										<td>{license.last_check}</td>
									</tr>
								</tbody>
							</table>
							<LoadingButton
								onClick={update}
								loading={isUpdating}
								disabled={
									isDeactivating || isUpdating || isActivating
								}
							>
								{__('Update', 'quillsmtp')}
							</LoadingButton>
							<LoadingButton
								onClick={deactivate}
								disabled={
									isDeactivating || isUpdating || isActivating
								}
								loading={isDeactivating}
								variant="contained"
								color="secondary"
								style={{
									marginLeft: '10px',
								}}
							>
								{__('Deactivate', 'quillsmtp')}
							</LoadingButton>
							{!!Object.values(license.upgrades).length && (
								<div>
									<h3>{__('Upgrades:', 'quillsmtp')}</h3>
									<ul>
										{Object.values(license.upgrades).map(
											(upgrade, index) => {
												return (
													<li key={index}>
														<a
															key={index}
															href={upgrade.url}
															target="_blank"
														>
															{__(
																'Upgrade to',
																'quillsmtp'
															)}{' '}
															{upgrade.plan_label}{' '}
															{__(
																'plan',
																'quillsmtp'
															)}{' '}
														</a>
													</li>
												);
											}
										)}
									</ul>
								</div>
							)}
							<div style={{ marginTop: '20px' }}>
								{!pluginData.is_installed && (
									<LoadingButton
										variant="contained"
										onClick={installPlugin}
										loading={isInstalling}
										disabled={
											isDeactivating ||
											isUpdating ||
											isActivating ||
											isInstalling
										}
										loadingPosition="start"
										startIcon={<SaveIcon />}
										style={{
											display: 'flex',
											marginTop: '20px',
										}}
									>
										{isInstalling ? __('Installing...', 'quillsmtp') : __('Install Plugin', 'quillsmtp')}
									</LoadingButton>
								)}
								{pluginData.is_installed && !pluginData.is_active && (
									<LoadingButton
										variant="contained"
										onClick={activatePlugin}
										loading={isActivatingPlugin}
										disabled={
											isDeactivating ||
											isUpdating ||
											isActivating ||
											isInstalling ||
											isActivatingPlugin
										}
										loadingPosition="start"
										startIcon={<SaveIcon />}
										style={{
											display: 'flex',
											marginTop: '20px',
										}}
									>
										{isActivatingPlugin ? __('Activating...', 'quillsmtp') : __('Activate Plugin', 'quillsmtp')}
									</LoadingButton>
								)}
								{pluginData.is_installed && pluginData.is_active && (
									<Alert severity="success">
										{__('QuillSMTP Pro is active.', 'quillsmtp')}
									</Alert>
								)}
							</div>
						</div>
					) : (
						<>
							<TextField
								label={__('License Key', 'quillsmtp')}
								value={licenseKey}
								onChange={(e) => setLicenseKey(e.target.value)}
								fullWidth
								sx={{
									mb: 2, "& .MuiOutlinedInput-notchedOutline": {
										borderColor: "gray",
									},
									"&:hover > .MuiOutlinedInput-notchedOutline": {
										borderColor: "gray"
									}
								}}
							/>
							<LoadingButton
								variant="contained"
								onClick={activate}
								loading={isActivating}
								disabled={
									isDeactivating || isUpdating || isActivating
								}
								loadingPosition="start"
								startIcon={<SaveIcon />}
								style={{
									display: 'flex',
									marginTop: '20px',
								}}
							>
								{__('Activate', 'quillsmtp')}
							</LoadingButton>

						</>
					)}
				</CardContent>
			</Card>
		</div>
	);
};

export default License;
