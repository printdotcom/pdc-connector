<div class="wp-list-table pdc-table widefat fixed striped posts">
	<div class="table-head">
		<div class="table-head-col">
			<strong>Information</strong>
		</div>
		<div class="table-head-col">
			<strong>Actions</strong>
		</div>
	</div>
	<div class="table-body">
		<?php
		$meta_key_pdf_url      = $this->get_meta_key( 'pdf_url' );
		$meta_key_preset_id    = $this->get_meta_key( 'preset_id' );
		$meta_key_preset_title = $this->get_meta_key( 'preset_title' );

		foreach ( $order->get_items() as $order_item_product ) {
			$order_item_id              = $order_item_product->get_id();
			$pdc_order_item             = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'order_item' ), true );
			$pdc_order_item_number      = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'order_item_number' ), true );
			$pdc_order_item_grand_total = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'order_item_grand_total' ), true );
			$pdc_purchase_date          = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'purchase_date' ), true );
			$pdc_image_url              = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'image_url' ), true );
			$pdc_pdf_url                = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'pdf_url' ), true );
			$pdc_order_item_status      = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'order_item_status' ), true );
			$pdc_tnt_url                = wc_get_order_item_meta( $order_item_id, $this->get_meta_key( 'order_item_tnt_url' ), true );

			$product         = wc_get_product( $order_item_product->get_product_id() );
			$pdc_preset_id   = $product->get_meta( $meta_key_preset_id );
			$pdc_preset_name = $product->get_meta( $meta_key_preset_title );

			$has_file   = $pdc_pdf_url ? true : false;
			$has_preset = $pdc_preset_id ? true : false;
			$filename   = basename( $pdc_pdf_url );
			?>
			<div class="table-row" id="pdc_order_item_<?php echo $order_item_id; ?>">
				<div class="table-row-contents" id="pdc_order_item_<?php echo $order_item_id; ?>_inner">
					<div class="table-cell">
						<?php if ( $pdc_order_item_number ) { ?>
							<span><strong>Order item number</strong> #<?php echo $pdc_order_item_number; ?></span><br>
							<span data-testid="pdc-ordered-copies"><strong>Copies</strong> <?php echo $pdc_order_item->options->copies; ?></span><br>
							<span><strong>Purchase Date</strong> <?php echo $pdc_purchase_date; ?></span><br>
							<span><strong>Item Status</strong> <?php echo $pdc_order_item_status; ?></span><br>
							<span><strong>Price</strong> <?php echo wc_price( $pdc_order_item_grand_total ); ?></span><br>
							<span><strong>Track & Trace</strong> <a href="<?php echo $pdc_tnt_url; ?>"><?php echo $pdc_tnt_url; ?></a></span><br>
						<?php } ?>

						<?php if ( $pdc_pdf_url ) { ?>
							<span><strong>File</strong> <a target="_blank" href="<?php echo $pdc_pdf_url; ?>"><?php echo $filename; ?></span></a><br>
						<?php } ?>

						<div class="notifications">
							<?php
							if ( ! $has_file ) {
								?>
								<p>Missing file. Upload one to purchase.</p> <?php } ?>
							<?php
							if ( ! $has_preset ) {
								?>
								<p> Missing preset. You need a connected preset on the product to purchase. </p><?php } ?>
						</div>
					</div>
					<div class="table-cell">
						<div class="actions">
							<?php if ( $pdc_order_item_number === null || $pdc_order_item_number === '' ) { ?>
								<input type="text" class="hidden" id="js-pdc-order-pdf" placeholder="<?php esc_attr_e( 'http://', 'pdc-connector' ); ?>" name="<? $meta_key_pdf_url; ?>" value="<?php echo esc_attr( $pdc_pdf_url ); ?>" />
								<?php
								if ( empty( $pdc_pdf_url ) ) {
									?>
									<a href="#" id="pdc-file-upload" data-order-item-id="<?php echo $order_item_id; ?>" class="button button-secondary">Upload PDF</a><?php } ?>
								<?php
								if ( $pdc_pdf_url ) {
									?>
									<a href="#" id="pdc-file-upload" data-order-item-id="<?php echo $order_item_id; ?>" class="button button-secondary">Replace PDF</a><?php } ?>
								<?php if ( $has_preset ) { ?>
									<a id="pdc-order" data-testid="pdc-purchase-orderitem" data-order-item-id="<?php echo $order_item_id; ?>" href="#" class="button button-primary"> Purchase</a>
								<?php } ?>
								<span class="spinner" id="js-pdc-action-spinner"></span>
								<div class="notice-warning"><span id="js-pdc-request-response"></span></div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>