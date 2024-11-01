<div id="general" class="ultimate-box-tab active" data-nav="general" >

	<div class="ultimate-box-header">
		<h2 class="ultimate-box-title"><?php esc_html_e( 'General', 'ultimate-db-manager' ); ?></h2>
	</div>

    <div class="ultimate-box-body">
		<?php $this->template( 'schedule/wizard/components/schedule-name', $settings ); ?>
		<?php $this->template( 'schedule/wizard/components/schedule-type', $settings ); ?>
   </div>

</div>
