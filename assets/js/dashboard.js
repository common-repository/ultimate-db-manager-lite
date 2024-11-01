(function($){

    "use strict";

    var UltimateDBDashboard = {

        tables_total_count : 0,
        tables_processed_index : 0,

        init: function()
        {
            // Document ready.
            $( document ).ready( UltimateDBDashboard._loadPopup() );


            this._bind();
        },

        /**
         * Binds events for the UltimateDBDashboard.
         *
         * @since 1.0.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $( document ).on('click', '.header-expand-collapse', UltimateDBDashboard._collapseTablesOptions );
            $( document ).on('click', '#migrate-selected', UltimateDBDashboard._selectTables );
            $( document ).on('click', '#migrate-only-with-prefix', UltimateDBDashboard._undoSelectTables );
            $( document ).on('click', '.ultimate-trigger-backup', UltimateDBDashboard._triggerBackup );
            $( document ).on('backup-complete-action' , UltimateDBDashboard._triggerComplete );
            $( document ).on('click', '.multiselect-select-all', UltimateDBDashboard._selectAllOptions );
            $( document ).on('click', '.multiselect-deselect-all', UltimateDBDashboard._deSelectAllOptions );
            $( document ).on('click', '.multiselect-invert-selection', UltimateDBDashboard._invertSelectOptions );
            $( document ).on('click', 'input[name=table_manual_select_option]', UltimateDBDashboard._tableManualOptions );
            $( document ).on('click', '.ultimate-cancel-backup', UltimateDBDashboard._closePopup );
            $( document ).on('click', '.initial-table-options', UltimateDBDashboard._showPopup );

        },

        /**
         * Show Popup
         *
         */
        _showPopup: function( event ) {

            event.preventDefault();

            console.log('show popup');
            $('.ultimate-box-title').html('Create a new backup');
            $('.ultimate-description').html('Select your custom backup options.');
            $('.ultimate-box-selectors').show();
            $('.ultimate-box-last-backup').html('');
            $('.ultimate-box-footer').show();

        },

        /**
         * Close Popup
         *
         */
        _closePopup: function( event ) {

            event.preventDefault();

            console.log('close popup');

            var magnificPopup = $.magnificPopup.instance; 
            // save instance in magnificPopup variable
            magnificPopup.close(); 
            // Close popup that is currently opened


        },

        /**
         * Load Popup
         *
         */
        _loadPopup: function( ) {

            $('.open-popup-link').magnificPopup({
                type:'inline',
                midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
                // Delay in milliseconds before popup is removed
                removalDelay: 300,

                // Class that is added to popup wrapper and background
                // make it unique to apply your CSS animations just to this exact popup
                callbacks: {
                    beforeOpen: function() {
                        this.st.mainClass = this.st.el.attr('data-effect');
                    }
                },
            });


        },

        /**
         * Collapse Tables Options
         *
         */
        _collapseTablesOptions: function( event ) {

            event.preventDefault();

            $(this).find('.expand-collapse-arrow').toggleClass('collapsed');
            $(this).closest('.option-section').find('.indent-wrap').toggleClass('collapsed-content');

        },

        /**
         * Select Tables
         *
         */
        _selectTables: function() {

            if($(this).prop("checked") == true){
                $(this).closest('.indent-wrap').find('.select-tables-wrap').show();
            }
        },

        /**
         * Undo Select Tables
         *
         */
        _undoSelectTables: function() {

            if($(this).prop("checked") == true){
                $(this).closest('.indent-wrap').find('.select-tables-wrap').hide();
            }
        },

        /**
         * Tables options
         *
         */
        _tableManualOptions: function() {

            var value = $(this).val();      
            $('#table_manual_option').val(value);
            
        },

        /**
         * Trigger Backup Action
         *
         */
        _triggerBackup: function( ) {

            console.log('trigger backup');

            var fields = {};
            fields['campaign_status'] = 'draft';
            fields['table_manual_option'] = $("#table_manual_option").val();
            fields['manual_select_tables'] = $('#manual-select-tables').val();
            console.log(fields);


            $.ajax({
                    url  : Ultimate_DB_Manager_Data.ajaxurl,
                    type : 'POST',
                    dataType: 'json',
                    data : {
                        action       : 'ultimate_db_trigger_backup',
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
                        console.log(options.data.tables);
                        UltimateDBDashboard.tables_total_count = options.data.tables.length;
                        $('.ultimate-box-title').html('Backup start now, please wait ...');
                        $('.ultimate-description').html('Running now, please wait ...');
                        $('.ultimate-box-footer').hide();
                        // Clear last backup data
                        $('.ultimate-box-selectors').hide();
                        $('.ultimate-box-last-backup').html('');
                        UltimateDBDashboard.tables_processed_index = 0;
                        // Get backup filename and tables
                        var filename = options.data.dump_filename;
                        $.each(options.data.tables, function(index, value) {
                            // Start table backup
                            UltimateDBDashboard._ajaxTableBackup(value, filename);
                        });

                    }
                });


        },

        /**
         * Trigger Backup Complete
         *
         */
        _triggerComplete: function( ) {
            console.log('trigger complete');

            $('.ultimate-box-title').html('Backup Complete!');
            $('.ultimate-description').html('Complete');

            // redirect to backups page
            window.location.replace(Ultimate_DB_Manager_Data.backups_url);

        },


        /**
         * Ajax table backup
         *
         */
        _ajaxTableBackup: function( table, filename ) {

            console.log(table + ' trigger');

            var fields = {};
            fields['table'] = table;
            fields['filename'] = filename;

            $.ajax({
                    url  : Ultimate_DB_Manager_Data.ajaxurl,
                    type : 'POST',
                    dataType: 'json',
                    data : {
                        action       : 'ultimate_db_table_backup',
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
                        UltimateDBDashboard.tables_processed_index++;
                        if($('.ultimate-box-last-backup').find('.option-section').length !== 0){
                            $('.ultimate-box-last-backup').html(options.data);
                        }else{
                            $('.ultimate-box-last-backup').append(options.data);
                        }
                        console.log(UltimateDBDashboard.tables_processed_index);
                        console.log(UltimateDBDashboard.tables_total_count);
                        if(UltimateDBDashboard.tables_processed_index == UltimateDBDashboard.tables_total_count){
                            // Trigger backup complete action
                            $(document).trigger( 'backup-complete-action' );
                        }

                    }
                });



        },

        /**
         * invert Options
         *
         */
        _invertSelectOptions: function( event ) {

            event.preventDefault();
            var t = $(this).parents(".select-tables-wrap").children(".multiselect").find("option");

            t.each(function(index, elem) {
                var $elem = $(elem);
                if ($elem.prop('selected')) {
                    $elem.prop('selected', false);
                }else{
                    $elem.prop('selected', true);
                }
            });

        },

        /**
         * deselect All Options
         *
         */
        _deSelectAllOptions: function( event ) {

            event.preventDefault();
            var t = $(this).parents(".select-tables-wrap").children(".multiselect").find("option");

            t.each(function(index, elem) {
                var $elem = $(elem);
                $elem.prop('selected', false);
            });

        },

        /**
         * select All Options
         *
         */
        _selectAllOptions: function( event ) {

            event.preventDefault();
            var t = $(this).parents(".select-tables-wrap").children(".multiselect").find("option");

            t.each(function(index, elem) {
                var $elem = $(elem);
                $elem.prop('selected', true);
            });

        },

        
    };

    /**
     * Initialize UltimateDBDashboard
     */
    $(function(){
        UltimateDBDashboard.init();
    });

})(jQuery);