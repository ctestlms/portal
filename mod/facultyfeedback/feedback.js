function set_item_focus(itemid) {
    var item = document.getElementById(itemid);
    if(item){
        item.focus();
    }
}

function facultyfeedbackGo2delete(form) {
    form.action = M.cfg.wwwroot+'/mod/facultyfeedback/delete_completed.php';
    form.submit();
}

function setcourseitemfilter(item, item_typ) {
    document.report.courseitemfilter.value = item;
    document.report.courseitemfiltertyp.value = item_typ;
    document.report.submit();
}


M.mod_facultyfeedback = {};

M.mod_facultyfeedback.init_sendmessage = function(Y) {
    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', 'checked');
        });
    }, '#checkall');

    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', '');
        });
    }, '#checknone');
};
