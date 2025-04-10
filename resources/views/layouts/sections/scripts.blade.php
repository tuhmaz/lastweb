@vite([
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/js/bootstrap.js',
  'resources/assets/vendor/libs/node-waves/node-waves.js',
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/hammer/hammer.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/js/menu.js'
])

@yield('vendor-script')

@vite(['resources/assets/js/main.js'])

<script>
function trackVisitorActivity() {
    const track = () => {
        navigator.sendBeacon('/track-visitor', JSON.stringify({
            url: window.location.pathname,
            referrer: document.referrer
        }));
    }

    // تتبع الأحداث التفاعلية
    ['click', 'scroll', 'mousemove'].forEach(event => {
        window.addEventListener(event, track, { passive: true });
    });

    // تحديث دوري كل 15 ثانية
    setInterval(track, 15000);

    // تتبع عند إغلاق الصفحة
    window.addEventListener('beforeunload', track);
}

document.addEventListener('DOMContentLoaded', trackVisitorActivity);


</script>



@yield('page-script')
