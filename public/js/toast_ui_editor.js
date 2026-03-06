function resolveToastEditorElement(selectorOrElement) {
    if (!selectorOrElement) {
        return null;
    }

    if (typeof selectorOrElement === 'string') {
        return document.querySelector(selectorOrElement);
    }

    return selectorOrElement;
}

function getToastEditorDefaultToolbarItems() {
    return [
        ['heading', 'bold', 'italic', 'strike'],
        ['hr', 'quote'],
        ['ul', 'ol', 'task'],
        ['link'],
        ['code', 'codeblock']
    ];
}

function looksLikeHtmlContent(value) {
    if (typeof value !== 'string') {
        return false;
    }

    return /<\s*\/?\s*(p|br|strong|b|em|i|u|s|h[1-6]|ul|ol|li|blockquote|pre|code|a|div|span)\b/i.test(value);
}

window.initToastUiEditor = function (options) {
    var config = options || {};
    var Editor = window.toastui && window.toastui.Editor;
    var editorEl = resolveToastEditorElement(config.editorSelector || config.editorEl);
    var sourceEl = resolveToastEditorElement(config.sourceSelector || config.sourceEl);
    var syncOnChange = config.syncOnChange !== false;
    var initialValue = typeof config.initialValue === 'string'
        ? config.initialValue
        : (sourceEl ? (sourceEl.value || '') : '');
    var hasInitialHtml = looksLikeHtmlContent(initialValue);

    if (!Editor || !editorEl) {
        return null;
    }

    var editor = new Editor({
        el: editorEl,
        height: config.height || '500px',
        initialEditType: config.initialEditType || 'wysiwyg',
        previewStyle: config.previewStyle || 'vertical',
        // HTML 콘텐츠는 생성 후 setHTML로 주입해야 안정적으로 보인다.
        initialValue: hasInitialHtml ? '' : initialValue,
        toolbarItems: config.toolbarItems || getToastEditorDefaultToolbarItems()
    });

    if (hasInitialHtml && typeof editor.setHTML === 'function') {
        editor.setHTML(initialValue, false);
    }

    if (sourceEl && syncOnChange) {
        sourceEl.value = editor.getHTML();
        editor.on('change', function () {
            sourceEl.value = editor.getHTML();
        });
    }

    return editor;
};

window.initToastUiEditors = function (configs) {
    var list = Array.isArray(configs) ? configs : [configs];
    return list.map(function (config) {
        return window.initToastUiEditor(config);
    }).filter(Boolean);
};

$(function () {
    var autoSelector = '.js-toast-ui-editor';
    var nodes = document.querySelectorAll(autoSelector);

    nodes.forEach(function (node) {
        var sourceSelector = node.getAttribute('data-source-selector');
        window.initToastUiEditor({
            editorEl: node,
            sourceSelector: sourceSelector || null
        });
    });
});
