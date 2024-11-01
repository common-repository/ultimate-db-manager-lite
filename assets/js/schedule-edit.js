(function($){

    "use strict";

    var UltimateDBScheduleEdit = {

        init: function()
        {
            // Document ready.
            $( document ).ready( UltimateDBScheduleEdit._scheduleSelect() );
            $( document ).ready( UltimateDBScheduleEdit._rangeSlider() );
            $( document ).ready( UltimateDBScheduleEdit._loadBackupOptions() );

            this._bind();
        },

        /**
         * Binds events for the UltimateDBScheduleEdit.
         *
         * @since 1.0.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            
            $( document ).on('click', '.ultimate-vertical-tab a', UltimateDBScheduleEdit._switchTabs );
            $( document ).on('click', '#ultimate-schedule-publish', UltimateDBScheduleEdit._publishSchedule );
            $( document ).on('click', '#ultimate-schedule-draft', UltimateDBScheduleEdit._draftSchedule );
            $( document ).on('click', '.sui-tab-item', UltimateDBScheduleEdit._switchSuiTabs );
            $( document ).on('click', '.header-expand-collapse', UltimateDBScheduleEdit._collapseTablesOptions );
            $( document ).on('click', '#backup-selected', UltimateDBScheduleEdit._selectTables );
            $( document ).on('click', '#backup-only-with-prefix', UltimateDBScheduleEdit._undoSelectTables );
            $( document ).on('click', '.multiselect-select-all', UltimateDBScheduleEdit._selectAllOptions );
            $( document ).on('click', '.multiselect-deselect-all', UltimateDBScheduleEdit._deSelectAllOptions );
            $( document ).on('click', '.multiselect-invert-selection', UltimateDBScheduleEdit._invertSelectOptions );


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
         * load Backup Options
         *
         */
        _loadBackupOptions: function() {

            if($('#backup-selected').prop("checked") == true){
                $('#backup-selected').closest('.indent-wrap').find('.select-tables-wrap').show();
            }
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
         * Switch Sui Tabs
         *
         */
        _switchSuiTabs: function( event ) {

            event.preventDefault();

            var tab = '#' + $(this).data('nav');

            $('.sui-tab-item').removeClass('active');
            $(this).addClass('active');

            $('.sui-tab-content').removeClass('active');
            $('.sui-tabs-content').find(tab).addClass('active');

            console.log($(this).data('nav'));

            $( "input[name='schedule_type']" ).val($(this).data('nav'));


        },

        /**
         * Switch Tabs
         *
         */
        _switchTabs: function( event ) {

            event.preventDefault();

            var tab = '#' + $(this).data('nav');

            $('.ultimate-vertical-tab').removeClass('current');
            $(this).parent().addClass('current');

            $('.ultimate-box-tab').removeClass('active');
            $('.ultimate-box-tabs').find(tab).addClass('active');

        },

        /**
         * Schedule Select
         *
         */
        _scheduleSelect: function( ) {

            // onClick new options list of new select
            var newOptions = $('.list-results > li');
            newOptions.on('click', function(){
                $(this).closest('.select-list-container').find('.list-value').text($(this).text());
                $(this).closest('.select-list-container').find('.list-value').val($(this).text());
                $(this).closest('.select-list-container').find('.list-results > li').removeClass('selected');
                $(this).addClass('selected');
            });

            var aeDropdown = $('.select-list-container');
            aeDropdown.on('click', function(){
                $(this).closest('.select-list-container').find('.list-results').toggleClass('ultimate-sidenav-hide-md');
            });

            var ultimateDropdown = $('.dropdown-handle');
            ultimateDropdown.on('click', function(){
                $(this).closest('.select-list-container').find('.list-results').toggleClass('ultimate-sidenav-hide-md');
            });

        },

        /**
         * Range Slider
         *
         */
        _rangeSlider: function( ) {

            var slider = $('.range-slider'),
                range = $('.range-slider__range'),
                value = $('.range-slider__value');

            slider.each(function(){

                value.each(function(){
                    var value = $(this).prev().attr('value');
                    $(this).html(value);
                });

                range.on('input', function(){
                    $(this).next(value).html(this.value);
                });
            });

        },

        /**
         * Publish Schedule
         *
         */
        _publishSchedule: function( ) {

            var formdata = $('.ultimate-schedule-form').serializeArray();
            var fields = {};
            $(formdata ).each(function(index, obj){
                fields[obj.name] = obj.value;
            });
            fields['schedule_status'] = 'publish';
            fields['update_frequency'] = $('.range-slider__value').text();
            fields['update_frequency_unit'] = $('#ultimate-field-unit-button').val();
            fields['ultimate_post_status'] = $('#ultimate-post-status').val();

            fields['ultimate_backup_tables'] = $('#backup-select-tables').val();
            console.log(fields);

            $.ajax({
                    url  : Ultimate_DB_Manager_Data.ajaxurl,
                    type : 'POST',
                    dataType: 'json',
                    data : {
                        action       : 'ultimate_db_save_schedule',
                        fields_data  : fields,
                        _ajax_nonce  : Ultimate_DB_Manager_Data._ajax_nonce,
                    },
                    beforeSend: function() {

                        $('.ultimate-status-changes').html('<ion-icon name="reload-circle"></ion-icon></ion-icon>Saving');

                    },
                })
                .fail(function( jqXHR ){
                    console.log( jqXHR.status + ' ' + jqXHR.responseText);
                })
                .done(function ( options ) {
                    if( false === options.success ) {
                        console.log(options);
                    } else {
                        $( "input[name='schedule_id']" ).val(options.data);

                        // update campaign tag status
                        $('.ultimate-tag').html('published');
                        $('.ultimate-tag').removeClass('ultimate-tag-draft');
                        $('.ultimate-tag').addClass('ultimate-tag-published');

                        // update schedule save icon status
                        $('.ultimate-status-changes').html('<ion-icon class="ultimate-icon-saved" name="checkmark-circle"></ion-icon>Saved');

                        // update schedule button text
                        $('.schedule-save-text').text('unpublish');
                        $('.schedule-publish-text').text('update');

                        //update page url with schedule id
                        var schedule_url = Ultimate_DB_Manager_Data.wizard_url+ '&id=' + options.data;
                        window.history.replaceState('','',schedule_url);
                    }
                });

        },

        /**
         * Draft Schedule
         *
         */
        _draftSchedule: function( ) {

            var formdata = $('.ultimate-schedule-form').serializeArray();
            var fields = {};
            $(formdata ).each(function(index, obj){
                fields[obj.name] = obj.value;
            });
            fields['schedule_status'] = 'draft';
            fields['update_frequency'] = $('.range-slider__value').text();
            fields['update_frequency_unit'] = $('#ultimate-field-unit-button').val();
            fields['ultimate_post_status'] = $('#ultimate-post-status').val();

            fields['ultimate_backup_tables'] = $('#backup-select-tables').val();
            console.log(fields);

            $.ajax({
                    url  : Ultimate_DB_Manager_Data.ajaxurl,
                    type : 'POST',
                    dataType: 'json',
                    data : {
                        action       : 'ultimate_db_save_schedule',
                        fields_data  : fields,
                        _ajax_nonce  : Ultimate_DB_Manager_Data._ajax_nonce,
                    },
                    beforeSend: function() {

                        $('.ultimate-status-changes').html('<ion-icon name="reload-circle"></ion-icon></ion-icon>Saving');

                    },
                })
                .fail(function( jqXHR ){
                    console.log( jqXHR.status + ' ' + jqXHR.responseText);
                })
                .done(function ( options ) {
                    if( false === options.success ) {
                        console.log(options);
                    } else {
                        $( "input[name='schedule_id']" ).val(options.data);

                         // update campaign status
                         $('.ultimate-tag').html('draft');
                         $('.ultimate-tag').removeClass('ultimate-tag-published');
                         $('.ultimate-tag').addClass('ultimate-tag-draft');

                        // update schedule save icon status
                        $('.ultimate-status-changes').html('<ion-icon class="ultimate-icon-saved" name="checkmark-circle"></ion-icon>Saved');

                        // update schedule button text
                        $('.schedule-save-text').text('save draft');
                        $('.schedule-publish-text').text('publish');

                        //update page url with schedule id
                        var schedule_url = Ultimate_DB_Manager_Data.wizard_url+ '&id=' + options.data;
                        window.history.replaceState('','',schedule_url);
                    }
                });

        },


       
        
    };

    /**
     * Initialize UltimateDBScheduleEdit
     */
    $(function(){
        UltimateDBScheduleEdit.init();
    });

})(jQuery);