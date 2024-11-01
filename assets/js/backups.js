(function($){

    "use strict";

    var UltimateDBBackups = {

        init: function()
        {
            this._bind();
        },

        /**
         * Binds events for the UltimateDBBackups.
         *
         * @since 1.0.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $( document ).on('click', '#ultimate-check-all-backups', UltimateDBBackups._checkAll );
            $( document ).on('click', '.ultimate-backup-delete', UltimateDBBackups._singleDelete );
            $( document ).on('click', '.ultimate-bulk-action-button', UltimateDBBackups._preparePost );

        },
       
        /**
         * Check All
         *
         */
        _checkAll: function( ) {

            if($(this).prop('checked')){
                $('.ultimate-backups-listing-checkbox').prop('checked', true);
            }else{
                $('.ultimate-backups-listing-checkbox').prop('checked', false);

            }

        },

        /**
         * Delete signle backup file
         *
         */
        _singleDelete: function( event ) {

            event.preventDefault();

            if (!confirm('Are you sure you wish to permanently delete this backups?')) {
				return;
            }
            
            var fields = {};
            fields['filename'] = $(this).data('name');

            $.ajax({
                url  : Ultimate_DB_Manager_Data.ajaxurl,
                type : 'POST',
                dataType: 'json',
                data : {
                    action       : 'ultimate_db_delete_backup',
                    fields_data  : fields,
                    _ajax_nonce  : Ultimate_DB_Manager_Data._ajax_nonce
                },
                beforeSend: function() {

                },
            })
            .fail(function( jqXHR ){
                console.log( jqXHR.status + ' ' + jqXHR.responseText);
            })
            .done(function ( options ) {
                if( false === options.success ) {
                    console.log(options);
                } else {
                    console.log(options);
                    window.location.reload();

                }
            });
        },

        /**
         * Prepare data before post action
         *
         */
        _preparePost: function( ) {

            var files = [];
            $('.ultimate-backups-listing-checkbox').each(function( index ) {
                if($(this).prop('checked')){
                    var value = $(this).val();
                    files.push(value);
                }
            });

            $('#ultimate-backup-names').val(files);

        },
    };

    /**
     * Initialize UltimateDBBackups
     */
    $(function(){
        UltimateDBBackups.init();
    });

})(jQuery);