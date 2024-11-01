<?php
if(!isset($settings['table_backup_option'])){
    $settings['table_backup_option'] = 'backup_only_with_prefix';
}

if(!isset($settings['schedule_type'])){
    $settings['schedule_type'] = 'backup';
}

?>
<div class="ultimate-box-settings-row">

    <div class="ultimate-box-settings-col-1">
        <span class="ultimate-settings-label"><?php esc_html_e( 'Type', 'ultimate-db-manager' ); ?></span>
    </div>

    <div class="ultimate-box-settings-col-2">
        <div class="sui-side-tabs">
            <div class="sui-tabs-menu">
                <div class="sui-tab-item <?php echo ( $settings['schedule_type'] == 'backup' ? 'active' : '' ); ?>" data-nav="backup"><?php esc_html_e( 'Backup', 'ultimate-db-manager' ); ?></div>
                <div class="sui-tab-item <?php echo ( $settings['schedule_type'] == 'cleanup' ? 'active' : '' ); ?>" data-nav="cleanup"><?php esc_html_e( 'Cleanup', 'ultimate-db-manager' ); ?></div>
                <div class="sui-tab-item <?php echo ( $settings['schedule_type'] == 'optimize' ? 'active' : '' ); ?>" data-nav="optimize"><?php esc_html_e( 'Optimize', 'ultimate-db-manager' ); ?></div>

            </div>
        </div>
        <div class="sui-tabs-content">
            <div class="sui-tab-content <?php echo ( $settings['schedule_type'] == 'backup' ? 'active' : '' ); ?>" id="backup">
            <div class="ultimate-box-selectors ultimate-schedule-type-selector">
                <div class="option-section">
                    <div class="header-expand-collapse clearfix">
                    <div class="expand-collapse-arrow collapsed">&#x25BC;</div>
                    <div class="option-heading tables-header"><?php esc_html_e( 'Tables', 'ultimate-db-manager' ); ?></div>
                    </div>

                    <div class="indent-wrap collapsed-content">

                    <ul class="option-group table-backup-options">
                    <li>
                        <label for="backup-only-with-prefix">
                            <input id="backup-only-with-prefix" type="radio" value="backup_only_with_prefix" name="table_backup_option" <?php echo ( $settings['table_backup_option'] == 'backup_only_with_prefix' ? ' checked="checked"' : '' ); ?>>
                            <span><?php esc_html_e( 'Backup all tables with prefix "wp_"', 'ultimate-db-manager' ); ?></span>
                        </label>
                    </li>
                    <li>
                        <label for="backup-selected">
                            <input id="backup-selected" class="show-multiselect" type="radio" value="backup_select" name="table_backup_option" <?php echo ( $settings['table_backup_option'] == 'backup_select' ? ' checked="checked"' : '' ); ?>>
                            <span><?php esc_html_e( 'Backup only selected tables below', 'ultimate-db-manager' ); ?></span>
                        </label>
                    </li>
                    </ul>

                    <div class="select-tables-wrap">
                    <select multiple name="backup_select_tables[]" id="backup-select-tables" class="multiselect schedule-multiselect" autocomplete="off">
                        <?php
                        $table_sizes = $this->table->get_table_sizes();
                        foreach ( $this->table->get_tables() as $table ) {
                            $size = (int) $table_sizes[ $table ] * 1024;
                            if ( ! empty( $settings['ultimate_backup_tables'] ) && in_array( $table, $settings['ultimate_backup_tables'] ) ) {
                                printf( '<option value="%1$s" selected="selected">%1$s (%2$s)</option>', esc_html( $table ), size_format( $size ) );
                            } else {
                                printf( '<option value="%1$s">%1$s (%2$s)</option>', esc_html( $table ), size_format( $size ) );
                            }
                        }
                        ?>
                    </select>
                    <br>
                    <a href="#" class="multiselect-select-all js-action-link"><?php esc_html_e( 'Select All', 'ultimate-db-manager' ); ?></a>
                    <span class="select-deselect-divider">/</span>
                    <a href="#" class="multiselect-deselect-all js-action-link"><?php esc_html_e( 'Deselect All', 'ultimate-db-manager' ); ?></a>
                    <span class="select-deselect-divider">/</span>
                    <a href="#" class="multiselect-invert-selection js-action-link"><?php esc_html_e( 'Invert Selection', 'ultimate-db-manager' ); ?></a>
                    </div>
                    </div>
                </div>
            </div>
            </div>

            <div class="sui-tab-content <?php echo ( $settings['schedule_type'] == 'cleanup' ? 'active' : '' ); ?>" id="cleanup">
               
            </div>

            <div class="sui-tab-content <?php echo ( $settings['schedule_type'] == 'optimize' ? 'active' : '' ); ?>" id="optimize">
                
            </div>
        </div>
    </div>

</div>
