<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder columns-2">
			<div id="postbox-container-1" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">

					<?php if ( current_user_can( 'manage_options' ) ) : ?>

						<div class="postbox">
							<h2 class="hndle"><span><?php esc_html_e( 'Help', 'pronamic_ideal' ); ?></span></h2>

							<div class="inside">
								<p>
									<?php esc_html_e( 'Before asking for help we recommend to check the tour, getting started and status page.', 'pronamic_ideal' ); ?>
								</p>

								<?php

								printf(
									'<a href="%s" class="button-secondary">%s</a>',
									esc_attr(
										wp_nonce_url( add_query_arg( array(
											'page'                     => 'pronamic_ideal',
											'pronamic_pay_ignore_tour'  => '0',
										) ), 'pronamic_pay_ignore_tour', 'pronamic_pay_nonce' )
									),
									esc_html__( 'Start Tour', 'pronamic_ideal' )
								);

								echo ' ';

								printf(
									'<a href="%s" class="button-secondary">%s</a>',
									esc_attr(
										add_query_arg( array(
											'page' => 'pronamic-pay-about',
											'tab'  => 'getting-started',
										) )
									),
									esc_html__( 'Getting Started', 'pronamic_ideal' )
								);

								echo ' ';

								printf(
									'<a href="%s" class="button-secondary">%s</a>',
									esc_attr(
										add_query_arg( array(
											'page' => 'pronamic_pay_tools',
										) )
									),
									esc_html__( 'System Status', 'pronamic_ideal' )
								);

								?>
							</div>
						</div>

					<?php endif; ?>

					<div class="postbox">
						<h2 class="hndle"><span><?php esc_html_e( 'Pending Payments', 'pronamic_ideal' ); ?></span></h2>

						<div class="inside">
							<?php

							$payments = get_posts( array(
								'post_type'      => 'pronamic_payment',
								'post_status'    => 'payment_pending',
								'posts_per_page' => 5,
							) );

							if ( empty( $payments ) ) : ?>



							<?php else : ?>

								<div id="dashboard_recent_drafts">
									<ul>

										<?php foreach ( $payments as $payment ) : ?>

											<li>
												<h4>
													<?php

													printf(
														'<a href="%s">%s</a>',
														esc_attr( get_edit_post_link( $payment ) ),
														esc_html( get_the_title( $payment ) )
													);

													?>
													<?php

													printf( '<abbr title="%s">%s</abbr>',
														/* translators: comment date format. See http://php.net/date */
														esc_attr( get_the_time( __( 'c', 'pronamic_ideal' ), $payment ) ),
														esc_html( get_the_time( get_option( 'date_format' ), $payment ) )
													);

													?>
												</h4>
											</li>

										<?php endforeach; ?>

									</ul>
								</div>

							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php esc_html_e( 'Pronamic News', 'pronamic_ideal' ); ?></span></h2>

						<div class="inside">
							<?php

							wp_widget_rss_output( 'http://feeds.feedburner.com/pronamic', array(
								'link'  => __( 'http://www.pronamic.eu/', 'pronamic_ideal' ),
								'url'   => 'http://feeds.feedburner.com/pronamic',
								'title' => __( 'Pronamic News', 'pronamic_ideal' ),
								'items' => 5,
							) );

							?>
						</div>
					</div>
				</div>
			</div>

			<div class="clear"></div>
		</div>
	</div>

	<?php include 'pronamic.php'; ?>
</div>
