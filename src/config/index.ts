/* eslint-disable jsdoc/check-line-alignment */
import type { ConfigData, License } from './types/config-data';
import type { StoreMailers } from './types/store-mailers';
import { InitialPayload } from './types/initial-payload';

const configData: ConfigData = {
	initialPayload: {
		global_network_settings: true,
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
	isMultisite: false,
	isMainSite: false,
	license: false,
	wpMailConfig: false,
	easySMTPConfig: false,
	fluentSMTPConfig: false,
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

/**
 * Set is multisite
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {boolean} isMultisite
 */
const setIsMultisite = (data: ConfigData) => (value: boolean) => {
	data.isMultisite = value;
};

/**
 * Get is multisite
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {boolean} isMultisite
 */
const getIsMultisite = (data: ConfigData) => (): boolean => {
	// Return boolean value
	return data.isMultisite == true;
};

/**
 * Set is main site
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {boolean} isMainSite
 */
const setIsMainSite = (data: ConfigData) => (value: boolean) => {
	data.isMainSite = value;
};

/**
 * Get is main site
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {boolean} isMainSite
 */
const getIsMainSite = (data: ConfigData) => (): boolean => {
	// Return boolean value
	return data.isMainSite == true;
};

// license
/**
 * Set license
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {License | false} license
 */
const setLicense = (data: ConfigData) => (value: License | false) => {
	data.license = value;
};

/**
 * Get license
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {License | false} license
 */
const getLicense = (data: ConfigData) => (): License | false => {
	return data.license;
};

/**
 * Set wp mail config
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {any} wpMailConfig
 */
const setWpMailConfig = (data: ConfigData) => (value: any) => {
	data.wpMailConfig = value;
};

/**
 * Get wp mail config
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {any} wpMailConfig
 */
const getWpMailConfig = (data: ConfigData) => (): any => {
	return data.wpMailConfig;
};

/**
 * Get easy mail smtp
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {any} easySMTP
 */
const getEasySMTPConfig = (data: ConfigData) => (): any => {
	return data.easySMTPConfig;
};

/**
 * Set easy mail smtp
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {any} easySMTP
 */
const setEasySMTPConfig = (data: ConfigData) => (value: any) => {
	data.easySMTPConfig = value;
};

/**
 * Set fluent mail smtp
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {any} fluentSMTP
 */
const setFluentSMTPConfig = (data: ConfigData) => (value: any) => {
	data.fluentSMTPConfig = value;
};

/**
 * Get fluent mail smtp
 *
 * @param data the json environment configuration to use for getting config values
 *
 * @returns {any} fluentSMTP
 */
const getFluentSMTPConfig = (data: ConfigData) => (): any => {
	return data.fluentSMTPConfig;
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
	setIsMultisite: (value: boolean) => void;
	getIsMultisite: () => boolean;
	setIsMainSite: (value: boolean) => void;
	getIsMainSite: () => boolean;
	setLicense: (value: License | false) => void;
	getLicense: () => License | false;
	setWpMailConfig: (value: any) => void;
	getWpMailConfig: () => any;
	setEasySMTPConfig: (value: any) => void;
	getEasySMTPConfig: () => any;
	setFluentSMTPConfig: (value: any) => void;
	getFluentSMTPConfig: () => any;
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
	configApi.getIsMultisite = getIsMultisite(data);
	configApi.setIsMultisite = setIsMultisite(data);
	configApi.getIsMainSite = getIsMainSite(data);
	configApi.setIsMainSite = setIsMainSite(data);
	configApi.getLicense = getLicense(data);
	configApi.setLicense = setLicense(data);
	configApi.setWpMailConfig = setWpMailConfig(data);
	configApi.getWpMailConfig = getWpMailConfig(data);
	configApi.setEasySMTPConfig = setEasySMTPConfig(data);
	configApi.getEasySMTPConfig = getEasySMTPConfig(data);
	configApi.setFluentSMTPConfig = setFluentSMTPConfig(data);
	configApi.getFluentSMTPConfig = getFluentSMTPConfig(data);

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
