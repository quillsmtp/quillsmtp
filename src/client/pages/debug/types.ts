export type Log = {
	log_id: number;
	level: string;
	message: string;
	source: string;
	datetime: string;
	local_datetime: string;
	context: {
		[key: string]: string;
	};
};
