<?php
$id = isset( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : '';
// Campaign Settings
$settings = array();
if(!empty($id)){
    $model    = $this->get_single_model( $id );
    $settings = $model->settings;
    $settings['status'] = $model->status;
    $schedule_type = $settings['schedule_type'];
}else{
    $schedule_type = 'backup';
}
?>
<div class="ultimate-row-with-sidenav">

    <div class="ultimate-sidenav">

        <div class="ultimate-mobile-select">
            <span class="ultimate-select-content"><?php esc_html_e( 'General', 'ultimate-db-manager' ); ?></span>
            <ion-icon name="chevron-down" class="ultimate-icon-down"></ion-icon>
        </div>

        <ul class="ultimate-vertical-tabs ultimate-sidenav-hide-md">

            <li class="ultimate-vertical-tab">
                <a href="#" data-nav="general"><?php esc_html_e( 'General', 'ultimate-db-manager' ); ?></a>
            </li>

            <li class="ultimate-vertical-tab">
                <a href="#" data-nav="schedule"><?php esc_html_e( 'Schedule', 'ultimate-db-manager' ); ?></a>
            </li>

        </ul>

    </div>

    <form class="ultimate-schedule-form" method="post" name="ultimate-schedule-form" action="">

    <div class="ultimate-box-tabs">
        <?php $this->template( 'schedule/wizard/sections/tab-save',  $settings); ?>
        <?php $this->template( 'schedule/wizard/sections/tab-general', $settings); ?>
        <?php $this->template( 'schedule/wizard/sections/tab-schedule',  $settings); ?>
    </div>
        <input type="hidden" name="schedule_id" value="<?php echo esc_html($id); ?>">
        <input type="hidden" name="schedule_type" value="<?php echo esc_html($schedule_type); ?>">
    </form>
</div>

