export type SetupFields = {
	[key: string]: {
		label: string;
		type: 'text';
		check: boolean;
	};
};

export type Setup = {
	Instructions: React.FC;
	fields: SetupFields;
};

export type AccountsAuthFields = {
	[key: string]: {
		label: string;
		type: 'text';
		required?: boolean;
	};
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
