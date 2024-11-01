<div class="ultimate-box-settings-row">

    <div class="ultimate-box-settings-col-1">
        <span class="ultimate-settings-label"><?php esc_html_e( 'Schedule Name', 'ultimate-db-manager' ); ?></span>
    </div>

    <div class="ultimate-box-settings-col-2">

        <div>
            <input
                type="text"
                name="ultimate_schedule_name"
                placeholder="<?php esc_html_e( 'Enter your Schedule Name here', 'ultimate-db-manager' ); ?>"
                value="<?php if(isset($settings['ultimate_schedule_name'])){echo esc_attr($settings['ultimate_schedule_name']);}?>"
                id="ultimate_schedule_name"
                class="ultimate-form-control"
                aria-labelledby="ultimate_schedule_name"
            />
        </div>


    </div>

</div>
