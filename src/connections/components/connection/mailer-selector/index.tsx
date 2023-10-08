/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';

/**
 * External dependencies
 */
import { map, keys, size } from 'lodash';
import Card from '@mui/material/Card';
import CardActions from '@mui/material/CardActions';
import CardContent from '@mui/material/CardContent';
import Grid from '@mui/material/Grid';
import Button from '@mui/material/Button';
import { CardActionArea } from '@mui/material';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import { getMailerModules } from '@quillsmtp/mailers';
import MailerModal from './mailer-modal';
import './style.scss';

interface Props {
	connectionId: string;
}
// @ts-ignore
const MailersSelector: React.FC<Props> = ({ connectionId }) => {
	const [modalMailer, setModalMailer] = useState(null);
	const mailerModules = getMailerModules();
	const { setCurrentMailerProvider } = useDispatch('quillSMTP/core');

	return (
		<>
			<Grid className="qsmtp-mailers-selector" container spacing={2}>
				{size(mailerModules) > 0 &&
					map(keys(mailerModules), (key) => {
						const mailer = mailerModules[key];
						return (
							<Grid item xs={12} sm={6} md={4} lg={3} key={key}>
								<Card
									className={classnames({
										'qsmtp-mailer-selector__card': true,
										'qsmtp-mailer-selector__card--active':
											modalMailer === key,
									})}
								>
									<CardActionArea
										onClick={() => {
											setModalMailer(key);
											setCurrentMailerProvider({
												slug: key,
												title: mailer.title,
											});
										}}
									>
										<CardContent>
											<div className="qsmtp-mailer-selector__card__title">
												<div className="qsmtp-mailer-selector__card__title__icon">
													<img src={mailer.icon} />
												</div>
												<div className="qsmtp-mailer-selector__card__title__text">
													<h3>{mailer.title}</h3>
												</div>
											</div>
											<p>{mailer.description}</p>
										</CardContent>
										<CardActions>
											<Button
												onClick={() => {
													setModalMailer(key);
													setCurrentMailerProvider({
														slug: key,
														title: mailer.title,
													});
												}}
												variant="contained"
											>
												{__('Configure', 'quillsmtp')}
											</Button>
										</CardActions>
									</CardActionArea>
								</Card>
							</Grid>
						);
					})}
			</Grid>
			{modalMailer && (
				<MailerModal
					mailer={mailerModules[modalMailer]}
					open={modalMailer !== null}
					onClose={() => {
						setModalMailer(null);
					}}
				/>
			)}
		</>
	);
};

export default MailersSelector;
