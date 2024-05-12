import React from 'react';

export type MailerModuleSettings = {
	connectParameters?: Record<string, any>;
	title: string;
	description: string;
	icon: string;
	is_pro?: boolean;
	account_settings?:
		| React.FC<{ connectionId: string }>
		| JSX.Element
		| React.Component;
};

export type MailerModules = Record<string, MailerModuleSettings>;
