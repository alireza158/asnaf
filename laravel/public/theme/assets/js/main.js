(function () {
  "use strict";

  const qs = (selector, scope = document) => scope.querySelector(selector);
  const qsa = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));

  // Hero Swiper slider
  if (window.Swiper && qs('.hero-slider')) {
    new Swiper('.hero-slider', {
      loop: true,
      speed: 650,
      effect: 'slide',
      autoplay: {
        delay: 4200,
        disableOnInteraction: false
      },
      pagination: {
        el: '.hero-slider-pagination',
        clickable: true
      },
      navigation: {
        nextEl: '.hero-slider-next',
        prevEl: '.hero-slider-prev'
      }
    });
  }

  // Header search
  const searchTrigger = qs('.search-trigger');
  const searchPanel = qs('#headerSearchPanel');
  const searchInput = qs('#siteSearchInput');
  const searchResults = qs('.header-search-results');
  const searchForm = qs('.header-search-form');

  const searchIndex = [
    { title: 'اتاق اصناف شهرستان گرگان', type: 'معرفی', url: '#' },
    { title: 'اتحادیه‌های صنفی گرگان', type: 'اتحادیه‌ها', url: '#representatives' },
    { title: 'صدور و تمدید پروانه کسب', type: 'خدمات صنفی', url: '#commissions' },
    { title: 'ثبت شکایت و گزارش تخلف صنفی', type: 'بازرسی و نظارت', url: '#friendship' },
    { title: 'دوره آموزش احکام تجارت و کسب‌وکار', type: 'آموزش', url: '#fractions' },
    { title: 'بخشنامه‌ها و اطلاعیه‌های اصناف', type: 'اطلاعیه', url: '#multimedia' },
    { title: 'آدرس و تلفن اتاق اصناف گرگان', type: 'تماس', url: '#friendship' },
    { title: 'سامانه‌های الکترونیکی اصناف', type: 'سامانه‌ها', url: '#commissions' }
  ];

  function normalize(text) {
    return (text || '')
      .toString()
      .trim()
      .toLowerCase()
      .replace(/[ي]/g, 'ی')
      .replace(/[ك]/g, 'ک')
      .replace(/\s+/g, ' ');
  }

  function renderSearchResults(query) {
    if (!searchResults) return;
    const normalizedQuery = normalize(query);
    searchResults.innerHTML = '';

    if (!normalizedQuery) {
      searchResults.classList.remove('is-visible');
      return;
    }

    const results = searchIndex.filter((item) => normalize(`${item.title} ${item.type}`).includes(normalizedQuery)).slice(0, 6);
    searchResults.classList.add('is-visible');

    if (!results.length) {
      searchResults.innerHTML = '<div class="header-search-empty">موردی مطابق جستجوی شما پیدا نشد.</div>';
      return;
    }

    const fragment = document.createDocumentFragment();
    results.forEach((item) => {
      const link = document.createElement('a');
      link.className = 'header-search-result';
      link.href = item.url;
      link.innerHTML = `<strong>${item.title}</strong><span>${item.type}</span>`;
      link.addEventListener('click', closeHeaderSearch);
      fragment.appendChild(link);
    });
    searchResults.appendChild(fragment);
  }

  function openHeaderSearch() {
    if (!searchPanel || !searchTrigger) return;
    searchPanel.hidden = false;
    searchTrigger.classList.add('is-active');
    searchTrigger.setAttribute('aria-expanded', 'true');
    window.setTimeout(() => searchInput && searchInput.focus(), 40);
  }

  function closeHeaderSearch() {
    if (!searchPanel || !searchTrigger) return;
    searchPanel.hidden = true;
    searchTrigger.classList.remove('is-active');
    searchTrigger.setAttribute('aria-expanded', 'false');
  }

  if (searchTrigger && searchPanel) {
    searchTrigger.addEventListener('click', () => {
      if (searchPanel.hidden) openHeaderSearch();
      else closeHeaderSearch();
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeHeaderSearch();
    });

    document.addEventListener('click', (event) => {
      if (searchPanel.hidden) return;
      const target = event.target;
      if (!searchPanel.contains(target) && !searchTrigger.contains(target)) closeHeaderSearch();
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', () => renderSearchResults(searchInput.value));
  }

  if (searchForm) {
    searchForm.addEventListener('submit', (event) => {
      event.preventDefault();
      renderSearchResults(searchInput ? searchInput.value : '');
    });
  }

  // Top navigation dropdowns
  const topNav = qs('#mainNav');
  qsa('.top-nav-item.has-top-submenu > .top-nav-link').forEach((button) => {
    button.addEventListener('click', () => {
      const item = button.closest('.top-nav-item');
      if (!item) return;
      const parent = item.parentElement;
      qsa('.top-nav-item.is-open', parent).forEach((openedItem) => {
        if (openedItem !== item) {
          openedItem.classList.remove('is-open');
          const openedButton = qs('.top-nav-link', openedItem);
          if (openedButton) openedButton.setAttribute('aria-expanded', 'false');
        }
      });
      const isOpen = item.classList.toggle('is-open');
      button.setAttribute('aria-expanded', String(isOpen));
    });
  });

  qsa('.top-submenu a').forEach((link) => {
    link.addEventListener('click', () => {
      const item = link.closest('.top-nav-item');
      if (!item) return;
      item.classList.remove('is-open');
      const button = qs('.top-nav-link', item);
      if (button) button.setAttribute('aria-expanded', 'false');
    });
  });

  document.addEventListener('click', (event) => {
    if (!topNav || topNav.contains(event.target)) return;
    qsa('.top-nav-item.is-open', topNav).forEach((item) => {
      item.classList.remove('is-open');
      const button = qs('.top-nav-link', item);
      if (button) button.setAttribute('aria-expanded', 'false');
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape' || !topNav) return;
    qsa('.top-nav-item.is-open', topNav).forEach((item) => {
      item.classList.remove('is-open');
      const button = qs('.top-nav-link', item);
      if (button) button.setAttribute('aria-expanded', 'false');
    });
  });

  // Vertical quick menu submenus
  // Desktop opens on hover via CSS; click remains available for keyboard/touch users.
  qsa('.quick-menu-item.has-submenu > .quick-menu-link').forEach((button) => {
    button.addEventListener('click', () => {
      const item = button.closest('.quick-menu-item');
      if (!item) return;
      const parent = item.parentElement;
      qsa('.quick-menu-item.is-open', parent).forEach((openedItem) => {
        if (openedItem !== item) {
          openedItem.classList.remove('is-open');
          const openedButton = qs('.quick-menu-link', openedItem);
          if (openedButton) openedButton.setAttribute('aria-expanded', 'false');
        }
      });
      const isOpen = item.classList.toggle('is-open');
      button.setAttribute('aria-expanded', String(isOpen));
    });
  });

  // Guild news slider
  if (window.Swiper && qs('.guild-news-slider')) {
    new Swiper('.guild-news-slider', {
      loop: true,
      speed: 600,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false
      },
      navigation: {
        nextEl: '.guild-slider-next',
        prevEl: '.guild-slider-prev'
      },
      pagination: {
        el: '.guild-news-slider .swiper-pagination',
        clickable: true
      }
    });
  }

  // Generic tab components
  qsa('[data-tab-group]').forEach((group) => {
    group.addEventListener('click', (event) => {
      const button = event.target.closest('[data-tab-target]');
      if (!button) return;

      const groupName = group.getAttribute('data-tab-group');
      const targetName = button.getAttribute('data-tab-target');
      const panelWrap = qs(`[data-tab-panels="${groupName}"]`);

      qsa('[data-tab-target]', group).forEach((item) => item.classList.remove('active'));
      button.classList.add('active');

      if (panelWrap) {
        qsa('[data-tab-panel]', panelWrap).forEach((panel) => {
          const active = panel.getAttribute('data-tab-panel') === targetName;
          panel.classList.toggle('active', active);
        });

        qsa('input[type="search"]', panelWrap).forEach((input) => {
          input.value = '';
          filterList(input, '');
        });
      }
    });
  });

  // Local list filters
  function filterList(input, query) {
    const normalizedQuery = normalize(query);
    const area = input.closest('[data-search-area]') || input.closest('section') || document;
    qsa('li', area).forEach((item) => {
      const isVisible = normalize(item.textContent).includes(normalizedQuery);
      item.toggleAttribute('data-filter-hidden', normalizedQuery && !isVisible);
    });
  }

  qsa('[data-filter-input]').forEach((input) => {
    input.addEventListener('input', () => filterList(input, input.value));
  });

  qsa('[data-global-filter]').forEach((input) => {
    input.addEventListener('input', () => {
      const areaName = input.getAttribute('data-global-filter');
      const area = qs(`[data-search-area="${areaName}"]`);
      const normalizedQuery = normalize(input.value);
      if (!area) return;
      qsa('li', area).forEach((item) => {
        const isVisible = normalize(item.textContent).includes(normalizedQuery);
        item.toggleAttribute('data-filter-hidden', normalizedQuery && !isVisible);
      });
    });
  });

  // Lightbox
  const lightboxEl = qs('.lightbox');
  if (lightboxEl) {
    const lightboxImg = qs('.lightbox-img', lightboxEl);
    const lightboxClose = qs('.lightbox-close', lightboxEl);
    const lightboxPrev = qs('.lightbox-prev', lightboxEl);
    const lightboxNext = qs('.lightbox-next', lightboxEl);
    const lightboxCounter = qs('.lightbox-counter', lightboxEl);
    let currentIndex = 0;
    let imageSources = [];

    function openLightbox(index) {
      currentIndex = index;
      if (!imageSources.length) return;
      lightboxImg.src = imageSources[currentIndex];
      lightboxEl.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      updateCounter();
    }

    function closeLightbox() {
      lightboxEl.classList.remove('is-open');
      document.body.style.overflow = '';
    }

    function updateCounter() {
      if (!lightboxCounter) return;
      lightboxCounter.textContent = `${currentIndex + 1} از ${imageSources.length}`;
    }

    lightboxClose.addEventListener('click', closeLightbox);

    lightboxPrev.addEventListener('click', () => {
      if (imageSources.length === 0) return;
      currentIndex = (currentIndex - 1 + imageSources.length) % imageSources.length;
      lightboxImg.src = imageSources[currentIndex];
      updateCounter();
    });

    lightboxNext.addEventListener('click', () => {
      if (imageSources.length === 0) return;
      currentIndex = (currentIndex + 1) % imageSources.length;
      lightboxImg.src = imageSources[currentIndex];
      updateCounter();
    });

    document.addEventListener('keydown', (e) => {
      if (!lightboxEl.classList.contains('is-open')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') lightboxNext.click();
      if (e.key === 'ArrowRight') lightboxPrev.click();
    });

    lightboxEl.addEventListener('click', (e) => {
      if (e.target === lightboxEl) closeLightbox();
    });

    // Init from gallery-thumbs
    qsa('[data-gallery-group]').forEach((group) => {
      const thumbs = qsa('[data-gallery-item]', group);
      const sources = thumbs.map((t) => t.getAttribute('data-gallery-item'));

      thumbs.forEach((thumb, idx) => {
        thumb.addEventListener('click', () => {
          imageSources = sources;
          openLightbox(idx);
        });
      });
    });
  }
})();
