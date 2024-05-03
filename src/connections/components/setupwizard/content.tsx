import { TextField } from '@mui/material';
import { useEffect, useState } from '@wordpress/element';
import { useDispatch, useSelect } from "@wordpress/data";
import { Icon } from '@wordpress/components';
import { check } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { motion } from "framer-motion"
import Button from '@mui/material/Button';
import "./style.scss";
import MailersSelector from '../connection/mailer-selector';
import MailerAccounts from '../connection/mailer-accounts';
import { getMailerModules } from '@quillsmtp/mailers';
import classNames from "classnames";

const WizardContent = ({ connectionId }) => {
    const [step, setStep] = useState(1);
    const [showNextButton, setShowNextButton] = useState(false);

    console.log(connectionId);


    const mailerModules = getMailerModules();
    const { mailerSlug, connectionName } = useSelect((select) => {
        return {
            mailerSlug: select('quillSMTP/core').getConnectionMailer(connectionId),
            connectionName: select('quillSMTP/core').getConnectionName(connectionId),
        }
    });

    useEffect(() => {

        if (step === 1 && connectionName) {
            setShowNextButton(true);
            return;
        }
        if (step === 2 && mailerSlug) {
            setShowNextButton(true);
            return;
        }
        if (step === 3) {
            setShowNextButton(true);
            return;
        }
        setShowNextButton(false);
    }, [step, connectionName, mailerSlug]);

    const { updateConnection } = useDispatch('quillSMTP/core');


    return (
        <>
            <div className="qsmtp-setup-wizard__sidebar">
                <div className="qsmtp-setup-wizard__sidebar-logo"><img width="40" src={qsmtpAdmin?.assetsBuildUrl + "assets/logo.svg"} alt="logo" /> </div>

                <div className="qsmtp-setup-wizard__sidebar-steps">


                    {[1, 2, 3, 4].map((s) => (
                        <div className={`qsmtp-setup-wizard__sidebar-step-wrapper`} key={s}>
                            <div className='qsmtp-setup-wizard__sidebar-step-line'></div>

                            <div className={`qsmtp-setup-wizard__sidebar-step ${s === step ? 'qsmtp-setup-wizard__sidebar-step--active' : ''} 
                             ${s < step ? 'qsmtp-setup-wizard__sidebar-step--checked' : ""}
                            `}

                                key={s}>
                                <div className='qsmtp-setup-wizard__sidebar-step-number'>
                                    {s < step ? <Icon icon={check} /> : s}
                                </div>
                            </div>
                        </div>
                    ))}



                </div>

            </div>
            <div className='qsmtp-setup-wizard__content'>

                {step === 1 && (
                    <>
                        <div className="qsmtp-setup-wizard__header">
                            <h2 className='qsmtp-setup-wizard__header-title'>{__('Let\'s start with the connection name', 'quillsmtp')}</h2>
                            <p>The connection name is used to identify the connection in the connection list.</p>
                        </div>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ duration: 0.5 }}
                            className="qsmtp-setup-wizard__first-step"
                        >
                            <TextField
                                autoComplete='new-password'
                                label={__('Connection Name', 'quillsmtp')}
                                value={connectionName}
                                onChange={(e) => {

                                    updateConnection(connectionId, {
                                        name: e.target.value
                                    });
                                }
                                }
                                variant="outlined"
                                color='primary'
                                fullWidth
                                sx={{ mb: 2 }}
                            />
                        </motion.div>
                    </>
                )}
                {step === 2 && (
                    <>
                        <div className="qsmtp-setup-wizard__header">
                            <h2 className='qsmtp-setup-wizard__header-title'>{__('Please select your mail provider', 'quillsmtp')}</h2>
                            <p> Select the mail provider you want to connect to. If you don't see your provider, please select the "Other" option. </p>
                        </div>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ duration: 0.5 }}
                            className="qsmtp-setup-wizard__second-step"
                        >
                            <MailersSelector
                                connectionId={connectionId}
                            />
                        </motion.div>
                    </>
                )}

                {step == 3 && (
                    <>
                        <div className="qsmtp-setup-wizard__header">
                            <h2 className='qsmtp-setup-wizard__header-title'>{__('Let\'s configure your mail provider account settings', 'quillsmtp')}</h2>
                            <p> Configure your mail provider account settings to connect to your mail provider. </p>
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

                <div className="qsmtp-setup-wizard__buttons">
                    <Button
                        className='qsmtp-setup-wizard__prev-button'
                        variant="contained"
                        color="primary"
                        onClick={() => {
                            if (step > 1) {
                                setStep(step - 1)
                            }
                        }}
                    >
                        {__('Previous', 'quillsmtp')}
                    </Button>
                    <Button
                        className={classNames('qsmtp-setup-wizard__next-button')}
                        variant="contained"
                        color="primary"
                        onClick={() => {
                            if (showNextButton === false) return;
                            if (step < 4) {
                                setStep(step + 1)
                            }
                        }}
                    >
                        {__('Next', 'quillsmtp')}
                    </Button>
                </div>
            </div>
        </>
    )
}

export default WizardContent;