export type InitialPayload = {
	mailers?: {
		[x: string]: any;
	};
	// Any other rest field
	[x: string]: any;
};
