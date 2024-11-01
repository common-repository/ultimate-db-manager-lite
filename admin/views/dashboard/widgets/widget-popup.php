<div id="new-backup-popup" class="white-popup mfp-with-anim mfp-hide">

    <div class="ultimate-box-header ultimate-block-content-center">
        <h3 class="ultimate-box-title type-title"><?php esc_html_e( 'Create a new backup', 'ultimate-db-manager' ); ?></h3>
    </div>

    <div class="ultimate-box-body ultimate-block-content-center">

        <p class="ultimate-description">
            <?php esc_html_e( 'Select your custom backup options.', 'ultimate-db-manager' ); ?>
        </p>

    </div>

    <div class="ultimate-box-selectors ultimate-box-selectors-col-2">
        <div class="option-section">
            <div class="header-expand-collapse clearfix">
                <div class="expand-collapse-arrow collapsed">&#x25BC;</div>
                <div class="option-heading tables-header"><?php esc_html_e( 'Tables', 'ultimate-db-manager' ); ?></div>
            </div>

            <div class="indent-wrap collapsed-content">

                <ul class="option-group table-migrate-options">
                    <li>
                        <label for="migrate-only-with-prefix">
                            <input id="migrate-only-with-prefix" type="radio" value="migrate_only_with_prefix" name="table_manual_select_option" checked="checked">
                            <span><?php esc_html_e( 'Backup all tables with prefix "wp_"', 'ultimate-db-manager' ); ?></span>					 
                        </label>
                    </li>
                    <li>
                        <label for="migrate-selected">
                            <input id="migrate-selected" class="show-multiselect" type="radio" value="migrate_select" name="table_manual_select_option">
                            <span><?php esc_html_e( 'Backup only selected tables below', 'ultimate-db-manager' ); ?></span>
                        </label>
                    </li>
                </ul>

                <input type="hidden" name="table-manual-option" id="table_manual_option" value="migrate_only_with_prefix">


                <div class="select-tables-wrap">
                    <select multiple="multiple" name="manual_select_tables[]" id="manual-select-tables" class="multiselect" autocomplete="off">
                        <?php
                        $table_sizes = $this->table->get_table_sizes();
                        foreach ( $this->table->get_tables() as $table ) {
                            $size = (int) $table_sizes[ $table ] * 1024;
                            printf( '<option value="%1$s">%1$s (%2$s)</option>', esc_html( $table ), size_format( $size ) );
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

    <div class="ultimate-box-last-backup">
    </div>

    <div class="ultimate-box-footer">
        <div class="ultimate-actions-left">
            <button class="ultimate-button ultimate-cancel-backup">
                <span class="ultimate-loading-text"><?php esc_html_e( 'Cancel', 'ultimate-db-manager' ); ?></span>
                <i class="ultimate-icon-load ultimate-loading" aria-hidden="true"></i>
            </button>
        </div>
        <div class="ultimate-actions-right">

            <button class="ultimate-button ultimate-trigger-backup">
                <span class="ultimate-loading-text"><?php esc_html_e( 'Continue', 'ultimate-db-manager' ); ?></span>
                <i class="ultimate-icon-load ultimate-loading" aria-hidden="true"></i>
            </button>

        </div>
    </div>

</div>

