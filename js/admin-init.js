/**
 * Created by moridrin on 5-12-16.
 */
jQuery(function ($) {
    $(document).ready(function () {
        var dateTimePickers = $('.datetimepicker');
        dateTimePickers.each(function () {
            var inline = $(this).attr('inline');
            var value = $(this).attr('value') ? $(this).attr('value') : 'now';
            $(this).datetimepicker({
                inline: inline === "true" || inline === "inline" || inline === "yes",
                mask: '9999-19-39 29:59',
                format: 'Y-m-d H:i',
                value: value
            });
        });
        var datePickers = $('.datepicker');
        datePickers.each(function () {
            var inline = $(this).attr('inline');
            var value = $(this).attr('value') ? $(this).attr('value') : 'now';
            $(this).datetimepicker({
                timepicker: false,
                inline: inline === "true" || inline === "inline" || inline === "yes",
                mask: '9999-19-39',
                format: 'Y-m-d',
                value: value
            });
        });
        var timePickers = $('.timepicker');
        timePickers.each(function () {
            var inline = $(this).attr('inline');
            var value = $(this).attr('value') ? $(this).attr('value') : 'now';
            $(this).datetimepicker({
                datepicker: false,
                inline: inline === "true" || inline === "inline" || inline === "yes",
                mask: '29:59',
                format: 'H:i',
                value: value
            });
        });
    });
});
