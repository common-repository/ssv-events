/**
 * Created by moridrin on 4-1-17.
 */

function mp_ssv_sortable_table(id) {
    var $ = jQuery.noConflict();
    var sortable = $("#" + id);
    sortable.sortable();
    sortable.disableSelection();
}
