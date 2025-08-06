@once
    <script>
        window.addEventListener('load', function() {

            const resizeObserverCallback = (entries, observer) => {
                entries.forEach(entry => {
                    const img = entry.target;
                    const rect = img.getBoundingClientRect();

                    const pixelWidth = Math.ceil(rect.width * window.devicePixelRatio);
                    const pixelHeight = Math.ceil(rect.height * window.devicePixelRatio);

                    const isPortrait = rect.height > rect.width;
                    const targetSize = isPortrait ? `${pixelHeight}px` : `${pixelWidth}px`;

                    img.parentNode.querySelectorAll('source').forEach(source => {
                        source.sizes = targetSize;
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

            window.addEventListener("updateResponsiveImages", function() {
                requestAnimationFrame(() => {
                    triggerResponsiveImageUpdate();
                });
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
        {{ $attributes->merge(['class' => 'w-full h-auto']) }}
        @if (!$loading) loading="lazy" @endif
        src="{{ $conversionDefaultUrl }}">
</picture>
