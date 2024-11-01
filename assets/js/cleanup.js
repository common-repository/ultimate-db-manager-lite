(function($){

    "use strict";

    var UltimateDBCleanup = {

        init: function()
        {
            this._bind();
        },

        /**
         * Binds events for the UltimateDBCleanup.
         *
         * @since 1.0.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $( document ).on('click', '#ultimate-check-all-cleanup', UltimateDBCleanup._checkAll );
            $( document ).on('click', '.ultimate-cleanup-empty', UltimateDBCleanup._singleCleanup );
            $( document ).on('click', '.ultimate-bulk-action-button', UltimateDBCleanup._prepareCleanup );

        },

        /**
         * Check All
         *
         */
        _checkAll: function( ) {

            if($(this).prop('checked')){
                $('.ultimate-cleanup-listing-checkbox').prop('checked', true);
            }else{
                $('.ultimate-cleanup-listing-checkbox').prop('checked', false);

            }

        },

        /**
         * Cleanup signle table
         *
         */
        _singleCleanup: function( event ) {

            event.preventDefault();

            if (!confirm('Are you sure you wish to empty this table data?')) {
				return;
            }
            
            var fields = {};
            fields['name'] = $(this).data('name');

            $.ajax({
                url  : Ultimate_DB_Manager_Data.ajaxurl,
                type : 'POST',
                dataType: 'json',
                data : {
                    action       : 'ultimate_db_empty_cleanup',
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
         * Prepare data before cleanup
         *
         */
        _prepareCleanup: function( ) {

            var files = [];
            $('.ultimate-cleanup-listing-checkbox').each(function( index ) {
                if($(this).prop('checked')){
                    var value = $(this).val();
                    files.push(value);
                }
            });

            $('#ultimate-cleanup-names').val(files);

        },
       
        
    };

    /**
     * Initialize UltimateDBCleanup
     */
    $(function(){
        UltimateDBCleanup.init();
    });

})(jQuery);