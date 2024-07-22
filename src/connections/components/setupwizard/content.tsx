/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';
import { getMailerModules } from '@quillsmtp/mailers';

/**
 * WordPress Dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { Icon } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { check } from '@wordpress/icons';

/**
 * External Dependencies
 */
import { TextField } from '@mui/material';
import { __ } from '@wordpress/i18n';
import { motion } from 'framer-motion';
import Button from '@mui/material/Button';
import LoadingButton from '@mui/lab/LoadingButton';

/**
 * Internal Dependencies
 */
import './style.scss';
import MailersSelector from '../connection/mailer-selector';
import MailerAccounts from '../connection/mailer-accounts';
import classNames from 'classnames';
import FromEmail from '../connection/from-email';
import ForceFromEmail from '../connection/force-from-email';
import FromName from '../connection/from-name';
import ForceFromName from '../connection/force-from-name';
import Notices from '../../../client/components/notices';

const WizardContent = ({ connectionId, mode, setSetUpWizard }) => {
	const [step, setStep] = useState(1);
	const [showNextButton, setShowNextButton] = useState(false);
	const [isSaving, setIsSaving] = useState(false);
	// dispatch notices.
	const {
		createNotice,
		removeAllTempConnections,
		addConnection,
		updateConnection,
	} = useDispatch('quillSMTP/core');

	const mailerModules = getMailerModules();
	const { mailerSlug, connectionName, accountId, connection } = useSelect(
		(select) => {
			return {
				mailerSlug:
					select('quillSMTP/core').getTempConnectionMailer(
						connectionId
					),
				connectionName:
					select('quillSMTP/core').getTempConnectionName(
						connectionId
					),
				accountId:
					select('quillSMTP/core').getTempConnectionAccountId(
						connectionId
					),
				connection:
					select('quillSMTP/core').getTempConnection(connectionId),
			};
		}
	);

	const save = () => {
		setIsSaving(true);

		const updatedConnection = { ...connection };
		apiFetch({
			path: `/qsmtp/v1/settings`,
			method: 'POST',
			data: {
				connections: {
					...ConfigAPI.getInitialPayload().connections,
					[connectionId]: updatedConnection,
				},
			},
		}).then((res: any) => {
			if (res.success) {
				ConfigAPI.setInitialPayload({
					...ConfigAPI.getInitialPayload(),
					connections: {
						...ConfigAPI.getInitialPayload().connections,
						[connectionId]: connection,
					},
				});
				setStep(step + 1);
				if (mode === 'add') {
					addConnection(connectionId, connection, false);
				} else {
					updateConnection(connectionId, connection);
				}
			} else {
				createNotice({
					type: 'error',
					message: __('Error saving settings.', 'quillsmtp'),
				});
			}
			setIsSaving(false);
		});
	};

	useEffect(() => {
		if (step === 1 && connectionName) {
			setShowNextButton(true);
			return;
		}
		if (step === 2 && mailerSlug) {
			setShowNextButton(true);
			return;
		}
		if (step === 3 && accountId) {
			setShowNextButton(true);
			return;
		}

		if (step === 4) {
			setShowNextButton(true);
			return;
		}
		setShowNextButton(false);
	}, [step, connectionName, mailerSlug, accountId]);

	const { updateTempConnection } = useDispatch('quillSMTP/core');

	return (
		<>
			<div className="qsmtp-setup-wizard__sidebar">
				<div className="qsmtp-setup-wizard__sidebar-logo">
					<img
						width="40"
						src={qsmtpAdmin?.assetsBuildUrl + 'assets/logo.svg'}
						alt="logo"
					/>{' '}
				</div>

				<div className="qsmtp-setup-wizard__sidebar-steps">
					{[1, 2, 3, 4].map((s) => (
						<div
							className={`qsmtp-setup-wizard__sidebar-step-wrapper`}
							key={s}
						>
							<div className="qsmtp-setup-wizard__sidebar-step-line"></div>

							<div
								className={`qsmtp-setup-wizard__sidebar-step ${
									s === step
										? 'qsmtp-setup-wizard__sidebar-step--active'
										: ''
								} 
                             ${
									s < step
										? 'qsmtp-setup-wizard__sidebar-step--checked'
										: ''
								}
                            `}
								key={s}
							>
								<div className="qsmtp-setup-wizard__sidebar-step-number">
									{s < step ? <Icon icon={check} /> : s}
								</div>
							</div>
						</div>
					))}
				</div>
			</div>
			<div className="qsmtp-setup-wizard__content">
				{step === 1 && (
					<>
						<div className="qsmtp-setup-wizard__header">
							<h2 className="qsmtp-setup-wizard__header-title">
								{__(
									"Let's start with the connection name",
									'quillsmtp'
								)}
							</h2>
							<p>
								The connection name is used to identify the
								connection in the connection list.
							</p>
						</div>
						<motion.div
							initial={{ opacity: 0 }}
							animate={{ opacity: 1 }}
							transition={{ duration: 0.5 }}
							className="qsmtp-setup-wizard__first-step"
						>
							<TextField
								autoComplete="new-password"
								label={__('Connection Name', 'quillsmtp')}
								value={connectionName}
								onChange={(e) => {
									updateTempConnection(connectionId, {
										name: e.target.value,
									});
								}}
								variant="outlined"
								color="primary"
								fullWidth
								sx={{ mb: 2 }}
							/>
						</motion.div>
					</>
				)}
				{step === 2 && (
					<>
						<div className="qsmtp-setup-wizard__header">
							<h2 className="qsmtp-setup-wizard__header-title">
								{__(
									'Please select your mail provider',
									'quillsmtp'
								)}
							</h2>
							<p>
								{' '}
								Select the mail provider you want to connect to.
								If you don't see your provider, please select
								the "Other" option.{' '}
							</p>
						</div>
						<motion.div
							initial={{ opacity: 0 }}
							animate={{ opacity: 1 }}
							transition={{ duration: 0.5 }}
							className="qsmtp-setup-wizard__second-step"
						>
							<MailersSelector connectionId={connectionId} />
						</motion.div>
					</>
				)}

				{step == 3 && (
					<>
						<div className="qsmtp-setup-wizard__header">
							<h2 className="qsmtp-setup-wizard__header-title">
								{__(
									"Let's configure your mail provider account settings",
									'quillsmtp'
								)}
							</h2>
							<p>
								{' '}
								Configure your mail provider account settings to
								connect to your mail provider.{' '}
							</p>
						</div>
						<motion.div
							initial={{ opacity: 0 }}
							animate={{ opacity: 1 }}
							transition={{ duration: 0.5 }}
							className="qsmtp-setup-wizard__second-step"
						>
							<MailerAccounts
								connectionId={connectionId}
								mailer={mailerModules[mailerSlug]}
								slug={mailerSlug}
								setStep={setStep}
							/>
						</motion.div>
					</>
				)}

				{step === 4 && (
					<>
						<div className="qsmtp-setup-wizard__header">
							<h2 className="qsmtp-setup-wizard__header-title">
								{__(
									'Finally, configure your sender settings',
									'quillsmtp'
								)}
							</h2>
						</div>
						<>
							<FromEmail connectionId={connectionId} />
							<ForceFromEmail connectionId={connectionId} />
							<FromName connectionId={connectionId} />
							<ForceFromName connectionId={connectionId} />
						</>
					</>
				)}

				{step === 5 && (
					<>
						<div className="qsmtp-setup-wizard__header">
							<h2 className="qsmtp-setup-wizard__header-title">
								{__('All set!', 'quillsmtp')}
							</h2>
							<p>
								{' '}
								You have successfully configured your
								connection.{' '}
							</p>
						</div>
						<Button
							className="qsmtp-setup-wizard__dashboard-button"
							variant="contained"
							color="primary"
							onClick={() => {
								setSetUpWizard(false);
								removeAllTempConnections();
							}}
						>
							{__('Go to Dashboard', 'quillsmtp')}
						</Button>
					</>
				)}
				{step !== 5 && (
					<div className="qsmtp-setup-wizard__buttons">
						<Button
							className="qsmtp-setup-wizard__prev-button"
							variant="contained"
							color="primary"
							onClick={() => {
								if (step > 1) {
									setStep(step - 1);
									setIsSaving(false);
								}

								if ('phpmailer' === mailerSlug && step === 4) {
									setStep(2);
								}
							}}
						>
							{__('Previous', 'quillsmtp')}
						</Button>
						<Button
							className={classNames(
								'qsmtp-setup-wizard__next-button',
								{
									'qsmtp-setup-wizard__next-button--disabled':
										!showNextButton,
								}
							)}
							variant="contained"
							color="primary"
							onClick={() => {
								if (showNextButton === false || isSaving)
									return;
								if (step === 4) {
									save();
									return;
								}
								if (step < 4) {
									setStep(step + 1);
								}
							}}
						>
							{step === 4
								? __('Save', 'quillsmtp')
								: __('Next', 'quillsmtp')}
						</Button>
					</div>
				)}
			</div>

			<Notices />
		</>
	);
};

export default WizardContent;
