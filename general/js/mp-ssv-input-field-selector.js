/**
 * Created by moridrin on 4-1-17.
 */

var custom_field_fields = settings.custom_field_fields;
var roles = JSON.parse(settings.roles);
var scripts = document.getElementsByTagName("script");
var pluginBaseURL = scripts[scripts.length - 1].src.split('/').slice(0, -3).join('/');

function mp_ssv_add_new_field(fieldType, inputType, fieldID, values, allowTabs) {
    if (typeof values === 'undefined' || values === null) {
        values = [];
    }
    if (fieldType === 'tab') {
        getTabField(fieldID, values, allowTabs);
    } else if (fieldType === 'header') {
        getHeaderField(fieldID, values, allowTabs);
    } else if (fieldType === 'input') {
        if (inputType === 'text') {
            getTextInputField(fieldID, values, allowTabs);
        } else if (inputType === 'select') {
            getSelectInputField(fieldID, values, allowTabs);
        } else if (inputType === 'checkbox') {
            getCheckboxInputField(fieldID, values, allowTabs);
        } else if (inputType === 'role_checkbox') {
            getRoleCheckboxInputField(fieldID, values, allowTabs);
        } else if (inputType === 'role_select') {
            getRoleSelectInputField(fieldID, values, allowTabs);
        } else if (inputType === 'date') {
            getDateInputField(fieldID, values, allowTabs);
        } else if (inputType === 'image') {
            getImageInputField(fieldID, values, allowTabs);
        } else if (inputType === 'hidden') {
            getHiddenInputField(fieldID, values, allowTabs);
        } else {
            getCustomInputField(inputType, fieldID, values, allowTabs);
        }
    } else if (fieldType === 'label') {
        getLabelField(fieldID, values, allowTabs);
    }
}

function getTabField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    var fieldTitle = '';
    var fields = {};
    var fieldType = 'tab';
    if (typeof values['title'] !== 'undefined') {
        fieldTitle = values['title'];
    }
    if (typeof values['fields'] !== 'undefined') {
        fields = values['fields'];
    }

    var startTR = document.createElement("tr");
    startTR.setAttribute("id", fieldID + "_tr");
    startTR.setAttribute("id", fieldID + "_tab_end");
    var startLabel = document.createTextNode("Start of Tab");
    startTR.appendChild(getStart(fieldID, true));
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(startLabel);
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(getEmpty(fieldID));
    startTR.appendChild(getEnd(fieldID));
    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getClass(fieldID, ""));
    tr.appendChild(getStyle(fieldID, ""));
    tr.appendChild(getEnd(fieldID));
    var endTR = document.createElement("tr");
    endTR.setAttribute("id", fieldID + "_tr");
    endTR.setAttribute("id", fieldID + "_tab_end");
    var endLabel = document.createTextNode("End of Tab");
    endTR.appendChild(getStart(fieldID, true));
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(endLabel);
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(getEmpty(fieldID));
    endTR.appendChild(getEnd(fieldID));

    container.appendChild(tr);
    // container.appendChild(startTR);
    for (var i in fields) {
        // alert(JSON.stringify(fields[i]));
        mp_ssv_add_new_field(fields[i]['field_type'], fields[i]['input_type'], fields[i]['id'], fields[i], allowTabs);
    }
    // container.appendChild(endTR);
}
function getHeaderField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    var fieldTitle = '';
    var fieldType = 'header';
    var classValue = '';
    var style = '';

    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));

    container.appendChild(tr);
}
function getTextInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var required = false;
    var disabled = false;
    var defaultValue = '';
    var placeholder = '';
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        required = values['required'];
        disabled = values['disabled'];
        defaultValue = values['default_value'];
        placeholder = values['placeholder'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getTextInputFields(tr, fieldID, name, required, disabled, defaultValue, placeholder, classValue, style);
    container.appendChild(tr);
}
function getSelectInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var options = '';
    var disabled = false;
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        options = values['options'];
        disabled = values['disabled'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getSelectInputFields(tr, fieldID, name, options, disabled, classValue, style);
    container.appendChild(tr);
}
function getCheckboxInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");
    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var required = false;
    var disabled = false;
    var defaultChecked = '';
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        required = values['required'];
        disabled = values['disabled'];
        defaultChecked = values['default_checked'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getCheckboxInputFields(tr, fieldID, name, required, disabled, defaultChecked, classValue, style);
    container.appendChild(tr);
}
function getRoleCheckboxInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");
    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getRoleCheckboxInputFields(tr, fieldID, name, classValue, style);
    container.appendChild(tr);
}
function getRoleSelectInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");
    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        options = values['options'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getRoleSelectInputFields(tr, fieldID, name, options, classValue, style);
    container.appendChild(tr);
}
function getImageInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var required = false;
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        required = values['required'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getImageInputFields(tr, fieldID, name, required, classValue, style);
    container.appendChild(tr);
}
function getHiddenInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var defaultValue = '';
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        defaultValue = values['default_value'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getHiddenInputFields(tr, fieldID, name, defaultValue, classValue, style);
    container.appendChild(tr);
}
function getCustomInputField(inputType, fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var required = false;
    var disabled = false;
    var defaultValue = '';
    var placeholder = '';
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        required = values['required'];
        disabled = values['disabled'];
        defaultValue = values['default_value'];
        placeholder = values['placeholder'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getCustomInputFields(tr, fieldID, inputType, name, required, disabled, defaultValue, placeholder, classValue, style);
    container.appendChild(tr);
}
function getDateInputField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldTitle = '';
    var fieldType = 'input';
    var name = '';
    var required = false;
    var disabled = false;
    var defaultValue = '';
    var dateRangeAfter = '';
    var dateRangeBefore = '';
    var classValue = '';
    var style = '';
    if (Object.keys(values).length > 1) {
        fieldTitle = values['title'];
        name = values['name'];
        required = values['required'];
        disabled = values['disabled'];
        defaultValue = values['default_value'];
        dateRangeAfter = values['date_range_after'];
        dateRangeBefore = values['date_range_before'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr = getDateInputFields(tr, fieldID, name, required, disabled, defaultValue, dateRangeAfter, dateRangeBefore, classValue, style);
    container.appendChild(tr);
}
function getLabelField(fieldID, values, allowTabs) {
    var container = document.getElementById("custom-fields-placeholder");

    //noinspection JSUnusedLocalSymbols
    var overrideRight = values['override_right'];
    var fieldType = 'label';
    var fieldTitle = '';
    var text = '';
    var classValue = '';
    var style = '';
    if (typeof values !== 'undefined') {
        fieldTitle = values['title'];
        text = values['text'];
        classValue = values['class'];
        style = values['style'];
    }

    var tr = getBaseFields(fieldID, fieldTitle, fieldType, allowTabs);
    tr.appendChild(getText(fieldID, text));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));

    container.appendChild(tr);
}

function getBaseFields(fieldID, fieldTitle, fieldType, allowTabs) {
    var tr = document.createElement("tr");
    tr.setAttribute("id", fieldID + "_tr");
    tr.appendChild(getStart(fieldID));
    tr.appendChild(getFieldID(fieldID));
    tr.appendChild(getDraggable(fieldID));
    tr.appendChild(getFieldTitle(fieldID, fieldTitle));
    tr.appendChild(getFieldType(fieldID, fieldType, allowTabs));
    return tr;
}
function getTextInputFields(tr, fieldID, name, required, disabled, defaultValue, placeholder, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'text'));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getDisabled(fieldID, disabled));
    tr.appendChild(getRequired(fieldID, required));
    tr.appendChild(getDefaultValue(fieldID, defaultValue));
    tr.appendChild(getPlaceholder(fieldID, placeholder));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getSelectInputFields(tr, fieldID, name, options, disabled, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'select'));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getDisabled(fieldID, disabled));
    tr.appendChild(getOptions(fieldID, options));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getCheckboxInputFields(tr, fieldID, name, required, disabled, defaultChecked, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'checkbox'));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getDisabled(fieldID, disabled));
    tr.appendChild(getRequired(fieldID, required));
    tr.appendChild(getDefaultSelected(fieldID, defaultChecked));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getRoleCheckboxInputFields(tr, fieldID, role, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'role_checkbox'));
    tr.appendChild(getRoleCheckbox(fieldID, role));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getRoleSelectInputFields(tr, fieldID, name, role, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'role_select'));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getRoleSelect(fieldID, role));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getImageInputFields(tr, fieldID, name, required, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'image'));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getPreview(fieldID, required));
    tr.appendChild(getRequired(fieldID, required));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getHiddenInputFields(tr, fieldID, name, defaultValue, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'hidden'));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getDefaultValue(fieldID, defaultValue));
    tr.appendChild(getEmpty(fieldID));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getCustomInputFields(tr, fieldID, inputType, name, required, disabled, defaultValue, placeholder, classValue, style) {
    tr.appendChild(getInputType(fieldID, inputType));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getDisabled(fieldID, disabled));
    tr.appendChild(getRequired(fieldID, required));
    tr.appendChild(getDefaultValue(fieldID, defaultValue));
    tr.appendChild(getPlaceholder(fieldID, placeholder));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}
function getDateInputFields(tr, fieldID, name, required, disabled, defaultValue, dateRangeAfter, dateRangeBefore, classValue, style) {
    tr.appendChild(getInputType(fieldID, 'date'));
    tr.appendChild(getName(fieldID, name));
    tr.appendChild(getDisabled(fieldID, disabled));
    tr.appendChild(getRequired(fieldID, required));
    tr.appendChild(getDefaultValue(fieldID, defaultValue, 'yyyy-mm-dd'));
    tr.appendChild(getDateRange(fieldID, dateRangeAfter, dateRangeBefore));
    tr.appendChild(getClass(fieldID, classValue));
    tr.appendChild(getStyle(fieldID, style));
    tr.appendChild(getEnd(fieldID));
    return tr;
}

function getBR() {
    var br = document.createElement("div");
    br.innerHTML = '<br/>';
    return br.childNodes[0];
}
function getEmpty(fieldID) {
    var td = document.createElement("td");
    td.setAttribute("class", fieldID + "_empty_td");
    return td;
}
function getStart(fieldID, isTab) {
    var start = document.createElement("input");
    start.setAttribute("type", "hidden");
    start.setAttribute("id", fieldID + "_start");
    start.setAttribute("name", "custom_field_" + fieldID + "_start");
    start.setAttribute("value", "start");
    var startTD = document.createElement("td");
    if (isTab) {
        startTD.setAttribute("style", "border-left: solid;");
    }
    startTD.setAttribute("id", fieldID + "_start_td");
    startTD.appendChild(start);
    return startTD;
}
function getFieldID(fieldID) {
    var fieldIDElement = document.createElement("input");
    fieldIDElement.setAttribute("type", "hidden");
    fieldIDElement.setAttribute("id", fieldID + "_id");
    fieldIDElement.setAttribute("name", "custom_field_" + fieldID + "_id");
    fieldIDElement.setAttribute("value", fieldID);
    var fieldIDTD = document.createElement("td");
    fieldIDTD.setAttribute("id", fieldID + "_id_td");
    fieldIDTD.appendChild(fieldIDElement);
    return fieldIDTD;
}
function getDraggable(fieldID) {
    var draggableIcon = document.createElement("img");
    draggableIcon.setAttribute("src", pluginBaseURL + '/general/images/icon-menu.svg');
    draggableIcon.setAttribute("style", "padding-right: 15px; margin: 10px 0;");
    var draggableIconTD = document.createElement("td");
    draggableIconTD.setAttribute("id", fieldID + "_draggable_td");
    draggableIconTD.setAttribute("style", "vertical-align: middle; cursor: move;");
    draggableIconTD.appendChild(draggableIcon);
    return draggableIconTD;
}
function getFieldTitle(fieldID, value) {
    var fieldTitle = document.createElement("input");
    fieldTitle.setAttribute("id", fieldID + "_title");
    fieldTitle.setAttribute("name", "custom_field_" + fieldID + "_title");
    fieldTitle.setAttribute("style", "width: 100%;");
    if (value) {
        fieldTitle.setAttribute("value", value);
    }
    var fieldTitleLabel = document.createElement("label");
    fieldTitleLabel.setAttribute("style", "white-space: nowrap;");
    fieldTitleLabel.setAttribute("for", fieldID + "_field_title");
    fieldTitleLabel.innerHTML = "Field Title";
    var fieldTitleTD = document.createElement("td");
    fieldTitleTD.setAttribute("id", fieldID + "_field_title_td");
    fieldTitleTD.appendChild(fieldTitleLabel);
    fieldTitleTD.appendChild(getBR());
    fieldTitleTD.appendChild(fieldTitle);
    return fieldTitleTD;
}
function getFieldType(fieldID, value, allowTabs) {
    var options;
    if (allowTabs) {
        options = ["Tab", "Header", "Input", "Label"];
    } else {
        options = ["Header", "Input", "Label"];
    }
    var fieldType = createSelect(fieldID, "_field_type", options, value);
    fieldType.setAttribute("style", "width: 100%;");
    fieldType.onchange = function () {
        fieldTypeChanged(fieldID);
    };
    var fieldTypeLabel = document.createElement("label");
    fieldTypeLabel.setAttribute("style", "white-space: nowrap;");
    fieldTypeLabel.setAttribute("for", fieldID + "_field_type");
    fieldTypeLabel.innerHTML = "Field Type";
    var fieldTypeTD = document.createElement("td");
    fieldTypeTD.setAttribute("id", fieldID + "_field_type_td");
    fieldTypeTD.appendChild(fieldTypeLabel);
    fieldTypeTD.appendChild(getBR());
    fieldTypeTD.appendChild(fieldType);
    return fieldTypeTD;
}
function getText(fieldID, value) {
    var fieldTitle = document.createElement("textarea");
    fieldTitle.setAttribute("id", fieldID + "_text");
    fieldTitle.setAttribute("name", "custom_field_" + fieldID + "_text");
    fieldTitle.setAttribute("style", "width: 100%;");
    fieldTitle.innerHTML = value;
    var fieldTitleLabel = document.createElement("label");
    fieldTitleLabel.setAttribute("style", "white-space: nowrap;");
    fieldTitleLabel.setAttribute("for", fieldID + "_text");
    fieldTitleLabel.innerHTML = "Text";
    var fieldTitleTD = document.createElement("td");
    fieldTitleTD.setAttribute("id", fieldID + "_text_td");
    var colspan = "6";
    fieldTitleTD.setAttribute("colspan", colspan);
    fieldTitleTD.appendChild(fieldTitleLabel);
    fieldTitleTD.appendChild(getBR());
    fieldTitleTD.appendChild(fieldTitle);
    return fieldTitleTD;
}
function getInputType(fieldID, value) {
    var options = ["Text", "Select", "Checkbox", "Role_Checkbox", "Role_Select", "Date", "Image", "Hidden", "Custom"];
    var customValue = '';
    if (["text", "select", "checkbox", "role_checkbox", "role_select", "date", "image", "hidden", "custom"].indexOf(value) === -1) {
        customValue = value;
        value = 'custom';
    }
    var inputType = createSelect(fieldID, "_input_type", options, value);
    if (value === 'custom') {
        inputType.setAttribute("style", "width: 48%;");
    } else {
        inputType.setAttribute("style", "width: 100%;");
    }
    inputType.onchange = function () {
        inputTypeChanged(fieldID);
    };
    var inputTypeLabel = document.createElement("label");
    inputTypeLabel.setAttribute("style", "white-space: nowrap;");
    inputTypeLabel.setAttribute("for", fieldID + "_input_type");
    inputTypeLabel.innerHTML = "Input Type";
    var inputTypeTD = document.createElement("td");
    inputTypeTD.setAttribute("id", fieldID + "_input_type_td");
    inputTypeTD.appendChild(inputTypeLabel);
    inputTypeTD.appendChild(getBR());
    inputTypeTD.appendChild(inputType);
    if (value === 'custom') {
        var inputTypeCustom = document.createElement("input");
        inputTypeCustom.setAttribute("id", fieldID + "_input_type");
        inputTypeCustom.setAttribute("name", "custom_field_" + fieldID + "_input_type");
        inputTypeCustom.setAttribute("style", "width: 50%;");
        inputTypeCustom.setAttribute("value", customValue);
        inputTypeCustom.setAttribute("required", "required");
        inputTypeTD.appendChild(inputTypeCustom);
    }
    return inputTypeTD;
}
function getName(fieldID, value) {
    var name = document.createElement("input");
    name.setAttribute("id", fieldID + "_name");
    name.setAttribute("name", "custom_field_" + fieldID + "_name");
    name.setAttribute("style", "width: 100%;");
    if (value) {
        name.setAttribute("value", value);
    }
    name.setAttribute("required", "required");
    var nameLabel = document.createElement("label");
    nameLabel.setAttribute("style", "white-space: nowrap;");
    nameLabel.setAttribute("for", fieldID + "_name");
    nameLabel.innerHTML = "Name";
    var nameTD = document.createElement("td");
    nameTD.setAttribute("id", fieldID + "_name_td");
    nameTD.appendChild(nameLabel);
    nameTD.appendChild(getBR());
    nameTD.appendChild(name);
    return nameTD;
}
function getRoleCheckbox(fieldID, value) {
    var inputType = createSelect(fieldID, "_name", roles, value);
    inputType.setAttribute("style", "width: 100%;");
    var inputTypeLabel = document.createElement("label");
    inputTypeLabel.setAttribute("style", "white-space: nowrap;");
    inputTypeLabel.setAttribute("for", fieldID + "_name");
    inputTypeLabel.innerHTML = "Role";
    var inputTypeTD = document.createElement("td");
    inputTypeTD.setAttribute("id", fieldID + "_name_td");
    inputTypeTD.appendChild(inputTypeLabel);
    inputTypeTD.appendChild(getBR());
    inputTypeTD.appendChild(inputType);
    return inputTypeTD;
}
function getRoleSelect(fieldID, value) {
    var inputType = createMultiSelect(fieldID, "_options", roles, value);
    inputType.setAttribute("style", "width: 100%;");
    var inputTypeLabel = document.createElement("label");
    inputTypeLabel.setAttribute("style", "white-space: nowrap;");
    inputTypeLabel.setAttribute("for", fieldID + "_options");
    inputTypeLabel.innerHTML = "Role";
    var inputTypeTD = document.createElement("td");
    inputTypeTD.setAttribute("id", fieldID + "_options_td");
    inputTypeTD.appendChild(inputTypeLabel);
    inputTypeTD.appendChild(getBR());
    inputTypeTD.appendChild(inputType);
    return inputTypeTD;
}
function getRequired(fieldID, value) {
    var required = document.createElement("input");
    required.setAttribute("type", "checkbox");
    required.setAttribute("id", fieldID + "_required");
    required.setAttribute("name", "custom_field_" + fieldID + "_required");
    required.setAttribute("value", "true");
    if (value) {
        required.setAttribute("checked", "checked");
    }
    var requiredReset = document.createElement("input");
    requiredReset.setAttribute("type", "hidden");
    requiredReset.setAttribute("id", fieldID + "_required");
    requiredReset.setAttribute("name", "custom_field_" + fieldID + "_required");
    requiredReset.setAttribute("value", "false");
    var requiredLabel = document.createElement("label");
    requiredLabel.setAttribute("style", "white-space: nowrap;");
    requiredLabel.setAttribute("for", fieldID + "_required");
    requiredLabel.innerHTML = "Required";
    var requiredTD = document.createElement("td");
    requiredTD.setAttribute("id", fieldID + "_required_td");
    requiredTD.appendChild(requiredLabel);
    requiredTD.appendChild(getBR());
    requiredTD.appendChild(requiredReset);
    requiredTD.appendChild(required);
    return requiredTD;
}
function getPreview(fieldID, value) {
    var preview = document.createElement("input");
    preview.setAttribute("type", "checkbox");
    preview.setAttribute("id", fieldID + "_preview");
    preview.setAttribute("name", "custom_field_" + fieldID + "_preview");
    preview.setAttribute("value", "true");
    if (value) {
        preview.setAttribute("checked", "checked");
    }
    var previewReset = document.createElement("input");
    previewReset.setAttribute("type", "hidden");
    previewReset.setAttribute("id", fieldID + "_preview");
    previewReset.setAttribute("name", "custom_field_" + fieldID + "_preview");
    previewReset.setAttribute("value", "false");
    var previewLabel = document.createElement("label");
    previewLabel.setAttribute("style", "white-space: nowrap;");
    previewLabel.setAttribute("for", fieldID + "_preview");
    previewLabel.innerHTML = "Preview";
    var previewTD = document.createElement("td");
    previewTD.setAttribute("id", fieldID + "_preview_td");
    previewTD.appendChild(previewLabel);
    previewTD.appendChild(getBR());
    previewTD.appendChild(previewReset);
    previewTD.appendChild(preview);
    return previewTD;
}
function getDisabled(fieldID, value) {
    var disabled = document.createElement("input");
    disabled.setAttribute("type", "checkbox");
    disabled.setAttribute("id", fieldID + "_disabled");
    disabled.setAttribute("name", "custom_field_" + fieldID + "_disabled");
    disabled.setAttribute("value", "true");
    if (value) {
        disabled.setAttribute("checked", "checked");
    }
    var disabledReset = document.createElement("input");
    disabledReset.setAttribute("type", "hidden");
    disabledReset.setAttribute("id", fieldID + "_disabled");
    disabledReset.setAttribute("name", "custom_field_" + fieldID + "_disabled");
    disabledReset.setAttribute("value", "false");
    var disabledLabel = document.createElement("label");
    disabledLabel.setAttribute("style", "white-space: nowrap;");
    disabledLabel.setAttribute("for", fieldID + "_disabled");
    disabledLabel.innerHTML = "Disabled";
    var disabledTD = document.createElement("td");
    disabledTD.setAttribute("id", fieldID + "_disabled_td");
    disabledTD.appendChild(disabledLabel);
    disabledTD.appendChild(getBR());
    disabledTD.appendChild(disabledReset);
    disabledTD.appendChild(disabled);
    return disabledTD;
}
function getOptions(fieldID, value) {
    var options = document.createElement("input");
    options.setAttribute("id", fieldID + "_options");
    options.setAttribute("name", "custom_field_" + fieldID + "_options");
    options.setAttribute("style", "width: 100%;");
    if (value) {
        options.setAttribute("value", value);
    }
    options.setAttribute("required", "required");
    options.setAttribute("placeholder", "Separate with ','");
    var optionsLabel = document.createElement("label");
    optionsLabel.setAttribute("style", "white-space: nowrap;");
    optionsLabel.setAttribute("for", fieldID + "_options");
    optionsLabel.innerHTML = "Options";
    var nameTD = document.createElement("td");
    nameTD.setAttribute("id", fieldID + "_options_td");
    nameTD.setAttribute("colspan", "3");
    nameTD.appendChild(optionsLabel);
    nameTD.appendChild(getBR());
    nameTD.appendChild(options);
    return nameTD;
}
function getDefaultValue(fieldID, value, placeholder) {
    var defaultValue = document.createElement("input");
    var show = custom_field_fields.indexOf('default') !== -1;
    if (!show) {
        defaultValue.setAttribute("type", "hidden");
    }
    defaultValue.setAttribute("id", fieldID + "_default_value");
    defaultValue.setAttribute("name", "custom_field_" + fieldID + "_default_value");
    defaultValue.setAttribute("style", "width: 100%;");
    if (placeholder) {
        defaultValue.setAttribute("placeholder", placeholder);
    }
    if (value) {
        defaultValue.setAttribute("value", value);
    }
    var defaultValueTD = document.createElement("td");
    defaultValueTD.setAttribute("id", fieldID + "_default_value_td");
    if (show) {
        var defaultValueLabel = document.createElement("label");
        defaultValueLabel.setAttribute("style", "white-space: nowrap;");
        defaultValueLabel.setAttribute("for", fieldID + "_default_value");
        defaultValueLabel.innerHTML = "Default Value";
        defaultValueTD.appendChild(defaultValueLabel);
        defaultValueTD.appendChild(getBR());
    }
    defaultValueTD.appendChild(defaultValue);
    return defaultValueTD;
}
function getDefaultSelected(fieldID, value) {
    var defaultSelected = document.createElement("input");
    var show = custom_field_fields.indexOf('default') !== -1;
    if (!show) {
        defaultSelected.setAttribute("type", "hidden");
    } else {
        defaultSelected.setAttribute("type", "checkbox");
    }
    defaultSelected.setAttribute("id", fieldID + "_default_checked");
    defaultSelected.setAttribute("name", "custom_field_" + fieldID + "_default_checked");
    defaultSelected.setAttribute("value", "true");
    if (value) {
        defaultSelected.setAttribute("checked", "checked");
    }
    var defaultSelectedReset = document.createElement("input");
    defaultSelectedReset.setAttribute("type", "hidden");
    defaultSelectedReset.setAttribute("id", fieldID + "_default_checked");
    defaultSelectedReset.setAttribute("name", "custom_field_" + fieldID + "_default_checked");
    defaultSelectedReset.setAttribute("value", "false");
    var requiredTD = document.createElement("td");
    requiredTD.setAttribute("id", fieldID + "_default_checked_td");
    if (show) {
        var requiredLabel = document.createElement("label");
        requiredLabel.setAttribute("style", "white-space: nowrap;");
        requiredLabel.setAttribute("for", fieldID + "_default_checked");
        requiredLabel.innerHTML = "Default Selected";
        requiredTD.appendChild(requiredLabel);
        requiredTD.appendChild(getBR());
    }
    requiredTD.appendChild(defaultSelectedReset);
    requiredTD.appendChild(defaultSelected);
    return requiredTD;
}
function getPlaceholder(fieldID, value) {
    var placeholder = document.createElement("input");
    var show = custom_field_fields.indexOf('placeholder') !== -1;
    if (!show) {
        placeholder.setAttribute("type", "hidden");
    }
    placeholder.setAttribute("id", fieldID + "_placeholder");
    placeholder.setAttribute("name", "custom_field_" + fieldID + "_placeholder");
    placeholder.setAttribute("style", "width: 100%;");
    if (value) {
        placeholder.setAttribute("value", value);
    }
    var placeholderTD = document.createElement("td");
    placeholderTD.setAttribute("id", fieldID + "_placeholder_td");
    if (show) {
        var placeholderLabel = document.createElement("label");
        placeholderLabel.setAttribute("style", "white-space: nowrap;");
        placeholderLabel.setAttribute("for", fieldID + "_placeholder");
        placeholderLabel.innerHTML = "Placeholder";
        placeholderTD.appendChild(placeholderLabel);
        placeholderTD.appendChild(getBR());
    }
    placeholderTD.appendChild(placeholder);
    return placeholderTD;
}
function getDateRange(fieldID, valueAfter, valueBefore) {
    var dateRangeAfter = document.createElement("input");
    var dateRangeBefore = document.createElement("input");
    var show = custom_field_fields.indexOf('placeholder') !== -1;
    if (!show) {
        dateRangeAfter.setAttribute("type", "hidden");
        dateRangeBefore.setAttribute("type", "hidden");
    }
    dateRangeAfter.setAttribute("id", fieldID + "_date_range_after");
    dateRangeBefore.setAttribute("id", fieldID + "_date_range_before");
    dateRangeAfter.setAttribute("name", "custom_field_" + fieldID + "_date_range_after");
    dateRangeBefore.setAttribute("name", "custom_field_" + fieldID + "_date_range_before");
    dateRangeAfter.setAttribute("style", "width: 49%;");
    dateRangeBefore.setAttribute("style", "width: 49%;");
    dateRangeAfter.setAttribute("placeholder", "yyyy-mm-dd");
    dateRangeBefore.setAttribute("placeholder", "yyyy-mm-dd");
    if (valueAfter) {
        dateRangeAfter.setAttribute("value", valueAfter);
    }
    if (valueBefore) {
        dateRangeBefore.setAttribute("value", valueBefore);
    }
    var dateRangeTD = document.createElement("td");
    dateRangeTD.setAttribute("id", fieldID + "_date_range_td");
    if (show) {
        var dateRangeLabel = document.createElement("label");
        dateRangeLabel.setAttribute("style", "white-space: nowrap;");
        dateRangeLabel.setAttribute("for", fieldID + "_date_range");
        dateRangeLabel.innerHTML = "Range";
        dateRangeTD.appendChild(dateRangeLabel);
        dateRangeTD.appendChild(getBR());
    }
    dateRangeTD.appendChild(dateRangeAfter);
    dateRangeTD.appendChild(dateRangeBefore);
    return dateRangeTD;
}
function getClass(fieldID, value) {
    var classField = document.createElement("input");
    var show = custom_field_fields.indexOf('class') !== -1;
    if (!show) {
        classField.setAttribute("type", "hidden");
    }
    classField.setAttribute("id", fieldID + "_class");
    classField.setAttribute("name", "custom_field_" + fieldID + "_class");
    classField.setAttribute("style", "width: 100%;");
    if (value) {
        classField.setAttribute("value", value);
    }
    var classTD = document.createElement("td");
    classTD.setAttribute("id", fieldID + "_class_td");
    if (show) {
        var classLabel = document.createElement("label");
        classLabel.setAttribute("style", "white-space: nowrap;");
        classLabel.setAttribute("for", fieldID + "_class");
        classLabel.innerHTML = "Class";
        classTD.appendChild(classLabel);
        classTD.appendChild(getBR());
    }
    classTD.appendChild(classField);
    return classTD;
}
function getStyle(fieldID, value) {
    var style = document.createElement("input");
    var show = custom_field_fields.indexOf('style') !== -1;
    if (!show) {
        style.setAttribute("type", "hidden");
    }
    style.setAttribute("id", fieldID + "_style");
    style.setAttribute("name", "custom_field_" + fieldID + "_style");
    style.setAttribute("style", "width: 100%;");
    if (value) {
        style.setAttribute("value", value);
    }
    var styleTD = document.createElement("td");
    styleTD.setAttribute("id", fieldID + "_style_td");
    if (show) {
        var styleLabel = document.createElement("label");
        styleLabel.setAttribute("style", "white-space: nowrap;");
        styleLabel.setAttribute("for", fieldID + "_style");
        styleLabel.innerHTML = "Style";
        styleTD.appendChild(styleLabel);
        styleTD.appendChild(getBR());
    }
    styleTD.appendChild(style);
    return styleTD;
}
function getEnd(fieldID, isTab) {
    var stop = document.createElement("input");
    stop.setAttribute("type", "hidden");
    stop.setAttribute("id", fieldID + "_end");
    stop.setAttribute("name", "custom_field_" + fieldID + "_end");
    stop.setAttribute("value", "end");
    var stopTD = document.createElement("td");
    if (isTab) {
        stopTD.setAttribute("style", "border-right: solid;");
    }
    stopTD.setAttribute("id", fieldID + "_end_td");
    stopTD.appendChild(stop);
    return stopTD;
}

function fieldTypeChanged(fieldID) {
    var tr = document.getElementById(fieldID + "_tr");
    var fieldType = document.getElementById(fieldID + "_field_type").value;
    removeField(document.getElementById(fieldID + "_text_td"));
    removeField(document.getElementById(fieldID + "_input_type_td"));
    removeField(document.getElementById(fieldID + "_name_td"));
    removeField(document.getElementById(fieldID + "_preview_td"));
    removeField(document.getElementById(fieldID + "_required_td"));
    removeField(document.getElementById(fieldID + "_options_td"));
    removeField(document.getElementById(fieldID + "_disabled_td"));
    removeField(document.getElementById(fieldID + "_default_value_td"));
    removeField(document.getElementById(fieldID + "_default_checked_td"));
    removeField(document.getElementById(fieldID + "_date_range_td"));
    removeField(document.getElementById(fieldID + "_placeholder_td"));
    removeField(document.getElementById(fieldID + "_class_td"));
    removeField(document.getElementById(fieldID + "_style_td"));
    removeField(document.getElementById(fieldID + "_end_td"));
    removeFields(document.getElementsByClassName(fieldID + "_empty_td"));
    if (fieldType === 'input') {
        tr.appendChild(getInputType(fieldID, ""));
        tr.appendChild(getName(fieldID, ""));
        tr.appendChild(getDisabled(fieldID, ""));
        tr.appendChild(getRequired(fieldID, ""));
        tr.appendChild(getDefaultValue(fieldID, ""));
        tr.appendChild(getPlaceholder(fieldID, ""));
        tr.appendChild(getClass(fieldID, ""));
        tr.appendChild(getStyle(fieldID, ""));
        tr.appendChild(getEnd(fieldID));
    } else if (fieldType === 'label') {
        tr.appendChild(getText(fieldID, ""));
        tr.appendChild(getClass(fieldID, ""));
        tr.appendChild(getStyle(fieldID, ""));
        tr.appendChild(getEnd(fieldID));
    } else {
        tr.appendChild(getEmpty(fieldID));
        tr.appendChild(getEmpty(fieldID));
        tr.appendChild(getEmpty(fieldID));
        tr.appendChild(getEmpty(fieldID));
        tr.appendChild(getEmpty(fieldID));
        tr.appendChild(getEmpty(fieldID));
        tr.appendChild(getClass(fieldID, ""));
        tr.appendChild(getStyle(fieldID, ""));
        tr.appendChild(getEnd(fieldID));
    }
}
function inputTypeChanged(fieldID) {
    var tr = document.getElementById(fieldID + "_tr");
    var inputType = document.getElementById(fieldID + "_input_type").value;
    removeField(document.getElementById(fieldID + "_input_type_td"));
    removeField(document.getElementById(fieldID + "_name_td"));
    removeField(document.getElementById(fieldID + "_preview_td"));
    removeField(document.getElementById(fieldID + "_required_td"));
    removeField(document.getElementById(fieldID + "_options_td"));
    removeField(document.getElementById(fieldID + "_disabled_td"));
    removeField(document.getElementById(fieldID + "_default_value_td"));
    removeField(document.getElementById(fieldID + "_default_checked_td"));
    removeField(document.getElementById(fieldID + "_date_range_td"));
    removeField(document.getElementById(fieldID + "_placeholder_td"));
    removeField(document.getElementById(fieldID + "_class_td"));
    removeField(document.getElementById(fieldID + "_style_td"));
    removeField(document.getElementById(fieldID + "_end_td"));
    removeFields(document.getElementsByClassName(fieldID + "_empty_td"));
    if (inputType === 'text') {
        getTextInputFields(tr, fieldID, "", "", "", "", "", "", "");
    } else if (inputType === 'select') {
        getSelectInputFields(tr, fieldID, "", "", "", "", "");
    } else if (inputType === 'checkbox') {
        getCheckboxInputFields(tr, fieldID, "", "", "", "", "", "")
    } else if (inputType === 'date') {
        getDateInputFields(tr, fieldID, "", "", "", "", "", "", "");
    } else if (inputType === 'role_checkbox') {
        getRoleCheckboxInputFields(tr, fieldID, "", "", "")
    } else if (inputType === 'role_select') {
        getRoleSelectInputFields(tr, fieldID, "", "", "", "")
    } else if (inputType === 'image') {
        getImageInputFields(tr, fieldID, "", "", "", "");
    } else if (inputType === 'hidden') {
        getHiddenInputFields(tr, fieldID, "", "", "", "");
    } else {
        getCustomInputFields(tr, fieldID, "", "", "", "", "", "", "");
    }
}

function createSelect(fieldID, fieldNameExtension, options, selected) {
    var select = document.createElement("select");
    select.setAttribute("id", fieldID + fieldNameExtension);
    select.setAttribute("name", "custom_field_" + fieldID + fieldNameExtension);

    for (var i = 0; i < options.length; i++) {
        var option = document.createElement("option");
        option.setAttribute("value", options[i].toLowerCase());
        if (options[i].toLowerCase() === selected) {
            option.setAttribute("selected", "selected");
        }
        option.innerHTML = options[i];
        select.appendChild(option);
    }

    return select;
}
function createMultiSelect(fieldID, fieldNameExtension, options, selected) {
    if (selected === null) {
        selected = [];
    }
    var select = document.createElement("select");
    select.setAttribute("id", fieldID + fieldNameExtension);
    select.setAttribute("name", "custom_field_" + fieldID + fieldNameExtension + "[]");
    select.setAttribute("multiple", "multiple");

    for (var i = 0; i < options.length; i++) {
        var option = document.createElement("option");
        option.setAttribute("value", options[i].toLowerCase());
        if (jQuery.inArray(options[i].toLowerCase(), selected) !== -1) {
            option.setAttribute("selected", "selected");
        }
        option.innerHTML = options[i];
        select.appendChild(option);
    }

    return select;
}
function removeFields(fields) {
    if (fields !== null) {
        while (fields.length > 0) {
            removeField(fields[0]);
        }
    }
}
function removeField(field) {
    if (field !== null) {
        field.parentElement.removeChild(field);
    }
}
