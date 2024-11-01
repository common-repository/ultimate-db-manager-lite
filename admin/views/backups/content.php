<?php
// Count total backups
$count = $this->countBackups();

// Available bulk actions
$bulk_actions = $this->bulk_actions();
?>
<?php if ( $count > 0 ) { ?>
    <!-- START: Bulk actions and pagination -->
    <div class="ultimate-listings-pagination">

        <div class="ultimate-pagination-mobile ultimate-pagination-wrap">
            <span class="ultimate-pagination-results">
                            <?php /* translators: ... */ echo esc_html( sprintf( _n( '%s result', '%s results', $count, 'ultimate-db-manager' ), $count ) ); ?>
                        </span>
            <?php $this->pagination(); ?>
        </div>

        <div class="ultimate-pagination-desktop ultimate-box">
            <div class="ultimate-box-search">

                <form method="post" name="ultimate-bulk-action-form" class="ultimate-search-left">

                    <input type="hidden" id="ultimate-backups-nonce" name="ultimate_backups_nonce" value="<?php echo wp_create_nonce( 'ultimate-backups-request' );?>">
                    <input type="hidden" name="_wp_http_referer" value="<?php admin_url( 'admin.php?page=ultimate-db-manager-backups' ); ?>">
                    <input type="hidden" name="backup-names" id="ultimate-backup-names" value="">

                    <div class="ultimate-select-wrapper">
                        <select name="ultimate_bulk_action" id="bulk-action-selector-top">
                            <option value=""><?php esc_html_e( 'Bulk Action', 'ultimate-db-manager' ); ?></option>
                            <?php foreach ( $bulk_actions as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="ultimate-button ultimate-bulk-action-button">Apply</button>

                </form>

                <div class="ultimate-search-right">

                    <div class="ultimate-pagination-wrap">
                        <span class="ultimate-pagination-results">
                            <?php /* translators: ... */ echo esc_html( sprintf( _n( '%s result', '%s results', $count, 'ultimate-db-manager' ), $count ) ); ?>
                        </span>
                        <?php $this->pagination(); ?>
                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- END: Bulk actions and pagination -->

    <div class="ultimate-box ultimate-table-list">
        <table class="ultimate-table-entries ultimate-table ultimate-table-flushed ultimate-accordion">
        <thead>
        <tr>
            <th class="ultimate-column-id">
                <label for="ultimate-check-all-backups" class="ultimate-checkbox">
                    <input type="checkbox" id="ultimate-check-all-backups">
                    <span aria-hidden="true"></span>
                    <span class="ultimate-screen-reader-text">Select all</span>
                </label>
                <label class="ultimate-checkbox ultimate-checkbox-sm">
                    <span><?php esc_html_e( 'Filename', 'ultimate-db-manager' ); ?></span>
                </label>
            </th>
            <th class="ultimate-column-date"><?php esc_html_e( 'Date', 'ultimate-db-manager' ); ?></th>
            <th class="ultimate-column-apps"><?php esc_html_e( 'Size', 'ultimate-db-manager' ); ?></th>
            <th class="ultimate-column-apps"><?php esc_html_e( 'Actions', 'ultimate-db-manager' ); ?></th>
        </tr>
        </thead>
        <tbody class="ultimate-list">
        <?php
            foreach ( $this->getBackups() as $module ) {
        ?>
        <tr class="ultimate-accordion-item">
            <td class="ultimate-column-id ultimate-accordion-item-title">
                <label class="ultimate-checkbox ultimate-checkbox-sm">
                    <input type="checkbox" class="ultimate-backups-listing-checkbox" value="<?php echo esc_html( $module['name'] ); ?>">
                    <span aria-hidden="true"></span>
                    <span><?php echo esc_html( $module['name'] ); ?></span>
                </label>
            </td>
            <td class="ultimate-column-date">
                <?php echo esc_html( $module['date'] ); ?>
            </td>
            <td class="ultimate-column-apps">
                <?php echo esc_html( $module['size'] ); ?>
            </td>
            <td class="ultimate-column-actions">
                <a href="#" class="ultimate-backup-actions ultimate-backup-delete" data-name="<?php echo esc_attr( $module['name'] ); ?>">
                    <ion-icon name="trash-outline"></ion-icon>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=ultimate-db-manager-backups&action=ultimate_download_backup' ).'&filename='.$module['name']; ?>" class="ultimate-backup-actions">
                    
                    <ion-icon name="download-outline"></ion-icon>        
                </a>
            </td>
        </tr>
        <?php
            }
        ?>
        </tbody>
    </table>
    </div>  
<?php } else { ?>
    <div class="ultimate-box ultimate-message ultimate-message-lg">

        <img src="<?php echo esc_url(ULTIMATE_DB_MANAGER_URL.'assets/images/ultimate.png'); ?>" class="ultimate-image ultimate-image-center" aria-hidden="true" alt="<?php esc_attr_e( 'Ultimate WP DB Manager', 'ultimate-db-manager' ); ?>">

        <div class="ultimate-message-content">

            <p><?php esc_html_e( 'Here is no database backup files to list now, please go back to the dashboard page to create new backup with advanced options first.', 'ultimate-db-manager' ); ?></p>

            <p>
                <a href="<?php echo admin_url( 'admin.php?page=ultimate-db-manager' ); ?>">
                    <button class="ultimate-button ultimate-button-blue" data-modal="custom_forms">
                        <i class="ultimate-icon-plus" aria-hidden="true"></i> <?php esc_html_e( 'Go to Dashboard', 'ultimate-db-manager' ); ?>
                    </button>
                </a>
            </p>


        </div>

    </div>

<?php } ?>