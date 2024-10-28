/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';
import { getMailerModules } from '@quillsmtp/mailers';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { select, useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { useState, createPortal } from '@wordpress/element';

/**
 * External Dependencies
 */

import EditIcon from '@mui/icons-material/Edit';

/**
 * Internal Dependencies
 */
import './style.scss';
import SetUpWizard from '../setupwizard';


interface Props {
    connectionId: string;
    index: number;
}

const ConnectionCard: React.FC<Props> = ({ connectionId, index }) => {
    const [isDeleting, setIsDeleting] = useState(false);
    const [setUpWizard, setSetUpWizard] = useState(false);
    const mailerModules = getMailerModules();
    const { mailerSlug, name, connection } = useSelect((select) => {
        return {
            connection: select('quillSMTP/core').getConnection(connectionId),
            mailerSlug: select('quillSMTP/core').getConnectionMailer(connectionId),
            name: select('quillSMTP/core').getConnectionName(connectionId),
        };
    });


    const { addConnection, deleteConnection } =
        useDispatch('quillSMTP/core');

    // dispatch notices.
    const { createNotice } = useDispatch('quillSMTP/core');

    const remove = () => {
        // First check if this is connection in stored in the initial payload.
        // If so, we need to remove it from the initial payload.
        const initialPayload = ConfigAPI.getInitialPayload();
        if (!initialPayload.connections[connectionId]) {
            deleteConnection(connectionId);
            return;
        }

        const newConnections = { ...initialPayload.connections };
        delete newConnections[connectionId];
        setIsDeleting(true);
        apiFetch({
            path: `/qsmtp/v1/settings`,
            method: 'POST',
            data: {
                connections: newConnections,
            },
        }).then((res: any) => {
            if (res.success) {
                ConfigAPI.setInitialPayload({
                    ...ConfigAPI.getInitialPayload(),
                    connections: newConnections,
                });
                deleteConnection(connectionId);
                createNotice({
                    type: 'success',
                    message: __('Settings saved successfully.', 'quillsmtp'),
                });
            } else {
                createNotice({
                    type: 'error',
                    message: __('Error saving settings.', 'quillsmtp'),
                });
            }

            setIsDeleting(false);
        });
    };


    return (
        <div
            className="qsmtp-connection-card"
            data-label="Default Connection"
        >
            {mailerSlug && mailerModules[mailerSlug] && mailerModules[mailerSlug].icon && <img src={mailerModules[mailerSlug].icon} alt={mailerSlug} className='qsmtp-connection-card__icon' />}
            <div className='qsmtp-connection-card__edit-icon' onClick={() => {
                addConnection(connectionId, connection, false);
                setSetUpWizard(true);
            }}>
                <EditIcon />
                {__('Edit', 'quillsmtp')}
            </div>
            <div className="qsmtp-connection-card__connection-name">{name}</div>
            {
                setUpWizard && createPortal(
                    <SetUpWizard
                        mode="edit"
                        connectionId={connectionId}
                        setSetUpWizard={setSetUpWizard}
                    />,
                    document.body
                )
            }
        </div >
    );
};

export default ConnectionCard;
