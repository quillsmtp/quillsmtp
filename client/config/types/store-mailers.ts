export type StoreMailer = {
	name: string;
	description: string;
	version: string;
	plan: string;
	assets: {
		icon: string;
		banner: string;
	};
};

export type StoreMailers = {
	[mailerSlug: string]: StoreMailer;
};
