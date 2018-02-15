(function ($) {

  if (typeof Drupal != 'undefined') {
    Drupal.behaviors.modok = {
      attach: function (context, settings) {
        init();
      },

      completedCallback: function () {
        // Do nothing. But it's here in case other modules/themes want to override it.
      }
    }
  }

  $(function () {
    if (typeof Drupal == 'undefined') {
      init();
    }

    $(window).load(function () {
      initHeaderNav();
    });
  });

  function init() {
    initElmsAnimation();
    initBtnMenu();
  }

  function initElmsAnimation() {
    var $elms = $('.el-with-animation');
    var animationEnd = [];

    $(window).on('resize scroll', checkScroll);

    checkScroll();

    function checkScroll() {
      if (animationEnd.length === $elms.length) return;

      for (var i = 0; i < $elms.length; i++) {
        var $currentEl = $elms.eq(i);

        if (!$currentEl.hasClass('animating-end') && $(window).height() + $(window).scrollTop() > $currentEl.offset().top + $currentEl.height() / 2 + 50) {
          animate($currentEl);
        }
      }
    }

    function animate(el) {
      el.addClass('animating-end');
      animationEnd.push(1);
    }
  }

  function initHeaderNav() {
    var $links = $('.nav .menu a');

    $links.on('click touch', goToItem);

    function goToItem() {

      var $this = $(this);
      var $el = $('body').find('[name="' + $this.attr('href').replace('/#', '') + '"]');

      if (!$el.length) return;

      $('html, body').animate({
        scrollTop: $el.offset().top
      });
    }
  }

  function initBtnMenu() {
    var $btn = $('.btn-menu'),
      $nav = $('.nav');

    $btn.on('click touch', function (e) {
      e.preventDefault();

      if ($(this).hasClass('active')) {

        $(this).removeClass('active');
        $nav.removeClass('active');

      } else {
        $(this).addClass('active');
        $nav.addClass('active');
      }
    })
  }

})(jQuery);