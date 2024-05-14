/**
 * WordPress dependencies
 */
import { useState, createPortal } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import Button from '@mui/material/Button';
import { size } from 'lodash';

/**
 * Internal Dependencies
 */
import SetUpWizard from '../../../connections/components/setupwizard';
import "./style.scss";

const WelcomePage = () => {

    const [setUpwizard, setSetUpWizard] = useState(false);
    const [newConnectionId, setNewConnectionId] = useState('');

    const { addConnection } = useDispatch('quillSMTP/core')
    return (
        <>
            <div className="qsmtp-welcome-page qsmtp-card">
                <h2> Welcome to Quill SMTP</h2>
                <p> Quill SMTP is a powerful SMTP plugin that allows you to send emails using your favorite SMTP server.</p>
                <Button
                    variant="contained"
                    color="primary"
                    size='large'
                    onClick={() => {
                        const randomId = () =>
                            Math.random().toString(36).substr(2, 9);

                        const connectionId = randomId();
                        setNewConnectionId(connectionId);
                        addConnection(connectionId, {
                            name: sprintf(
                                __('Connection 1', 'quillsmtp')
                            ),
                            mailer: '',
                            account_id: '',
                            from_email: '',
                            force_from_email: false,
                            from_name: '',
                            force_from_name: false,
                        });

                        setTimeout(() => {
                            setSetUpWizard(true);
                        }, 100);
                    }}>
                    Create Your Default Connection
                </Button>

            </div>
            {setUpwizard && createPortal(
                <SetUpWizard
                    mode="edit"
                    connectionId={newConnectionId}
                    setSetUpWizard={setSetUpWizard}
                />,
                document.body
            )}
        </>
    )
}
export default WelcomePage;