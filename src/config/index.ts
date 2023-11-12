/* eslint-disable jsdoc/check-line-alignment */
import type { ConfigData } from './types/config-data';
import type { StoreMailers } from './types/store-mailers';
import { InitialPayload } from './types/initial-payload';

const configData: ConfigData = {
	initialPayload: {
		default_connection: '',
		fallback_connection: '',
		mailers: {},
	},
	storeMailers: {},
	adminUrl: '',
	pluginDirUrl: '',
	adminEmail: '',
	ajaxUrl: '',
	nonce: '',
};

/**
 * Returns configuration value for given key
 *
 * If the requested key isn't defined in the configuration
 * data then this will report the failure with either an
 * error or a console warning.

 * @param {ConfigData} data Configurat data.
 * @returns A function that gets the value of property named by the key
 */
const config =
	(data: ConfigData) =>
	<T>(key: string): T | undefined => {
		if (key in data) {
			return data[key] as T;
		}
		return undefined;
	};

/**
 * set initial builder payload
 *
 * @param data the json environment configuration to use for getting config values
 */
const setInitialPayload = (data: ConfigData) => (value: InitialPayload) => {
	data.initialPayload = value;
};

/**
 * Get initial builder payload
 *
 * @param data the json environment configuration to use for getting config values
 */
const getInitialPayload = (data: ConfigData) => (): InitialPayload => {
	return data.initialPayload;
};

/**
 * set store mailers
 *
 * @param data the json environment configuration to use for getting config values
 */
const setStoreMailers = (data: ConfigData) => (value: StoreMailers) => {
	data.storeMailers = value;
};

/**
 * Get store mailers
 *
 * @param data the json environment configuration to use for getting config values
 */
const getStoreMailers = (data: ConfigData) => (): StoreMailers => {
	return data.storeMailers;
};

/**
 * Get admin url
 *
 * @param data the json environment configuration to use for getting config values
 */
const getAdminUrl = (data: ConfigData) => (): string => {
	return data.adminUrl;
};

/**
 * Set admin url
 *
 * @param data the json environment configuration to use for getting config values
 */
const setAdminUrl = (data: ConfigData) => (value: string) => {
	data.adminUrl = value;
};

/**
 * Get admin email
 *
 * @param data the json environment configuration to use for getting config values
 */
const getAdminEmail = (data: ConfigData) => (): string => {
	return data.adminEmail;
};

/**
 * Set admin email
 *
 * @param data the json environment configuration to use for getting config values
 */
const setAdminEmail = (data: ConfigData) => (value: string) => {
	data.adminEmail = value;
};

/**
 * Get ajax url
 *
 * @param data the json environment configuration to use for getting config values
 */
const getAjaxUrl = (data: ConfigData) => (): string => {
	return data.ajaxUrl;
};

/**
 * Set ajax url
 *
 * @param data the json environment configuration to use for getting config values
 */
const setAjaxUrl = (data: ConfigData) => (value: string) => {
	data.ajaxUrl = value;
};

/**
 * Get nonce
 *
 * @param data the json environment configuration to use for getting config values
 */
const getNonce = (data: ConfigData) => (): string => {
	return data.nonce;
};

/**
 * Set nonce
 *
 * @param data the json environment configuration to use for getting config values
 */
const setNonce = (data: ConfigData) => (value: string) => {
	data.nonce = value;
};

/**
 * Get plugin dir url
 *
 * @param data the json environment configuration to use for getting config values
 */
const getPluginDirUrl = (data: ConfigData) => (): string => {
	return data.pluginDirUrl;
};

/**
 * Set plugin dir url
 *
 * @param data the json environment configuration to use for getting config values
 */
const setPluginDirUrl = (data: ConfigData) => (value: string) => {
	data.pluginDirUrl = value;
};

export interface ConfigApi {
	<T>(key: string): T;
	setInitialPayload: (value: InitialPayload) => void;
	getInitialPayload: () => InitialPayload;
	setStoreMailers: (value: StoreMailers) => void;
	getStoreMailers: () => StoreMailers;
	setAdminUrl: (value: string) => void;
	getAdminUrl: () => string;
	setAdminEmail: (value: string) => void;
	getAdminEmail: () => string;
	setAjaxUrl: (value: string) => void;
	getAjaxUrl: () => string;
	setNonce: (value: string) => void;
	getNonce: () => string;
	setPluginDirUrl: (value: string) => void;
	getPluginDirUrl: () => string;
}

const createConfig = (data: ConfigData): ConfigApi => {
	const configApi = config(data) as ConfigApi;
	configApi.setInitialPayload = setInitialPayload(data);
	configApi.getInitialPayload = getInitialPayload(data);
	configApi.setStoreMailers = setStoreMailers(data);
	configApi.getStoreMailers = getStoreMailers(data);
	configApi.getAdminUrl = getAdminUrl(data);
	configApi.setAdminUrl = setAdminUrl(data);
	configApi.getAdminEmail = getAdminEmail(data);
	configApi.setAdminEmail = setAdminEmail(data);
	configApi.getAjaxUrl = getAjaxUrl(data);
	configApi.setAjaxUrl = setAjaxUrl(data);
	configApi.getNonce = getNonce(data);
	configApi.setNonce = setNonce(data);
	configApi.getPluginDirUrl = getPluginDirUrl(data);
	configApi.setPluginDirUrl = setPluginDirUrl(data);

	return configApi;
};

const ConfigAPI = createConfig(configData);

// @ts-ignore
if (window.qsmtp === undefined) {
	// @ts-ignore
	window.qsmtp = {
		config: ConfigAPI,
	};
}

// @ts-ignore
export default window.qsmtp.config as ConfigApi;
export * from './types/initial-payload';
