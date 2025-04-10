@php
$shareUrl = urlencode($url);
$shareTitle = urlencode($title);
@endphp

<div class="social-share-buttons">
    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" 
       target="_blank" 
       class="btn btn-facebook btn-icon me-2">
        <i class="ti ti-brand-facebook"></i>
    </a>

    <!-- Twitter/X -->
    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" 
       target="_blank" 
       class="btn btn-twitter btn-icon me-2">
        <i class="ti ti-brand-twitter"></i>
    </a>

    <!-- WhatsApp -->
    <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" 
       target="_blank" 
       class="btn btn-success btn-icon me-2">
        <i class="ti ti-brand-whatsapp"></i>
    </a>

    <!-- LinkedIn -->
    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $shareUrl }}&title={{ $shareTitle }}" 
       target="_blank" 
       class="btn btn-linkedin btn-icon me-2">
        <i class="ti ti-brand-linkedin"></i>
    </a>

    <!-- Copy Link -->
    <button type="button" 
            class="btn btn-secondary btn-icon copy-link" 
            data-url="{{ $url }}" 
            title="{{ __('Copy Link') }}">
        <i class="ti ti-copy"></i>
    </button>
</div>

@push('scripts')
<script>
document.querySelectorAll('.copy-link').forEach(button => {
    button.addEventListener('click', function() {
        const url = this.getAttribute('data-url');
        navigator.clipboard.writeText(url).then(() => {
            // يمكن إضافة إشعار هنا
            alert('{{ __("Link copied to clipboard!") }}');
        });
    });
});
</script>
@endpush
