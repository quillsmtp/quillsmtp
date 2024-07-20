import { useEffect } from '@wordpress/element';
import { Icon } from '@wordpress/components';
import { close } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { motion } from "framer-motion"
import "./style.scss";
import Particles from '../particles';
import WizardContent from './content';
import { useDispatch } from '@wordpress/data';

const SetUpWizard = ({ setSetUpWizard, connectionId, mode }) => {
    const { removeAllTempConnections } = useDispatch('quillSMTP/core');

    useEffect(() => {
        // add class to the body when this component mounts and remove it when the component unmounts
        document.body.classList.add('qsmtp-setup-wizard-active');

        return () => {
            document.body.classList.remove('qsmtp-setup-wizard-active');
        };
    }, []);

    return (
        <motion.div
            className='qsmtp-setup-wizard'
            initial={{ transform: 'scale(0.8)', opacity: '0', zIndex: -1 }}
            animate={{
                zIndex: 1111111111, opacity: '1', transform: 'scale(1)'
            }}
            transition={{ duration: 0.3, type: 'tween' }}
        >
            <div className='qsmtp-setup-wizard__close'
                onClick={() => {

                    setSetUpWizard(false);
                    // if (mode === 'add') {
                    //     deleteConnection(connectionId);
                    // }
                    removeAllTempConnections();
                }
                }

            >
                <Icon icon={close} />
            </div>
            <WizardContent mode={mode} setSetUpWizard={setSetUpWizard} connectionId={connectionId} />
            <div className="qsmtp-setup-wizard__right-blank-area">
                <Particles
                /></div>

        </motion.div>
    )
}

export default SetUpWizard;