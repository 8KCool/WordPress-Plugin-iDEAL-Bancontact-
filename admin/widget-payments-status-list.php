<div class="pronamic-pay-status-widget">
	<ul class="pronamic-pay-status-list">

		<?php foreach ( $states as $status => $label ) : ?>

			<li class="<?php echo esc_attr( 'payment_status-' . $status ); ?>">
				<a href="<?php echo esc_attr( add_query_arg( 'post_status', $status, $url ) ); ?>">
					<?php

					$count = isset( $counts->$status ) ? $counts->$status : 0;

					printf( //xss ok
						$label,
						'<strong>' . sprintf(
							esc_html( _n( '%s payment', '%s payments', $count, 'pronamic_ideal' ) ),
							esc_html( number_format_i18n( $count ) )
						) . '</strong>'
					);

					?>
				</a>
			</li>

		<?php endforeach; ?>

	</ul>
</div>
