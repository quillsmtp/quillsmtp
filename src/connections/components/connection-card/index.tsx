/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';
import { getMailerModules } from '@quillsmtp/mailers';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { select, useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { useState, createPortal } from '@wordpress/element';

/**
 * External Dependencies
 */

import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import IconButton from '@mui/material/IconButton';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import Button from '@mui/material/Button';
import LoadingButton from '@mui/lab/LoadingButton';

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
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
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
            <div className="qsmtp-connection-card__actions">
                <div className='qsmtp-connection-card__edit-icon' onClick={() => {
                    addConnection(connectionId, connection, false);
                    setSetUpWizard(true);
                }}>
                    <EditIcon />
                    {__('Edit', 'quillsmtp')}
                </div>
                <div
                    className='qsmtp-connection-card__delete-icon'
                    onClick={(e) => {
                        e.stopPropagation();
                        setShowDeleteConfirm(true);
                    }}
                >
                    <DeleteIcon />
                    {__('Delete', 'quillsmtp')}
                </div>
            </div>
            <div className="qsmtp-connection-card__connection-name">{name}</div>
            {showDeleteConfirm && (
                <Dialog
                    open={showDeleteConfirm}
                    onClose={() => {
                        if (!isDeleting) setShowDeleteConfirm(false);
                    }}
                    aria-labelledby="delete-connection-dialog-title"
                    aria-describedby="delete-connection-dialog-description"
                >
                    <DialogTitle id="delete-connection-dialog-title">
                        {__('Delete Connection', 'quillsmtp')}
                    </DialogTitle>
                    <DialogContent>
                        <DialogContentText id="delete-connection-dialog-description">
                            {sprintf(
                                __(
                                    'Are you sure you want to delete the connection "%s"?',
                                    'quillsmtp'
                                ),
                                name
                            )}
                        </DialogContentText>
                    </DialogContent>
                    <DialogActions>
                        <Button
                            onClick={() => {
                                if (!isDeleting) setShowDeleteConfirm(false);
                            }}
                            disabled={isDeleting}
                        >
                            {__('Cancel', 'quillsmtp')}
                        </Button>
                        <LoadingButton
                            onClick={() => {
                                remove();
                                setShowDeleteConfirm(false);
                            }}
                            color="error"
                            startIcon={<DeleteIcon />}
                            loading={isDeleting}
                        >
                            {__('Delete', 'quillsmtp')}
                        </LoadingButton>
                    </DialogActions>
                </Dialog>
            )}
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
