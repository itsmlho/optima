<?php

namespace App\Services;

/**
 * Lazy Loading Service
 * Mengimplementasikan lazy loading untuk images dan components
 */
class LazyLoadingService
{
    protected $config;
    
    public function __construct()
    {
        $this->config = [
            'placeholder_image' => '/assets/images/placeholder.svg',
            'loading_gif' => '/assets/images/loading.gif',
            'lazy_class' => 'lazy-load',
            'threshold' => '50px',
            'fade_duration' => 300
        ];
    }

    /**
     * Generate lazy loading image tag
     */
    public function lazyImage($src, $alt = '', $class = '', $attributes = [])
    {
        $lazyClass = trim($this->config['lazy_class'] . ' ' . $class);
        
        $defaultAttributes = [
            'data-src' => $src,
            'alt' => $alt,
            'class' => $lazyClass,
            'loading' => 'lazy'
        ];
        
        // Merge dengan attributes tambahan
        $allAttributes = array_merge($defaultAttributes, $attributes);
        
        // Set placeholder sebagai src awal
        $allAttributes['src'] = $this->config['placeholder_image'];
        
        // Generate attribute string
        $attributeString = '';
        foreach ($allAttributes as $key => $value) {
            $attributeString .= sprintf('%s="%s" ', $key, htmlspecialchars($value));
        }
        
        return sprintf('<img %s/>', trim($attributeString));
    }

    /**
     * Generate lazy loading background image div
     */
    public function lazyBackground($src, $content = '', $class = '', $attributes = [])
    {
        $lazyClass = trim($this->config['lazy_class'] . ' lazy-bg ' . $class);
        
        $defaultAttributes = [
            'data-bg' => $src,
            'class' => $lazyClass
        ];
        
        $allAttributes = array_merge($defaultAttributes, $attributes);
        
        $attributeString = '';
        foreach ($allAttributes as $key => $value) {
            $attributeString .= sprintf('%s="%s" ', $key, htmlspecialchars($value));
        }
        
        return sprintf('<div %s>%s</div>', trim($attributeString), $content);
    }

    /**
     * Generate lazy loading iframe
     */
    public function lazyIframe($src, $class = '', $attributes = [])
    {
        $lazyClass = trim($this->config['lazy_class'] . ' ' . $class);
        
        $defaultAttributes = [
            'data-src' => $src,
            'class' => $lazyClass,
            'loading' => 'lazy'
        ];
        
        $allAttributes = array_merge($defaultAttributes, $attributes);
        
        $attributeString = '';
        foreach ($allAttributes as $key => $value) {
            $attributeString .= sprintf('%s="%s" ', $key, htmlspecialchars($value));
        }
        
        return sprintf('<iframe %s></iframe>', trim($attributeString));
    }

    /**
     * Generate JavaScript untuk lazy loading implementation
     */
    public function getLazyLoadingScript()
    {
        $threshold = $this->config['threshold'];
        $fadeDuration = $this->config['fade_duration'];
        $lazyClass = $this->config['lazy_class'];
        
        return "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer untuk lazy loading
            const lazyObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        
                        if (element.tagName === 'IMG') {
                            loadLazyImage(element);
                        } else if (element.classList.contains('lazy-bg')) {
                            loadLazyBackground(element);
                        } else if (element.tagName === 'IFRAME') {
                            loadLazyIframe(element);
                        }
                        
                        observer.unobserve(element);
                    }
                });
            }, {
                rootMargin: '{$threshold}',
                threshold: 0.1
            });

            // Function untuk load lazy image
            function loadLazyImage(img) {
                const dataSrc = img.getAttribute('data-src');
                if (!dataSrc) return;

                // Create new image untuk preload
                const newImg = new Image();
                newImg.onload = function() {
                    // Fade effect
                    img.style.opacity = '0';
                    img.style.transition = 'opacity {$fadeDuration}ms ease-in-out';
                    
                    img.src = dataSrc;
                    img.removeAttribute('data-src');
                    
                    // Fade in
                    setTimeout(() => {
                        img.style.opacity = '1';
                        img.classList.add('loaded');
                        img.classList.remove('{$lazyClass}');
                    }, 50);
                };
                newImg.onerror = function() {
                    img.classList.add('error');
                    img.alt = 'Image failed to load';
                };
                newImg.src = dataSrc;
            }

            // Function untuk load lazy background
            function loadLazyBackground(element) {
                const dataBg = element.getAttribute('data-bg');
                if (!dataBg) return;

                // Preload background image
                const img = new Image();
                img.onload = function() {
                    element.style.backgroundImage = 'url(' + dataBg + ')';
                    element.removeAttribute('data-bg');
                    element.classList.add('loaded');
                    element.classList.remove('{$lazyClass}');
                };
                img.src = dataBg;
            }

            // Function untuk load lazy iframe
            function loadLazyIframe(iframe) {
                const dataSrc = iframe.getAttribute('data-src');
                if (!dataSrc) return;

                iframe.src = dataSrc;
                iframe.removeAttribute('data-src');
                iframe.classList.add('loaded');
                iframe.classList.remove('{$lazyClass}');
            }

            // Observe semua lazy elements
            const lazyElements = document.querySelectorAll('.{$lazyClass}');
            lazyElements.forEach(element => {
                lazyObserver.observe(element);
            });

            // Fallback untuk browser yang tidak support Intersection Observer
            if (!('IntersectionObserver' in window)) {
                lazyElements.forEach(element => {
                    if (element.tagName === 'IMG') {
                        loadLazyImage(element);
                    } else if (element.classList.contains('lazy-bg')) {
                        loadLazyBackground(element);
                    } else if (element.tagName === 'IFRAME') {
                        loadLazyIframe(element);
                    }
                });
            }
        });
        </script>
        ";
    }

    /**
     * Generate CSS untuk lazy loading
     */
    public function getLazyLoadingCSS()
    {
        $lazyClass = $this->config['lazy_class'];
        $fadeDuration = $this->config['fade_duration'];
        
        return "
        <style>
        .{$lazyClass} {
            opacity: 0;
            transition: opacity {$fadeDuration}ms ease-in-out;
        }
        
        .{$lazyClass}.loaded {
            opacity: 1;
        }
        
        .{$lazyClass}.error {
            opacity: 0.5;
            filter: grayscale(100%);
        }
        
        /* Placeholder styles */
        .{$lazyClass}:not(.loaded):not(.error) {
            background-color: #f0f0f0;
            background-image: 
                linear-gradient(45deg, transparent 25%, rgba(255,255,255,.5) 25%, rgba(255,255,255,.5) 75%, transparent 75%, transparent),
                linear-gradient(45deg, transparent 25%, rgba(255,255,255,.5) 25%, rgba(255,255,255,.5) 75%, transparent 75%, transparent);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            animation: lazy-loading 2s linear infinite;
        }
        
        @keyframes lazy-loading {
            0% { background-position: 0px 0px, 10px 10px; }
            100% { background-position: 20px 20px, 30px 30px; }
        }
        
        /* Responsive lazy images */
        .lazy-image-container {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }
        
        .lazy-image-container img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        /* Loading spinner untuk lazy content */
        .lazy-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Hide lazy content until loaded */
        .lazy-content {
            display: none;
        }
        
        .lazy-content.loaded {
            display: block;
        }
        </style>
        ";
    }

    /**
     * Create lazy loading untuk DataTable images
     */
    public function lazyDataTableImage($src, $alt = '', $width = '50px', $height = '50px')
    {
        $attributes = [
            'style' => "width: {$width}; height: {$height}; object-fit: cover;",
            'data-width' => $width,
            'data-height' => $height
        ];
        
        return $this->lazyImage($src, $alt, 'datatable-image', $attributes);
    }

    /**
     * Lazy loading untuk gallery images
     */
    public function lazyGalleryImage($src, $thumbnail = '', $alt = '', $class = '')
    {
        $thumbSrc = $thumbnail ?: $this->generateThumbnail($src);
        
        $attributes = [
            'data-full' => $src,
            'data-thumbnail' => $thumbSrc,
            'onclick' => 'openLazyModal(this)'
        ];
        
        return $this->lazyImage($thumbSrc, $alt, "gallery-image {$class}", $attributes);
    }

    /**
     * Generate thumbnail path
     */
    protected function generateThumbnail($imagePath)
    {
        $pathInfo = pathinfo($imagePath);
        return $pathInfo['dirname'] . '/thumbs/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
    }

    /**
     * Lazy loading untuk content sections
     */
    public function lazyContent($contentId, $loadUrl, $placeholder = 'Loading...')
    {
        return "
        <div id='{$contentId}' class='lazy-content-container' data-load-url='{$loadUrl}'>
            <div class='lazy-placeholder'>
                <div class='lazy-spinner'></div>
                {$placeholder}
            </div>
            <div class='lazy-content'></div>
        </div>
        ";
    }

    /**
     * JavaScript untuk lazy content loading
     */
    public function getLazyContentScript()
    {
        return "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lazyContentObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        loadLazyContent(entry.target);
                        lazyContentObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            function loadLazyContent(container) {
                const loadUrl = container.getAttribute('data-load-url');
                const contentDiv = container.querySelector('.lazy-content');
                const placeholder = container.querySelector('.lazy-placeholder');

                if (!loadUrl || !contentDiv) return;

                fetch(loadUrl)
                    .then(response => response.text())
                    .then(html => {
                        placeholder.style.display = 'none';
                        contentDiv.innerHTML = html;
                        contentDiv.classList.add('loaded');
                        
                        // Trigger event untuk post-load actions
                        const event = new CustomEvent('lazyContentLoaded', {
                            detail: { container, loadUrl }
                        });
                        document.dispatchEvent(event);
                    })
                    .catch(error => {
                        console.error('Error loading lazy content:', error);
                        placeholder.innerHTML = '<div class=\"error\">Failed to load content</div>';
                    });
            }

            // Observe lazy content containers
            document.querySelectorAll('.lazy-content-container').forEach(container => {
                lazyContentObserver.observe(container);
            });
        });
        </script>
        ";
    }

    /**
     * Generate complete lazy loading setup
     */
    public function renderLazyLoadingSetup($includeCSS = true, $includeJS = true)
    {
        $output = '';
        
        if ($includeCSS) {
            $output .= $this->getLazyLoadingCSS();
        }
        
        if ($includeJS) {
            $output .= $this->getLazyLoadingScript();
            $output .= $this->getLazyContentScript();
        }
        
        return $output;
    }

    /**
     * Helper untuk convert existing img tags ke lazy loading
     */
    public function convertToLazyLoading($html)
    {
        // Convert img tags
        $html = preg_replace_callback(
            '/<img([^>]*?)src=["\']([^"\']*)["\']([^>]*?)>/i',
            function($matches) {
                $beforeSrc = $matches[1];
                $src = $matches[2];
                $afterSrc = $matches[3];
                
                // Skip jika sudah lazy loading
                if (strpos($matches[0], 'data-src') !== false) {
                    return $matches[0];
                }
                
                $lazyClass = $this->config['lazy_class'];
                $placeholder = $this->config['placeholder_image'];
                
                // Extract class jika ada
                preg_match('/class=["\']([^"\']*)["\']/', $beforeSrc . $afterSrc, $classMatch);
                $existingClass = isset($classMatch[1]) ? $classMatch[1] : '';
                $newClass = trim($existingClass . ' ' . $lazyClass);
                
                // Remove existing class attribute
                $beforeSrc = preg_replace('/class=["\'][^"\']*["\']/', '', $beforeSrc);
                $afterSrc = preg_replace('/class=["\'][^"\']*["\']/', '', $afterSrc);
                
                return sprintf(
                    '<img%s src="%s" data-src="%s" class="%s" loading="lazy"%s>',
                    $beforeSrc,
                    $placeholder,
                    $src,
                    $newClass,
                    $afterSrc
                );
            },
            $html
        );
        
        return $html;
    }

    /**
     * Performance monitoring untuk lazy loading
     */
    public function getLazyLoadingMetrics()
    {
        return "
        <script>
        // Monitor lazy loading performance
        window.lazyLoadingMetrics = {
            totalImages: 0,
            loadedImages: 0,
            failedImages: 0,
            loadTimes: []
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.lazyLoadingMetrics.totalImages = document.querySelectorAll('.{$this->config['lazy_class']}').length;
            
            // Monitor load events
            document.addEventListener('lazyImageLoaded', function(e) {
                window.lazyLoadingMetrics.loadedImages++;
                if (e.detail && e.detail.loadTime) {
                    window.lazyLoadingMetrics.loadTimes.push(e.detail.loadTime);
                }
            });
            
            document.addEventListener('lazyImageError', function(e) {
                window.lazyLoadingMetrics.failedImages++;
            });
        });

        // Function untuk get metrics
        function getLazyLoadingStats() {
            const metrics = window.lazyLoadingMetrics;
            const avgLoadTime = metrics.loadTimes.length > 0 
                ? metrics.loadTimes.reduce((a, b) => a + b) / metrics.loadTimes.length 
                : 0;
            
            return {
                total: metrics.totalImages,
                loaded: metrics.loadedImages,
                failed: metrics.failedImages,
                successRate: metrics.totalImages > 0 ? (metrics.loadedImages / metrics.totalImages * 100).toFixed(2) + '%' : '0%',
                averageLoadTime: avgLoadTime.toFixed(2) + 'ms'
            };
        }
        </script>
        ";
    }

    /**
     * Generate progressive image loading
     */
    public function progressiveImage($src, $lowQualitySrc = '', $alt = '', $class = '')
    {
        if (empty($lowQualitySrc)) {
            $lowQualitySrc = $this->generateLowQualityPlaceholder($src);
        }
        
        $attributes = [
            'data-src' => $src,
            'data-lqip' => $lowQualitySrc,
            'class' => trim($class . ' progressive-image'),
            'style' => 'filter: blur(5px); transition: filter 0.3s;'
        ];
        
        return $this->lazyImage($lowQualitySrc, $alt, 'progressive', $attributes);
    }

    /**
     * Generate low quality placeholder
     */
    protected function generateLowQualityPlaceholder($imagePath)
    {
        // Return a lower quality version path
        // This would typically be generated by your image processing system
        $pathInfo = pathinfo($imagePath);
        return $pathInfo['dirname'] . '/lq/' . $pathInfo['filename'] . '_lq.' . $pathInfo['extension'];
    }

    /**
     * Set configuration
     */
    public function setConfig($key, $value)
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * Get configuration
     */
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}