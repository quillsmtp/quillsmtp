import type { InitialPayload } from './initial-payload';
import { StoreMailers } from './store-mailers';

export type ConfigData = Record<string, unknown> & {
	initialPayload: InitialPayload;
	storeMailers: StoreMailers;
	adminUrl: string;
	pluginDirUrl: string;
	adminEmail: string;
	ajaxUrl: string;
	nonce: string;
	isMultisite: boolean;
	isMainSite: boolean;
	license: License | false;
	wpMailConfig: SMTP_Config | false;
	easySMTPConfig: SMTP_Config | false;
	fluentSMTPConfig: SMTP_Config | false;
	proPluginData: ProPluginData;
};

export type ProPluginData = {
	is_installed: boolean;
	is_active: boolean;
};

export type SMTP_Config = {
	from_email: string;
	from_name: string;
	from_name_force: boolean;
	from_email_force: boolean;
	mailer: string;
	[key: string]: any;
};

export type License = {
	upgrades: {
		[key: string]: Upgrade;
	};
	[key: string]: any;
};

export type Upgrade = {
	[key: string]: any;
};
