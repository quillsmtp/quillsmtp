export type Log = {
	log_id: number;
	level: string;
	message: string;
	source: string;
	datetime: string;
	local_datetime: string;
	context: {
		code: string;
		connection_id: string;
		connection_name: string;
		provider: string;
		response: string;
		email_details: {
			from: string;
			to: string;
			cc: string;
			bcc: string;
			reply_to: string;
			subject: string;
			headers: {
				[key: string]: string;
			};
			plain: string;
			html: string;
			attachments: string[];
		};
		resend_count?: number;
	};
};
