<?php
$status = isset( $settings['status'] ) ? sanitize_text_field( $settings['status'] ) : 'draft';
?>
<div id="auto-ultimate-builder-status" class="ultimate-box ultimate-box-sticky">
    <div class="ultimate-box-status">
        <div class="ultimate-status">
            <div class="ultimate-status-module">
                <?php esc_html_e( 'Status', 'ultimate-db-manager' ); ?>
                    <?php
                    if( $status === 'draft'){
                        ?>
                    <span class="ultimate-tag ultimate-tag-draft">
                        <?php esc_html_e( 'draft', 'ultimate-db-manager' ); ?>
                    </span>
                    <?php
                    }else if($status === 'publish'){
                        ?>
                    <span class="ultimate-tag ultimate-tag-published">
                       <?php esc_html_e( 'published', 'ultimate-db-manager' ); ?>
                    </span>
                    <?php
                    }
                    ?>
            </div>
            <div class="ultimate-status-changes">

            </div>
        </div>
        <div class="ultimate-actions">
            <button id="ultimate-schedule-draft" class="ultimate-button" type="button">
                <span class="ultimate-loading-text">
                    <ion-icon name="reload-circle"></ion-icon>
                    <span class="button-text campaign-save-text">
                        <?php
                        if($status === 'publish'){
                            echo esc_html( 'unpublish', 'ultimate-db-manager' );
                        }else{
                            echo esc_html( 'save draft', 'ultimate-db-manager' );
                        }
                        ?>
                    </span>
                </span>
            </button>
            <button id="ultimate-schedule-publish" class="ultimate-button ultimate-button-blue" type="button">
                <span class="ultimate-loading-text">
                    <ion-icon name="save"></ion-icon>
                    <span class="button-text campaign-publish-text">
                        <?php
                        if($status === 'publish'){
                            echo esc_html( 'update', 'ultimate-db-manager' );
                        }else{
                            echo esc_html( 'publish', 'ultimate-db-manager' );
                        }
                        ?>
                    </span>
                </span>
            </button>
        </div>
    </div>
</div>
