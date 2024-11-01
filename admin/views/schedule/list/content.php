<?php
// Count total forms
$count        = $this->countModules();
$count_active = $this->countModules( 'publish' );

// available bulk actions
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
                    <input type="hidden" id="ultimate-db-manager-nonce" name="ultimate_db_manager_nonce" value="<?php echo wp_create_nonce( 'ultimate-db-schedule-request' );?>">
                    <input type="hidden" name="_wp_http_referer" value="<?php admin_url( 'admin.php?page=auto-ultimate-campaign' ); ?>">
                    <input type="hidden" name="ids" id="ultimate-select-schedules-ids" value="">

                    <label for="ultimate-check-all-schedules" class="ultimate-checkbox">
                        <input type="checkbox" id="ultimate-check-all-schedules">
                        <span aria-hidden="true"></span>
                        <span class="ultimate-screen-reader-text">Select all</span>
                    </label>

                    <div class="ultimate-select-wrapper">
                        <select name="ultimate_db_manager_bulk_action" id="bulk-action-selector-top">
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

    <div class="ultimate-accordion ultimate-accordion-block" id="ultimate-modules-list">

        <?php
        foreach ( $this->getModules() as $module ) {
        ?>
            <div class="ultimate-accordion-item">
                <div class="ultimate-accordion-item-header">

                    <div class="ultimate-accordion-item-title ultimate-trim-title">
                        <label for="wpf-module-<?php echo esc_attr( $module['id'] ); ?>" class="ultimate-checkbox ultimate-accordion-item-action">
                            <input type="checkbox" id="wpf-module-<?php echo esc_attr( $module['id'] ); ?>" class="ultimate-check-single-campaign" value="<?php echo esc_html( $module['id'] ); ?>">
                            <span aria-hidden="true"></span>
                            <span class="ultimate-screen-reader-text"><?php esc_html_e( 'Select this form', 'ultimate-db-manager' ); ?></span>
                        </label>
                        <span class="ultimate-trim-text">
                            <?php echo ultimate_db_manager_get_schedule_name( $module['id'] ); ?>
                        </span>
                        <?php
                        if ( 'publish' === $module['status'] ) {
                            echo '<span class="ultimate-tag ultimate-tag-blue">' . esc_html__( 'Published', 'ultimate-db-manager' ) . '</span>';
                        }
                        ?>

                        <?php
                        if ( 'draft' === $module['status'] ) {
                            echo '<span class="ultimate-tag">' . esc_html__( 'Draft', 'ultimate-db-manager' ) . '</span>';
                        }
                        ?>
                    </div>

                    <div class="ultimate-accordion-item-date">
                        <strong><?php esc_html_e( 'Next Run', 'ultimate-db-manager' ); ?></strong>
                        <?php echo ultimate_db_manager_get_next_run_time( $module['id'] ); ?>
                    </div>

                    <div class="ultimate-accordion-col-auto">

                        <a href="<?php echo admin_url( 'admin.php?page=ultimate-db-manager-schedule-wizard&id=' . $module['id'] ); ?>"
                           class="ultimate-button ultimate-button-ghost ultimate-accordion-item-action ultimate-desktop-visible">
                            <ion-icon name="pencil" class="ultimate-icon-pencil"></ion-icon>
                            <?php esc_html_e( 'Edit', 'ultimate-db-manager' ); ?>
                        </a>

                        <div class="ultimate-dropdown ultimate-accordion-item-action">
                            <button class="ultimate-button-icon ultimate-dropdown-anchor">
                                <ion-icon name="settings"></ion-icon>
                            </button>
                            <ul class="ultimate-dropdown-list">

                                <li>
                                    <form method="post">
                                        <input type="hidden" id="ultimate-db-manager-nonce" name="ultimate_db_manager_nonce" value="<?php echo wp_create_nonce( 'ultimate-db-schedule-request' );?>">
                                        <input type="hidden" name="ultimate_db_manager_single_action" value="update-status">
                                        <input type="hidden" name="id" value="<?php echo esc_attr( $module['id'] ); ?>">
                                        <?php
                                        if ( 'publish' === $module['status'] ) {
                                            ?>
                                            <input type="hidden" name="status" value="draft">
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if ( 'draft' === $module['status'] ) {
                                            ?>
                                            <input type="hidden" name="status" value="publish">
                                            <?php
                                        }
                                        ?>
                                        <button type="submit">
                                            <ion-icon class="ultimate-icon-cloud" name="cloud"></ion-icon>
                                            <?php
                                            if ( 'publish' === $module['status'] ) {
                                                echo esc_html__( 'Unpublish', 'ultimate-db-manager' );
                                            }
                                            ?>

                                            <?php
                                            if ( 'draft' === $module['status'] ) {
                                                echo esc_html__( 'Publish', 'ultimate-db-manager' );
                                            }
                                            ?>
                                        </button>
                                    </form>
                                </li>

                                <li>
                                    <form method="post">
                                        <input type="hidden" id="ultimate-db-manager-nonce" name="ultimate_db_manager_nonce" value="<?php echo wp_create_nonce( 'ultimate-db-schedule-request' );?>">
                                        <input type="hidden" name="ultimate_db_manager_single_action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo esc_attr( $module['id'] ); ?>">
                                        <button type="submit">
                                            <ion-icon class="ultimate-icon-trash" name="trash"></ion-icon>
                                            <?php esc_html_e( 'Delete', 'ultimate-db-manager' ); ?>
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>

                        <button class="ultimate-button-icon ultimate-accordion-open-indicator" aria-label="<?php esc_html_e( 'Open item', 'ultimate-db-manager' ); ?>">
                            <ion-icon name="chevron-down"></ion-icon>
                        </button>


                    </div>

                </div>
            </div>

        <?php

        }

        ?>

    </div>


<?php } else { ?>
<div class="ultimate-box ultimate-message ultimate-message-lg">

    <img src="<?php echo esc_url(ULTIMATE_DB_MANAGER_URL.'/assets/images/ultimate.png'); ?>" class="ultimate-image ultimate-image-center" aria-hidden="true" alt="<?php esc_attr_e( 'Auto Robot', 'ultimate-db-manager' ); ?>">

    <div class="ultimate-message-content">

        <p><?php esc_html_e( 'Create schedule for your needs with customized settings, include database backup, cleanup and optimize.', 'ultimate-db-manager' ); ?></p>

        <p>
                <a href="<?php echo esc_url( 'https://1.envato.market/oQDB9', 'ultimate-db-manager' ); ?>" target="_blank" class="ultimate-button ultimate-button-blue">
                    <?php esc_html_e( 'Buy Pro Version', 'ultimate-db-manager' ); ?>
                </a>
        </p>


    </div>

</div>

<?php } ?>