import React from 'react';

export type MailerModuleSettings = {
	render:
		| React.FC<{ slug: string; connectionId: string }>
		| JSX.Element
		| React.Component;
	title: string;
	description: string;
	icon: string;
	is_pro?: boolean;
};

export type MailerModules = Record<string, MailerModuleSettings>;
