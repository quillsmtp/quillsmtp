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
import { GoSortAsc } from "react-icons/go";


/**
 * Internal dependencies
 */
import './style.scss';
import configApi from '@quillsmtp/config';
import { Alert, Button, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, TextField } from '@mui/material';

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
		<div className="qsmtp-license-page pb-32">
			<div className="qsmtp-card-header pl-20">
				<div className="qsmtp-license-settings-header__title font-roboto font-[500] text-[38px] text-[#333333] pb-2">
					{__('License', 'quillsmtp')}
				</div>
			</div>
			{license && !!Object.values(license.upgrades).length &&(
			<div className='flex justify-end pr-20 pb-3'>
				<Button className=''>
					<a //key={index}
						//href={upgrade.url}
						target="_blank" className='flex items-center gap-1 bg-[#3858E9] text-white font-roboto capitalize py-3 px-5 font-[500]'><GoSortAsc className='text-[16px]' /> Upgrade My Plan</a>
				</Button>
			</div>)}
			<Card
				className="qsmtp-license-settings mx-20"
			>

				<CardContent>
					{license ? (
						<div className='border border-[#E5E5E5] w-full'>
							<div className='px-[2rem] pt-[2.5rem] pb-14 flex items-center justify-between'>
								<div className='flex gap-5 items-center'>
									<p className='bg-[#993F7B] rounded-full py-5 px-7 text-white text-[20px]'>M</p>
									<div className='text-[#333333]'>
										<h5 className='font-roboto text-[18px] font-semibold pb-1'>Mohamed Haridy</h5>
										<span className='font-roboto text-[14px]'>Md.Magdy.Sa@gmail.com</span>
									</div>
								</div>
								<div>
									<LoadingButton
										onClick={update}
										loading={isUpdating}
										disabled={
											isDeactivating || isUpdating || isActivating
										}
										className='capitalize font-roboto bg-[#333333] px-9 py-3 text-white hover:text-[#333333]'
									>
										{__('Update Plugin', 'quillsmtp')}
									</LoadingButton>
									<LoadingButton
										onClick={deactivate}
										disabled={
											isDeactivating || isUpdating || isActivating
										}
										loading={isDeactivating}
										variant="contained"
										color="secondary"
										className='capitalize font-roboto bg-transparent px-2 py-3 text-[#333333] ml-[10px] shadow-none hover:bg-[#333333] hover:text-white'
									>
										{__('Deactivate', 'quillsmtp')}
									</LoadingButton>
								</div>
							</div>
							<TableContainer>
								<Table className='font-roboto'>
									<TableHead className='bg-[#333333]'>
										<TableRow className=''>
											<TableCell className='text-white pl-[2.5rem]'>Status</TableCell>
											<TableCell className='text-white pl-[2.5rem]'>Plan</TableCell>
											<TableCell className='text-white pl-[2.5rem]'>Expires</TableCell>
											<TableCell className='text-white pl-[2.5rem]'>Last Update</TableCell>
											<TableCell className='text-white pl-[2.5rem]'>Last Check</TableCell>
										</TableRow>
									</TableHead>
									<TableBody>
										<TableRow>
											<TableCell
												className={
													license.status === 'valid'
														? 'quillsmtp-license-valid pl-[2.5rem]'
														: 'quillsmtp-license-invalid pl-[2.5rem]'
												}
											>
												{license.status_label}
											</TableCell>
											<TableCell className='pl-[2.5rem]'>{license.plan_label}</TableCell>
											<TableCell className='pl-[2.5rem]'>{license.expires}</TableCell>
											<TableCell className='pl-[2.5rem]'>{license.last_update}</TableCell>
											<TableCell className='pl-[2.5rem]'>{license.last_check}</TableCell>
										</TableRow>
									</TableBody>
								</Table>
							</TableContainer>

							{/* { && ( */}
							{/* <div>
							<h3>{__('Upgrades:', 'quillsmtp')}</h3>
							<ul>
								{Object.values(license.upgrades).map(
											(upgrade, index) => { 
								 return ( 
								<li
								key={index}
								>
									<a
										key={index}
										href={upgrade.url}
										target="_blank"
									>
										{__(
											'Upgrade to',
											'quillsmtp'
										)}{' '}
										upgrade.plan_label{' '}
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
						 )} */}
							{/* <div style={{ marginTop: '20px' }}>
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
						</div>*/}
						</div>
					) : (
						<>
						<div className='px-36'>
							<label className='font-roboto text-[#3858E9] mb-2 text-[18px]'>{__('License Key', 'quillsmtp')}</label>
							<TextField
								// label={__('License Key', 'quillsmtp')}
								value={licenseKey}
								onChange={(e) => setLicenseKey(e.target.value)}
								fullWidth
								sx={{
									mb: 2, "& .MuiOutlinedInput-notchedOutline": {
										borderColor: "#9E9E9E",
									},
									"&:hover > .MuiOutlinedInput-notchedOutline": {
										borderColor: "#9E9E9E"
									},
								}}
								className='mt-2'
							/>
							<LoadingButton
								variant="contained"
								onClick={activate}
								loading={isActivating}
								disabled={
									isDeactivating || isUpdating || isActivating
								}
								loadingPosition="start"
								startIcon={ }
								className='bg-[#3858E9] flex mt-[20px] py-[15px] px-[65px]'
							>
								{__('Activate', 'quillsmtp')}
							</LoadingButton>
							</div>
						</>
					)}
				</CardContent>
			</Card>
		</div>
	);
};

export default License;
