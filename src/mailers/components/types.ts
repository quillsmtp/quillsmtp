export type SetupFields = {
	[key: string]: {
		label: string;
		type: 'text' | 'password' | 'select';
		check: boolean;
		help?: string | React.FC;
		options?: {
			label: string;
			value: string;
		}[];
	};
};

export type Setup = {
	Instructions: React.FC;
	fields: SetupFields;
};

export type AccountsAuthFields = {
	[key: string]: AccountsAuthField;
};

export type AccountsAuthField = {
	label: string;
	type: 'text' | 'select' | 'toggle' | 'number' | 'password';
	required?: boolean;
	help?: string | React.FC;
	options?: {
		label: string;
		value: string;
	}[];
	dependencies?: {
		type?: 'or' | 'and';
		conditions: {
			field: string;
			value: string | number | boolean;
			operator: '==' | '!=' | '>' | '<' | '>=' | '<=';
		}[];
	};
	default?: string | number | boolean;
};

export type AccountsLabels = {
	singular: string;
	plural: string;
};

export type AccountsAuth = {
	type: 'credentials' | 'oauth';
	fields?: AccountsAuthFields;
	Instructions?: React.FC;
};

export type ConnectMainAccounts = {
	auth: AccountsAuth;
	labels?: AccountsLabels;
};

export type ConnectMain = {
	accounts: ConnectMainAccounts;
};
