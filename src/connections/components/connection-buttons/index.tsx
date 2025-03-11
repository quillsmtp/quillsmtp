/**
 * QuillSMTP Dependencies
 */
import ConfigAPI from '@quillsmtp/config';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { useState, createPortal } from '@wordpress/element';

/**
 * External Dependencies
 */

import EditIcon from '@mui/icons-material/Edit';
import { RiDeleteBinLine } from "react-icons/ri";


/**
 * Internal Dependencies
 */
import './style.scss';
import SetUpWizard from '../setupwizard';
import IconButton from '@mui/material/IconButton';


interface Props {
    connectionId: string;
}

const ConnectionButtons: React.FC<Props> = ({ connectionId }) => {
    const [isDeleting, setIsDeleting] = useState(false);
    const [setUpWizard, setSetUpWizard] = useState(false);
    const { connection } = useSelect((select) => {
        return {
            connection: select('quillSMTP/core').getConnection(connectionId),
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
        <div className='flex gap-2'>
                <IconButton sx={{ size: "small", padding: "0px" }} onClick={() => {
                    deleteConnection(connectionId);
                    setSetUpWizard(true);
                }}>
                    <RiDeleteBinLine className='rounded-full border border-[#E52747] border-opacity-20 !text-[#E52747] p-1 hover:!bg-[#E52747] hover:!text-white' />
                </IconButton>
                <IconButton sx={{ padding: "0px" }} size="small" onClick={() => {
                    addConnection(connectionId, connection, false);
                    setSetUpWizard(true);
                }}>
                    <EditIcon color="primary" className='rounded-full border border-[##E5E5E5] text-[#333333] p-1 hover:bg-[#333333] hover:text-white' />
                </IconButton>
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

export default ConnectionButtons;
