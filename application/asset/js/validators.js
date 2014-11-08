function getInvalidFields(scope)
{
    var invalids = $(".required", scope).filter(function() {
        return this.value == "";
    });

    //controllo approvazione condizioni
    var chk = $('input[type="checkbox"].required', scope);
    if (chk.attr("checked") != "checked") {
        invalids = invalids.add(chk);
    }

    //controllo email
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    $('.mailinput', scope).each(function(index, el) {
        var mail = $(el);
        if (!re.test(mail.val())) {
            invalids = invalids.add(mail);
        }
    });

    return invalids;
}

$(function() {
    //$(".dateinput").datepicker({"autoSize": true, dateFormat: "dd/mm/yy"});
    $('input[type="submit"]').click(function(e) {
        if (!testFields($(this).closest("form")))
            e.preventDefault();
    });

    $('input').change(function() {
        $(this).removeClass('invalid')
    });
});

function testFields(scope)
{
    var invalids = getInvalidFields(scope);
    if (invalids.length === 0) {
        return true;
    }
    $(invalids[0]).focus();
    invalids.addClass('invalid');
    alert("Alcuni campi obbligatori non sono stati compilati.")
    return false;
}