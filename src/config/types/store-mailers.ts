export type StoreMailer = {
	name: string;
	description: string;
	version: string;
	plan: string;
	assets: {
		icon: string;
		banner: string;
	};
	is_pro?: boolean;
	documentation: string | false;
};

export type StoreMailers = {
	[mailerSlug: string]: StoreMailer;
};
