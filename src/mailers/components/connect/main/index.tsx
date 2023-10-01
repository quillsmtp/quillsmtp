/**
 * Internal dependencies
 */
import { ConnectMain } from '../../types';

interface Props {
	main: ConnectMain;
	close: () => void;
}

const Main: React.FC<Props> = ({ main, close }) => {
	console.log('main', main);
	console.log('close', close);

	return (
		<div>
			<h1>Mail</h1>
		</div>
	);
};

export default Main;
