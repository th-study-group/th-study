$(function () {
    $('.board-filter-toggle').each(function () {
        var $toggle = $(this);
        var targetSelector = resolveTargetSelector($toggle);
        var $panel;

        if (!targetSelector) {
            return;
        }

        $panel = $(targetSelector).first();
        if (!$panel.length) {
            return;
        }

        applyToggleState($toggle, $panel);

        $panel.on('shown.bs.collapse.boardFilterToggle', function () {
            applyToggleState($toggle, $panel);
        });

        $panel.on('hidden.bs.collapse.boardFilterToggle', function () {
            applyToggleState($toggle, $panel);
        });
    });
});

function resolveTargetSelector($toggle) {
    var targetSelector = $toggle.attr('data-bs-target');
    var controls;

    if (targetSelector) {
        return targetSelector;
    }

    controls = $toggle.attr('aria-controls');
    if (controls) {
        return '#' + controls;
    }

    return '';
}

function applyToggleState($toggle, $panel) {
    var isExpanded = $panel.hasClass('show');

    if (isExpanded) {
        $toggle.text('펼침');
        $toggle.attr('aria-expanded', 'true');
        $toggle.removeClass('collapsed');
        return;
    }

    $toggle.text('접기');
    $toggle.attr('aria-expanded', 'false');
    $toggle.addClass('collapsed');
}