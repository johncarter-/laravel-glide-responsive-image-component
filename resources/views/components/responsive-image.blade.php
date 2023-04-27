@once
  <script>
    window.addEventListener('load', function() {

        const resizeObserverCallback = (entries, observer) => {
            entries.forEach(entry => {
                const imgWidth = entry.target.getBoundingClientRect().width;
                entry.target.parentNode.querySelectorAll('source').forEach(source => {
                    source.sizes = `${Math.ceil(imgWidth / window.innerWidth * 100)}vw`;
                });
            });
        };

        const triggerResponsiveImageUpdate = () => {
            const responsiveImages = document.querySelectorAll('[data-laravel-glide-responsive-image-component]');
            if (responsiveImages.length > 0) {
                window.responsiveResizeObserver.disconnect(); // Disconnect observer to prevent duplicate observations
                responsiveImages.forEach(responsiveImage => {
                    window.responsiveResizeObserver.observe(responsiveImage);
                });
            }
        };

        window.responsiveResizeObserver = new ResizeObserver(resizeObserverCallback);

        triggerResponsiveImageUpdate();
        
        if (typeof Livewire !== 'undefined' && Livewire.hook) {
            Livewire.hook('message.processed', triggerResponsiveImageUpdate);
        }
        
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
