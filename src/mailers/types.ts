import React from 'react';

export type MailerModuleSettings = {
	connectParameters?: Record<string, any>;
	title: string;
	description: string;
	icon: string;
	account_settings?:
		| React.FC<{ connectionId: string }>
		| JSX.Element
		| React.Component;
	documentation: string | false;
};

export type MailerModules = Record<string, MailerModuleSettings>;
