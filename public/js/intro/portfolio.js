function copyFrom(sel, btn){
    const el = document.querySelector(sel);
    if(!el || !btn) return;
    const txt = el.innerText;
    initCopyButtons(btn, { getText: function () { return txt; }, trackConversion: false });
    $(btn).trigger('click');
}
