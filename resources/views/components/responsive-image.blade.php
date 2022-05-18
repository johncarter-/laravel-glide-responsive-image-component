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
      document.querySelectorAll('[data-responsive-images]').forEach(responsiveImage => {
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
    data-responsive-images
    loading="lazy"
    {{ $attributes }}
    src="{{ $conversionDefaultUrl }}">
</picture>
