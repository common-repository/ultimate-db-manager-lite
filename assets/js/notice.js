(function($){

    "use strict";

    var UltimateDBNotice = {

        init: function()
        {
            // Document ready.
            this._bind();
        },

        /**
         * Binds events for the Ultimate DB Notice.
         *
         * @since 1.0.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $( document ).on('click', '.ultimate-notice-skip', UltimateDBNotice._skip );

        },

        /**
         * Skip opt in
         *
         */
        _skip: function( event ) {

            event.preventDefault();

            console.log('click skip.');

            $.ajax({
                url  : Ultimate_DB_Manager_Data.ajaxurl,
                type : 'POST',
                dataType: 'json',
                data : {
                    action       : 'ultimate_db_skip_premium',
                    type         : 'skip',         
                    _ajax_nonce  : Ultimate_DB_Manager_Data._ajax_nonce,
                },
                beforeSend: function() {
                },
            })
            .fail(function( jqXHR ){
                console.log( jqXHR.status + ' ' + jqXHR.responseText);
            })
            .done(function ( option ) {
                if( false === option.success ) {
                    console.log(option);
                } else {
                    console.log(option);
                    window.location.href = "admin.php?page=ultimate-db-manager";
                }

            });

        },    

    };

    /**
     * Initialize UltimateDBNotice
     */
    $(function(){
        UltimateDBNotice.init();
    });

})(jQuery);
