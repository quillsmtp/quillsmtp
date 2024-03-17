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
