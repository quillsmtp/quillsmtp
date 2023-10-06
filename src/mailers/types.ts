import React from 'react';

export type MailerModuleSettings = {
	render:
		| React.FC<{ connectionId: string; onClose: () => null }>
		| JSX.Element
		| React.Component;
	title: string;
	description: string;
	icon: string;
};

export type MailerModules = Record<string, MailerModuleSettings>;
