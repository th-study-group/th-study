function normalizeTag(rawTag) {
  return String(rawTag ?? '')
    .replace(/#/g, ' ')
    .replace(/,/g, ' ')
    .trim()
    .replace(/\s+/g, ' ');
}

function updateThumbnailName(inputElement, nameSelector) {
  const fileName = inputElement.files && inputElement.files[0]
    ? inputElement.files[0].name
    : '선택된 파일 없음';

  $(nameSelector).text(fileName);
}

function renderTagManager(manager) {
  manager.$chips.empty();

  manager.tags.forEach(function (tag) {
    const $item = $('<li>', { class: 'blog-create-tag-chip' });
    $('<span>', {
      class: 'blog-create-tag-text',
      text: '#' + tag,
    }).appendTo($item);

    $('<button>', {
      type: 'button',
      class: 'blog-create-tag-remove js-tag-remove',
      text: '×',
      'aria-label': '태그 삭제',
    })
      .attr('data-tag', tag)
      .appendTo($item);

    manager.$chips.append($item);
  });

  manager.$hidden.val(manager.tags.join(','));
}

function addTagToManager(manager, rawTag) {
  const normalized = normalizeTag(rawTag);

  if (!normalized) {
    return { ok: false, reason: 'empty' };
  }

  if (manager.tags.includes(normalized)) {
    return { ok: false, reason: 'duplicate' };
  }

  if (manager.tags.length >= manager.maxCount) {
    return { ok: false, reason: 'max' };
  }

  manager.tags.push(normalized);
  renderTagManager(manager);

  return { ok: true };
}

function removeTagFromManager(manager, tag) {
  const idx = manager.tags.indexOf(String(tag ?? ''));

  if (idx > -1) {
    manager.tags.splice(idx, 1);
    renderTagManager(manager);
  }
}

function bindRenderTagManager(manager) {
  return renderTagManager.bind(null, manager);
}

function bindAddTagToManager(manager) {
  return addTagToManager.bind(null, manager);
}

function bindRemoveTagFromManager(manager) {
  return removeTagFromManager.bind(null, manager);
}

function createTagManager(options) {
  const manager = {
    maxCount: Number(options?.maxCount ?? 10),
    $chips: $(options?.chipsSelector ?? '#blogTagChips'),
    $hidden: $(options?.hiddenSelector ?? '#tags'),
    tags: [],
  };

  manager.render = bindRenderTagManager(manager);
  manager.addTag = bindAddTagToManager(manager);
  manager.removeTag = bindRemoveTagFromManager(manager);

  return manager;
}
