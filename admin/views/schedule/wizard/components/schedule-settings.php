<div class="ultimate-box-settings-row">

    <div class="ultimate-box-settings-col-1">
        <span class="ultimate-settings-label"><?php esc_html_e( 'Schedule Settings', 'ultimate-db-manager' ); ?></span>
        <span class="ultimate-description"><?php esc_html_e( 'Run this campaign as the WP Cron Job Schedule.', 'ultimate-db-manager' ); ?></span>
    </div>

    <div class="ultimate-box-settings-col-2">

        <label class="ultimate-settings-label"><?php esc_html_e( 'Schedule Settings', 'ultimate-db-manager' ); ?></label>

        <span class="ultimate-description"><?php esc_html_e( 'Select Campaign Schedule use the WP Cron Job Schedule on backend.', 'ultimate-db-manager' ); ?></span>

        <div class="range-slider">
            <input class="range-slider__range" type="range" value="<?php if(isset($settings['update_frequency'])){echo esc_attr($settings['update_frequency']);}else{echo esc_attr('100');}?>" min="0" max="500">
            <span class="range-slider__value">0</span>
        </div>

        <span class="ultimate-description"><?php esc_html_e( 'Time Unit', 'ultimate-db-manager' ); ?></span>

        <div class="select-container">
            <span class="dropdown-handle" aria-hidden="true">
                <ion-icon name="chevron-down" class="ultimate-icon-down"></ion-icon>
            </span>
            <div class="select-list-container">
                <button type="button" class="list-value" id="ultimate-field-unit-button" value="Minutes">
                    <?php
                    if(isset($settings['update_frequency_unit'])){
                        echo esc_html($settings['update_frequency_unit']);
                    }else{
                        esc_html_e( 'Minutes', 'ultimate-db-manager' );
                    }
                    ?>
                </button>
                <ul tabindex="-1" role="listbox" class="list-results ultimate-sidenav-hide-md" >
                    <li><?php esc_html_e( 'Minutes', 'ultimate-db-manager' ); ?></li>
                    <li><?php esc_html_e( 'Hours', 'ultimate-db-manager' ); ?></li>
                    <li><?php esc_html_e( 'Days', 'ultimate-db-manager' ); ?></li>
                </ul>
            </div>
        </div>


    </div>

</div>
