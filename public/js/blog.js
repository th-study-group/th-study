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
  const safeThumbUrl = escapeHtmlText(thumbUrl);
  const normalizedThumbUrl = thumbUrl.split('?')[0].split('#')[0].toLowerCase();
  const isNoImage = normalizedThumbUrl.endsWith('/images/no_image.png') || normalizedThumbUrl.endsWith('/no_image.png');
  const hasThumbnail = String(item.thumbnail_url || '').trim() !== '' && !isNoImage;
  const thumbnailHtml = hasThumbnail ? `
    <a
      href="${safeThumbUrl}"
      class="blog-item-image-link"
      target="_blank"
      rel="noopener noreferrer"
      aria-label="이미지 보기"
      title="이미지 보기"
    >
      <img src="${safeThumbUrl}" alt="" class="blog-item-thumb" data-pwa-image-preview>
      <span class="blog-item-image-zoom" aria-hidden="true">
        <i class="bi bi-zoom-in"></i>
      </span>
    </a>
  ` : `
    <img src="${safeThumbUrl}" alt="" class="blog-item-thumb is-placeholder">
  `;
  const showUrl = escapeHtmlText(item.show_url || '');
  const useFlag = String(item.use_flag || 'N');
  const useFlagLabel = String(item.use_flag_label || (useFlag === 'Y' ? '공개' : '비공개'));
  const canManageVisibility = window.blogCanManageVisibility === true;
  const visibilityBadgeHtml = canManageVisibility
    ? createVisibilityBadgeHtml(useFlag, useFlagLabel, 'blog-item-visibility')
    : '';

  return `
    <article class="blog-item" data-note-idx="${Number(item.idx || 0)}" data-show-url="${showUrl}" data-use-flag="${escapeHtmlText(useFlag)}">
      <div class="blog-item-left">
        <h3 class="blog-item-subject">${escapeHtmlText(item.subject)}</h3>
        <p class="blog-item-category">${escapeHtmlText(item.group_topic_name)}</p>
        <p class="blog-item-desc">${escapeHtmlText(item.desc)}</p>
        <div class="blog-item-meta">
          <span class="blog-item-more">${escapeHtmlText(formatRelativeTimeKorean(item.create_datetime))}</span>
          ${visibilityBadgeHtml}
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

function createVisibilityBadgeHtml(useFlag, useFlagLabel, className) {
  const resolvedUseFlag = String(useFlag || 'N');
  const resolvedLabel = String(useFlagLabel || (resolvedUseFlag === 'Y' ? '공개' : '비공개'));
  const isPublic = resolvedUseFlag === 'Y';
  const iconPath = isPublic
    ? '<path d="M9 10V7.75a3.75 3.75 0 017.1-1.7"></path><path d="M16.5 10H18a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2h8"></path>'
    : '<path d="M8 10V7.5a4 4 0 118 0V10"></path><rect x="5" y="10" width="14" height="10" rx="2"></rect>';

  return `
    <span class="${escapeHtmlText(className)} ${isPublic ? 'is-public' : 'is-private'}" title="${escapeHtmlText(resolvedLabel)}" aria-label="${escapeHtmlText(resolvedLabel)}">
      <svg viewBox="0 0 24 24" aria-hidden="true">${iconPath}</svg>
      <span>${escapeHtmlText(resolvedLabel)}</span>
    </span>
  `;
}

function normalizeQueryAmp(value) {
  return String(value || '').replace(/([?&])amp;/g, '$1');
}

function isUnsafeHref(href) {
  return /^\s*(javascript:|data:)/i.test(String(href || ''));
}

function isSkippableOutboundHref(href) {
  var resolvedHref = String(href || '').trim().toLowerCase();

  if (!resolvedHref) {
    return true;
  }

  if (resolvedHref.startsWith('#')) {
    return true;
  }

  if (resolvedHref.startsWith('mailto:') || resolvedHref.startsWith('tel:')) {
    return true;
  }

  return isUnsafeHref(resolvedHref);
}

function isInternalHrefForOutbound(href) {
  var resolvedHref = String(href || '').trim();

  if (
    resolvedHref.startsWith('/') ||
    resolvedHref.startsWith('#') ||
    resolvedHref.startsWith('mailto:') ||
    resolvedHref.startsWith('tel:')
  ) {
    return true;
  }

  try {
    var parsed = new URL(resolvedHref, location.origin);
    return parsed.host === location.host;
  } catch (e) {
    return false;
  }
}

function extractPathWithQueryFromUrl(rawUrl) {
  try {
    var resolvedUrl = new URL(String(rawUrl || '').trim(), location.origin);
    return String(resolvedUrl.pathname || '/') + String(resolvedUrl.search || '');
  } catch (e) {
    return '';
  }
}

function resolveOutboundSourcePage() {
  var preferredSourcePage = String(window.blogCurrentSourcePage || '').trim();
  if (preferredSourcePage.startsWith('/')) {
    return preferredSourcePage;
  }

  return String(location.pathname + location.search || '/').trim();
}

function buildOutboundTrackingHref(targetUrl, conversionType, options) {
  var params = new URLSearchParams({
    url: String(targetUrl || ''),
    conversion_type: String(conversionType || 'outbound'),
  });

  var skipSourcePage = !!(options && options.skipSourcePage === true);

  if (!skipSourcePage) {
    var sourcePage = resolveOutboundSourcePage();
    if (!sourcePage.startsWith('/')) {
      sourcePage = '/' + sourcePage.replace(/^\/+/, '');
    }
    sourcePage = sourcePage.slice(0, 255);
    params.set('source_page', sourcePage || '/');
  }

  return '/outbound?' + params.toString();
}

function isInternalTargetForOutboundHref(href) {
  try {
    var outboundUrl = new URL(String(href || ''), location.origin);
    var target = String(outboundUrl.searchParams.get('url') || '').trim();

    if (!target) {
      return false;
    }

    var resolved = new URL(target, location.origin);
    return resolved.host === location.host;
  } catch (e) {
    return false;
  }
}

function isOutboundTrackingHref(href) {
  try {
    var resolved = new URL(String(href || ''), location.origin);
    return resolved.pathname === '/outbound';
  } catch (e) {
    return false;
  }
}

function normalizeOutboundLinkHref(href, options) {
  var resolved = normalizeQueryAmp(String(href || '').trim());
  var trackInternal = !!(options && options.trackInternal === true);
  var skipSourcePage = !!(options && options.skipSourcePage === true);

  if (isSkippableOutboundHref(resolved)) {
    return resolved;
  }

  if (resolved.startsWith('/outbound')) {
    try {
      var outboundUrl = new URL(resolved, location.origin);
      var targetUrl = outboundUrl.searchParams.get('url');
      var conversionType =
        outboundUrl.searchParams.get('conversion_type') ||
        outboundUrl.searchParams.get('amp;conversion_type') ||
        'outbound';

      if (!targetUrl) {
        return resolved;
      }

      return buildOutboundTrackingHref(targetUrl, conversionType, {
        skipSourcePage: skipSourcePage,
      });
    } catch (e) {
      return resolved;
    }
  }

  if (!isInternalHrefForOutbound(resolved)) {
    return buildOutboundTrackingHref(resolved, 'outbound', {
      skipSourcePage: skipSourcePage,
    });
  }

  if (trackInternal) {
    try {
      var internalTarget = new URL(resolved, location.origin).toString();
      return buildOutboundTrackingHref(internalTarget, 'outbound', {
        skipSourcePage: skipSourcePage,
      });
    } catch (e) {
      return resolved;
    }
  }

  return resolved;
}

function rewriteHtmlAnchorHrefsToOutbound(contentHtml, options) {
  var container = document.createElement('div');
  container.innerHTML = String(contentHtml || '');

  container.querySelectorAll('a[href]').forEach(function (anchor) {
    var href = anchor.getAttribute('href');
    var normalizedHref = normalizeOutboundLinkHref(href, options);

    if (normalizedHref) {
      anchor.setAttribute('href', normalizedHref);
    }
  });

  return container.innerHTML;
}

function applyExternalLinkAttributes(containerSelector) {
  var $container = $(containerSelector);
  var shouldOpenInNewWindow = !(
    typeof isStandalonePwa === 'function' &&
    isStandalonePwa()
  );

  $container.off('click.blogOutboundSource', 'a[href]');
  $container.on('click.blogOutboundSource', 'a[href]', function () {
    var currentHref = String($(this).attr('href') || '').trim();
    if (!isOutboundTrackingHref(currentHref)) {
      return;
    }

    try {
      var outboundUrl = new URL(currentHref, location.origin);
      var targetUrl = outboundUrl.searchParams.get('url');
      var conversionType =
        outboundUrl.searchParams.get('conversion_type') ||
        outboundUrl.searchParams.get('amp;conversion_type') ||
        'outbound';

      if (!targetUrl) {
        return;
      }

      $(this).attr('href', buildOutboundTrackingHref(targetUrl, conversionType));
    } catch (e) {
      return;
    }
  });

  $container.find('a').each(function () {
    var $link = $(this);
    var href = normalizeOutboundLinkHref($link.attr('href'), { trackInternal: false });

    if (!href) {
      return;
    }

    if (href !== String($link.attr('href') || '').trim()) {
      $link.attr('href', href);
    }

    var isOutboundTracking = isOutboundTrackingHref(href);
    var isInternal = !isOutboundTracking && isInternalHrefForOutbound(href);

    $link.removeAttr('data-link-kind');

    if (isOutboundTracking) {
      var isInternalTarget = isInternalTargetForOutboundHref(href);

      $link.attr('data-link-kind', isInternalTarget ? 'internal' : 'external');

      if (shouldOpenInNewWindow) {
        $link
          .attr('target', '_blank')
          .attr('rel', 'noopener noreferrer');
      } else {
        $link
          .removeAttr('target')
          .removeAttr('rel');
      }

      return;
    }

    if (!isInternal) {
      $link
        .attr('href', buildOutboundTrackingHref(href, 'outbound'))
        .attr('data-link-kind', 'external');

      if (shouldOpenInNewWindow) {
        $link
          .attr('target', '_blank')
          .attr('rel', 'noopener noreferrer');
      } else {
        $link
          .removeAttr('target')
          .removeAttr('rel');
      }
      return;
    }

    $link
      .attr('data-link-kind', 'internal');

    if (shouldOpenInNewWindow) {
      $link
        .attr('target', '_blank')
        .attr('rel', 'noopener noreferrer');
    } else {
      $link
        .removeAttr('target')
        .removeAttr('rel');
    }
  });
}

function enhanceBlogDetailContent(containerSelector) {
  applyExternalLinkAttributes(containerSelector);
}

function initBlogDetailContentEnhancements() {
  enhanceBlogDetailContent('.blog-show-content');
}

function syncBlogListItemVisibility(noteIdx, useFlag, useFlagLabel) {
  if (window.blogCanManageVisibility !== true) {
    return;
  }

  const safeNoteIdx = Number(noteIdx || 0);
  if (safeNoteIdx <= 0) {
    return;
  }

  const $item = $(`.blog-item[data-note-idx='${safeNoteIdx}']`);
  if ($item.length === 0) {
    return;
  }

  $item.attr('data-use-flag', String(useFlag || 'N'));
  $item.find('.blog-item-visibility').replaceWith(
    createVisibilityBadgeHtml(useFlag, useFlagLabel, 'blog-item-visibility')
  );
}

function applyBlogDetailState(state, payload) {
  const note = payload.note || {};
  const actions = payload.actions || {};
  const permissions = payload.permissions || {};
  const relatedTitle = String(payload.related_title || '');
  const relatedNotes = Array.isArray(payload.related_notes) ? payload.related_notes : [];
  const topicName = String(note.topic_name || '').trim();
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
  const noteIdx = Number(note.idx || 0);

  state.currentDetail = {
    note,
    actions,
    permissions,
  };
  window.blogCurrentSourcePage = extractPathWithQueryFromUrl(actions.show_url || '');
  state.pendingDetailScrollTop = 0;

  if (noteIdx > 0) {
    const savedScrollTop = Number(state.detailScrollByNoteIdx?.[noteIdx] ?? 0);
    state.pendingDetailScrollTop = Number.isFinite(savedScrollTop) && savedScrollTop > 0
      ? savedScrollTop
      : 0;
  }

  $('#blogDetailCategory').text(note.group_topic_name || '-');
  $('#blogDetailTitle').text(note.subject || '-');
  $('#blogDetailDate').text(note.create_datetime || '-');
  $('#blogDetailContent').html(note.content_html || '');
  enhanceBlogDetailContent('#blogDetailContent');

  const $relatedWrap = $('#blogDetailRelatedWrap');
  const $relatedTitle = $('#blogDetailRelatedTitle');
  const $relatedList = $('#blogDetailRelatedList');
  const resolvedTopicName = topicName !== ''
    ? topicName
    : String(relatedTitle).replace(/ 카테고리의 관련 글$/, '').trim();
  $relatedTitle.html(
    `<span class="blog-detail-related-topic">${escapeHtmlText(resolvedTopicName || '-')}</span>` +
    `<span>관련 글</span>`
  );
  $relatedList.empty();

  if (relatedNotes.length > 0) {
    relatedNotes.forEach(function (related) {
      const showUrl = String(related?.show_url || '');
      const subject = String(related?.subject || '');
      const relativeTime = String(related?.relative_time || '-');
      $relatedList.append(
        `<li class="blog-detail-related-item">` +
          `<a href="${escapeHtmlText(showUrl)}" class="blog-detail-related-subject js-blog-detail-related-open" data-show-url="${escapeHtmlText(showUrl)}">${escapeHtmlText(subject)}</a>` +
          `<span class="blog-detail-related-date">${escapeHtmlText(relativeTime)}</span>` +
        `</li>`
      );
    });
    $relatedWrap.show();
  } else {
    $relatedWrap.hide();
  }

  const $visibility = $('#blogDetailVisibility');
  if (window.blogCanManageVisibility === true) {
    $visibility
      .html(createVisibilityBadgeHtml(useFlag, useFlagLabel, 'blog-detail-visibility-badge-inner'))
      .toggleClass('is-public', useFlag === 'Y')
      .toggleClass('is-private', useFlag !== 'Y')
      .show();
    $visibility.closest('.blog-detail-visibility').show();
  } else {
    $visibility.empty().hide();
    $visibility.closest('.blog-detail-visibility').hide();
  }

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

  updateBlogMetaKeywords(state, tags);
}

function getBlogDetailBody() {
  return $('#blogDetailModal .blog-detail-body').first();
}

function updateBlogMetaKeywords(state, tags) {
  const $metaKeywords = $('meta[name="keywords"]');
  if ($metaKeywords.length === 0) {
    return;
  }

  const nextKeywords = Array.isArray(tags)
    ? tags
        .map(function (tag) {
          return String(tag || '').trim();
        })
        .filter(function (tag) {
          return tag !== '';
        })
        .join(',')
    : '';

  $metaKeywords.attr('content', nextKeywords);
  state.currentMetaKeywords = nextKeywords;
}

function saveBlogDetailScrollPosition(state) {
  if (!state || !state.currentDetail) {
    return;
  }

  const noteIdx = Number(state.currentDetail?.note?.idx || 0);
  if (noteIdx <= 0) {
    return;
  }

  const $body = getBlogDetailBody();
  if ($body.length === 0) {
    return;
  }

  state.detailScrollByNoteIdx = state.detailScrollByNoteIdx || {};
  state.detailScrollByNoteIdx[noteIdx] = $body.scrollTop();
}

function openBlogDetailModal(state) {
  const $modal = $('#blogDetailModal');
  if ($modal.length && !$modal.parent().is('body')) {
    $modal.appendTo('body');
  }

  $('html, body').addClass('blog-modal-open');
  $modal.css('display', 'flex');
  $modal.attr('aria-hidden', 'false');

  const nextScrollTop = Number(state?.pendingDetailScrollTop ?? 0);
  const safeScrollTop = Number.isFinite(nextScrollTop) && nextScrollTop > 0 ? nextScrollTop : 0;
  const $body = getBlogDetailBody();
  if ($body.length > 0) {
    $body.scrollTop(safeScrollTop);
    requestAnimationFrame(function () {
      $body.scrollTop(safeScrollTop);
    });
  }
}

function closeBlogDetailModal(state) {
  saveBlogDetailScrollPosition(state);
  const $metaKeywords = $('meta[name="keywords"]');
  if ($metaKeywords.length > 0) {
    $metaKeywords.attr('content', String(state?.initialMetaKeywords ?? ''));
    state.currentMetaKeywords = String(state?.initialMetaKeywords ?? '');
  }

  $('html, body').removeClass('blog-modal-open');
  $('#blogDetailModal').hide();
  $('#blogDetailModal').attr('aria-hidden', 'true');
  window.blogCurrentSourcePage = '';
}

function setMoreButtonLoading(state, isLoading) {
  if (!state || !state.$moreButton || state.$moreButton.length === 0) {
    return;
  }

  const $button = state.$moreButton;
  const originalHtml = $button.data('original-html');

  if (isLoading) {
    if (!originalHtml) {
      $button.data('original-html', $button.html());
    }
    $button
      .prop('disabled', true)
      .html(
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
        '<span>불러오는 중...</span>'
      );
    return;
  }

  $button.prop('disabled', false);
  if (originalHtml) {
    $button.html(originalHtml);
  } else {
    $button.html('+ 목록 더보기');
  }
}

function fetchBlogListPage(state, page, shouldAppend) {
  if (state.isLoadingList) {
    return;
  }

  const isAppendLoad = shouldAppend === true;
  state.isLoadingList = true;
  if (isAppendLoad) {
    setMoreButtonLoading(state, true);
  } else {
    state.$moreButton.prop('disabled', true);
  }

  requestAjax({
    method: 'GET',
    url: state.listUrl,
    dataType: 'json',
    showLoading: !isAppendLoad,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
    },
    data: {
      page: page,
      search_select_type: state.searchType,
      search_keyword: state.searchKeyword,
      search_topic: state.selectedTopicValue,
    },
    onSuccess: function (res) {
      const items = Array.isArray(res.items) ? res.items : [];
      const pagination = res.pagination || {};
      const now = new Date();
      state.pagination = pagination;

      renderBlogListItems(state.$items, items, shouldAppend);
      updateBlogMoreButton(state.$moreWrap, pagination);
      $('#blog_list_total').text(`총 ${Number(pagination.total || 0)}건`);
      $('#blogRefreshTime')
        .attr('datetime', now.toISOString())
        .text(new Intl.DateTimeFormat('ko-KR', {
          month: 'numeric',
          day: 'numeric',
          hour: 'numeric',
          minute: '2-digit',
          hour12: true,
        }).format(now));

      if (!isAppendLoad) {
        window.scrollTo({ top: 0, behavior: 'auto' });
      }
    },
    onError: function () {
      alert('목록을 불러오는 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
    },
    onComplete: function () {
      state.isLoadingList = false;
      if (isAppendLoad) {
        setMoreButtonLoading(state, false);
      } else {
        state.$moreButton.prop('disabled', false);
      }
    },
  });
}

function fetchBlogDetail(state, detailUrl) {
  saveBlogDetailScrollPosition(state);

  requestAjax({
    method: 'GET',
    url: detailUrl,
    dataType: 'json',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
    },
    onSuccess: function (res) {
      applyBlogDetailState(state, res || {});
      openBlogDetailModal(state);
      setTimeout(function() {
        reloadAdfit();
    }, 100);
    },
    onError: function () {
      alert('상세 정보를 불러오는 중 오류가 발생했습니다.');
    },
  });
}

function reloadAdfit() {
  document
    .querySelectorAll('script[src*="kakaocdn.net/kas/static/ba.min.js"]')
    .forEach(function(el) {
      el.remove();
    });

  var script = document.createElement('script');
  script.src = '//t1.kakaocdn.net/kas/static/ba.min.js';
  script.async = true;

  document.body.appendChild(script);
}
