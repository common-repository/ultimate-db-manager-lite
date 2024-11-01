(function($){

    "use strict";

    var UltimateDBScheduleList = {

        init: function()
        {
            this._bind();
        },

        /**
         * Binds events for the UltimateDBScheduleList.
         *
         * @since 1.0.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $( document ).on('click', '#ultimate-check-all-schedules', UltimateDBScheduleList._checkAll );
            $( document ).on('click', '.ultimate-bulk-action-button', UltimateDBScheduleList._prepareBulk );
            $( document ).on('click', '.ultimate-dropdown-anchor', UltimateDBScheduleList._displayActions );
            $( document ).on('click', '.ultimate-delete-action', UltimateDBScheduleList._deleteAction );

        },

        /**
         * Delete Action
         *
         */
        _deleteAction: function( ) {

            var data = $(this).data('schedule-id');

            $('.ultimate-delete-id').val(data);


        },

        /**
         * Display Actions
         *
         */
        _displayActions: function( event ) {

            event.preventDefault();

            if($(this).closest('.ultimate-dropdown').find('.ultimate-dropdown-list').hasClass('active')){
                $(this).closest('.ultimate-dropdown').find('.ultimate-dropdown-list').removeClass('active');
            }else{
                $(this).closest('.ultimate-dropdown').find('.ultimate-dropdown-list').addClass('active');
            }

        },


         /**
         * Check All
         *
         */
        _checkAll: function( ) {

            if($(this).prop('checked')){
                $('.ultimate-check-single-campaign').prop('checked', true);
            }else{
                $('.ultimate-check-single-campaign').prop('checked', false);

            }

        },

        /**
         * Prepare data before bulk action
         *
         */
        _prepareBulk: function( ) {

            var ids = [];
            $('.ultimate-check-single-campaign').each(function( index ) {
                if($(this).prop('checked')){
                    var value = $(this).val();
                    ids.push(value);
                }
            });

            $('#ultimate-select-schedules-ids').val(ids);

        },

       
        
    };

    /**
     * Initialize UltimateDBScheduleList
     */
    $(function(){
        UltimateDBScheduleList.init();
    });

})(jQuery);