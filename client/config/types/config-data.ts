import type { InitialPayload } from './initial-payload';
import { StoreMailers } from './store-mailers';

export type ConfigData = Record<string, unknown> & {
	initialPayload: InitialPayload;
	storeMailers: StoreMailers;
	adminUrl: string;
	pluginDirUrl: string;
};
