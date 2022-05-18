@once
  <script>
    window.addEventListener('load', function() {
      window.responsiveResizeObserver = new ResizeObserver((entries) => {
        entries.forEach(entry => {
          const imgWidth = entry.target.getBoundingClientRect().width;
          entry.target.parentNode.querySelectorAll('source').forEach((source) => {
            source.sizes = Math.ceil(imgWidth / window.innerWidth * 100) + 'vw';
          });
        });
      });
      document.querySelectorAll('[data-laravel-glide-responsive-image-component]').forEach(
        responsiveImage => {
          responsiveResizeObserver.onload = null;
          responsiveResizeObserver.observe(responsiveImage);
        });
    });
  </script>
@endonce


<picture>
  <source
    type="image/webp"
    srcset="{{ $conversionSrcsetString }}"
    sizes="1px">

  <img
    data-laravel-glide-responsive-image-component
    loading="lazy"
    {{ $attributes }}
    class="w-full h-auto"
    src="{{ $conversionDefaultUrl }}">
</picture>
