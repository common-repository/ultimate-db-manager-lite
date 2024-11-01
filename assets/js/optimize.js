(function($){

    "use strict";

    var UltimateDBOptimize = {

        init: function()
        {
            this._bind();
        },

        /**
         * Binds events for the UltimateDBOptimize.
         *
         * @since 1.0.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $( document ).on('click', '#ultimate-check-all-optimize', UltimateDBOptimize._checkAll );
            $( document ).on('click', '.ultimate-bulk-action-button', UltimateDBOptimize._prepareOptimize );
            $( document ).on('click', '.ultimate-single-optimize', UltimateDBOptimize._singleOptimize );


        },

        /**
         * Optimize signle table
         *
         */
        _singleOptimize: function( event ) {

            event.preventDefault();

            if (!confirm('Are you sure you wish to optimize this table?')) {
				return;
            }
            
            var fields = {};
            fields['name'] = $(this).data('name');

            $.ajax({
                url  : Ultimate_DB_Manager_Data.ajaxurl,
                type : 'POST',
                dataType: 'json',
                data : {
                    action       : 'ultimate_db_single_optimize',
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
         * Check All
         *
         */
        _checkAll: function( ) {

            if($(this).prop('checked')){
                $('.ultimate-optimize-listing-checkbox').prop('checked', true);
            }else{
                $('.ultimate-optimize-listing-checkbox').prop('checked', false);

            }

        },

        /**
         * Prepare data before optimize
         *
         */
        _prepareOptimize: function( ) {

            var files = [];
            $('.ultimate-optimize-listing-checkbox').each(function( index ) {
                if($(this).prop('checked')){
                    var value = $(this).val();
                    files.push(value);
                }
            });

            $('#ultimate-optimize-names').val(files);

        },

       
        
    };

    /**
     * Initialize UltimateDBOptimize
     */
    $(function(){
        UltimateDBOptimize.init();
    });

})(jQuery);