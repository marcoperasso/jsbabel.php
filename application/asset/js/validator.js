
function _jsbGetInvalidFields(scope)
{
    var invalids = jQuery(".required", scope).filter(function () {
        return this.value == "";
    });
    //controllo approvazione condizioni
    var chk = jQuery('input[type="checkbox"].required', scope);
    if (!chk.prop("checked")) {
        invalids = invalids.add(chk);
    }


    return invalids;
}

function _jsbResetError()
{
    var jGroup = jQuery(this);
    jGroup.off('change', _jsbResetError).removeClass('has-error');
    jQuery('.glyphicon', jGroup).removeClass('glyphicon-warning-sign');
}
function _jsbTestFields(scope)
{
    var invalids = _jsbGetInvalidFields(scope);
    if (invalids.length === 0) {
        return true;
    }
    jQuery(invalids[0]).focus();
    var jGroup = invalids.parents(".form-group");
    jGroup.addClass('has-error').on("change", _jsbResetError);
    jQuery('.glyphicon', jGroup).addClass('glyphicon-warning-sign');
    return false;
}