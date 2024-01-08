export type Log = {
	log_id: number;
	subject: string;
	source: string;
	datetime: string;
	local_datetime: string;
	body: string;
	headers: {
		[key: string]: string;
	};
	from: string;
	recipients: {
		to: string;
		cc: string;
		bcc: string;
		reply_to: string;
	};
	status: 'succeeded' | 'failed';
	provider: string;
	provider_name: string;
	connection_id: string;
	connection_name: string;
	account_id: string;
	account_name: string;
	souce: {
		name: string;
		slug: string;
		type: string;
	};
	resend_count?: number;
	context: {
		[key: string]: string;
	};
	attachments: string[];
	response: string;
};
