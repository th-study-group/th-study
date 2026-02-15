function copyFrom(sel, btn){
    const el = document.querySelector(sel);
    if(!el || !btn) return;
    const txt = el.innerText;
    const original = btn.textContent;
    navigator.clipboard.writeText(txt).then(()=>{
        btn.textContent = '복사됨';
        btn.disabled = true;
        setTimeout(()=>{
        btn.textContent = original;
        btn.disabled = false;
        }, 1300);
    }).catch(()=>{
        btn.textContent = '실패';
        setTimeout(()=>{ btn.textContent = original; }, 1300);
    });
}