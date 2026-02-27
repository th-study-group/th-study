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

function escapeHtmlText(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function formatRelativeTimeKorean(dateString) {
  const raw = String(dateString ?? '').trim();
  if (!raw) {
    return '-';
  }

  const parsed = new Date(raw.replace(' ', 'T'));
  if (Number.isNaN(parsed.getTime())) {
    return raw;
  }

  const now = new Date();
  const diffMs = now.getTime() - parsed.getTime();
  const diffSec = Math.max(0, Math.floor(diffMs / 1000));

  if (diffSec < 60) {
    return '방금 전';
  }

  const diffMin = Math.floor(diffSec / 60);
  if (diffMin < 60) {
    return `${diffMin}분 전`;
  }

  const diffHour = Math.floor(diffMin / 60);
  if (diffHour < 24) {
    return `${diffHour}시간 전`;
  }

  const diffDay = Math.floor(diffHour / 24);
  if (diffDay < 30) {
    return `${diffDay}일 전`;
  }

  const diffMonth = Math.floor(diffDay / 30);
  if (diffMonth < 12) {
    return `${diffMonth}달 전`;
  }

  const diffYear = Math.floor(diffMonth / 12);
  return `${diffYear}년 전`;
}

function createBlogListItemHtml(item) {
  const thumbUrl = String(item.thumbnail_url || '/images/no_image.png');
  const thumbnailHtml = `<img src="${escapeHtmlText(thumbUrl)}" alt="" class="blog-item-thumb">`;
  const showUrl = escapeHtmlText(item.show_url || '');

  return `
    <article class="blog-item" data-note-idx="${Number(item.idx || 0)}" data-show-url="${showUrl}">
      <div class="blog-item-left">
        <h3 class="blog-item-subject">${escapeHtmlText(item.subject)}</h3>
        <p class="blog-item-category">${escapeHtmlText(item.group_topic_name)}</p>
        <p class="blog-item-desc">${escapeHtmlText(item.desc)}</p>
        <div class="blog-item-meta">
          <span class="blog-item-more">${escapeHtmlText(formatRelativeTimeKorean(item.create_datetime))}</span>
          <button type="button" class="blog-item-more-btn" data-show-url="${showUrl}" aria-label="더보기" title="더보기">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="6" cy="12" r="1.8"></circle>
              <circle cx="12" cy="12" r="1.8"></circle>
              <circle cx="18" cy="12" r="1.8"></circle>
            </svg>
          </button>
        </div>
      </div>
      <div class="blog-item-right">${thumbnailHtml}</div>
    </article>
  `;
}

function renderBlogListItems($container, items, shouldAppend) {
  if (!shouldAppend) {
    $container.empty();
  }

  if (!Array.isArray(items) || items.length === 0) {
    if (!shouldAppend) {
      $container.html('<p class="blog-empty">등록된 글이 없습니다.</p>');
    }
    return;
  }

  const html = items.map(createBlogListItemHtml).join('');
  if (shouldAppend) {
    $container.append(html);
    return;
  }

  $container.html(html);
}

function updateBlogMoreButton($button, pagination) {
  if (!pagination || !pagination.has_more) {
    $button.hide();
    return;
  }

  $button.show();
}

function applyBlogDetailState(state, payload) {
  const note = payload.note || {};
  const actions = payload.actions || {};
  const permissions = payload.permissions || {};
  const tags = Array.isArray(note.tags)
    ? note.tags
        .map(function (tag) {
          if (typeof tag === 'string') {
            return tag;
          }
          if (tag && typeof tag === 'object') {
            return String(tag.name ?? tag.tag_name ?? '').trim();
          }
          return '';
        })
        .filter(function (tagName) {
          return tagName !== '';
        })
    : [];
  const useFlag = String(note.use_flag || 'N');
  const useFlagLabel = String(note.use_flag_label || (useFlag === 'Y' ? '공개' : '비공개'));

  state.currentDetail = {
    note,
    actions,
    permissions,
  };

  $('#blogDetailTitle').text(note.subject || '-');
  $('#blogDetailCategory').text(note.group_topic_name || '-');
  $('#blogDetailDate').text(note.create_datetime || '-');
  $('#blogDetailContent').html(note.content_html || '');

  const $visibility = $('#blogDetailVisibility');
  $visibility.text(useFlagLabel);
  $visibility.toggleClass('is-public', useFlag === 'Y');

  const $tags = $('#blogDetailTags');
  $tags.empty();
  if (tags.length > 0) {
    tags.forEach(function (tagName) {
      $tags.append(`<li>#${escapeHtmlText(tagName)}</li>`);
    });
    $tags.show();
  } else {
    $tags.hide();
  }

  $('#blogDetailEditBtn').toggle(!!permissions.can_update);
  $('#blogDetailDeleteBtn').toggle(!!permissions.can_delete);
  $('#blogDetailPublicBtn').toggle(!!permissions.can_update_use_flag);
}

function openBlogDetailModal() {
  const $modal = $('#blogDetailModal');
  if ($modal.length && !$modal.parent().is('body')) {
    $modal.appendTo('body');
  }

  $('html, body').addClass('blog-modal-open');
  $modal.css('display', 'flex');
}

function closeBlogDetailModal() {
  $('html, body').removeClass('blog-modal-open');
  $('#blogDetailModal').hide();
}

function fetchBlogListPage(state, page, shouldAppend) {
  if (state.isLoadingList) {
    return;
  }

  state.isLoadingList = true;
  state.$moreButton.prop('disabled', true);

  requestAjax({
    method: 'GET',
    url: state.listUrl,
    dataType: 'json',
    data: {
      page: page,
      search_select_type: state.searchType,
      search_keyword: state.searchKeyword,
    },
    onSuccess: function (res) {
      const items = Array.isArray(res.items) ? res.items : [];
      const pagination = res.pagination || {};
      state.pagination = pagination;

      renderBlogListItems(state.$items, items, shouldAppend);
      updateBlogMoreButton(state.$moreWrap, pagination);
      $('#blog_list_total').text(`총 ${Number(pagination.total || 0)}건`);
    },
    onError: function () {
      alert('목록을 불러오는 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
    },
    onComplete: function () {
      state.isLoadingList = false;
      state.$moreButton.prop('disabled', false);
    },
  });
}

function fetchBlogDetail(state, detailUrl) {
  requestAjax({
    method: 'GET',
    url: detailUrl,
    dataType: 'json',
    onSuccess: function (res) {
      applyBlogDetailState(state, res || {});
      openBlogDetailModal();
    },
    onError: function () {
      alert('상세 정보를 불러오는 중 오류가 발생했습니다.');
    },
  });
}
