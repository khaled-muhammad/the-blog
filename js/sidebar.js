document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('.admin-sidebar');
  const nav = document.querySelector('.admin-side-nav');
  const toggle = document.querySelector('.sidebar-toggle');

  if (!sidebar || !nav || !toggle) {
    return;
  }

  const toggleIcon = toggle.querySelector('ion-icon');
  const navTextSpans = sidebar.querySelectorAll('.nav-link span');

  const setSpanVisibility = (collapsed) => {
    navTextSpans.forEach((span) => {
      if (collapsed) {
        span.style.display = 'none';
        span.setAttribute('aria-hidden', 'true');
      } else {
        span.style.display = '';
        span.removeAttribute('aria-hidden');
      }
    });
  };

  const updateToggleState = () => {
    const isCollapsed = sidebar.classList.contains('collapsed');
    toggle.setAttribute('aria-expanded', String(!isCollapsed));
    if (toggleIcon) {
      toggleIcon.setAttribute('name', isCollapsed ? 'chevron-forward-outline' : 'chevron-back-outline');
    }
    if (!isCollapsed) {
      setSpanVisibility(false);
    }
  };

  const handleTransitionEnd = (event) => {
    if (event.propertyName !== 'width') {
      return;
    }
    const isCollapsed = sidebar.classList.contains('collapsed');
    setSpanVisibility(isCollapsed);
    sidebar.removeEventListener('transitionend', handleTransitionEnd);
  };

  toggle.addEventListener('click', () => {
    const collapsed = sidebar.classList.toggle('collapsed');
    if (collapsed) {
    const isCollapsed = sidebar.classList.contains('collapsed');
    setSpanVisibility(isCollapsed);
      //sidebar.addEventListener('transitionend', handleTransitionEnd);
    } else {
      setSpanVisibility(false);
    }
    updateToggleState();
  });

  nav.addEventListener('mouseenter', () => {
    if (!toggle.matches(':hover')) {
      toggle.style.opacity = '0';
    }
  });

  nav.addEventListener('mouseleave', () => {
    toggle.style.opacity = '1';
  });

  toggle.addEventListener('mouseenter', () => {
    toggle.style.opacity = '1';
  });

  setSpanVisibility(sidebar.classList.contains('collapsed'));
  updateToggleState();
});