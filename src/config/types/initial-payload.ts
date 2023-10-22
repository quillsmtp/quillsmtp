export type InitialPayload = {
	default_connection: string;
	fallback_connection: string;
	mailers: {
		[x: string]: any;
	};
	// Any other rest field
	[x: string]: any;
};
