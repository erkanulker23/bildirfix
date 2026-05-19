@once
    @push('head')
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Quill === 'undefined') {
                    return;
                }
                document.querySelectorAll('.psc-quill-mount').forEach(function (mount) {
                    if (mount.dataset.quillReady === '1') {
                        return;
                    }
                    const inputId = mount.dataset.quillInput;
                    const hidden = inputId ? document.getElementById(inputId) : null;
                    if (!hidden) {
                        return;
                    }
                    const editor = new Quill(mount, {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ header: [2, 3, false] }],
                                ['bold', 'italic', 'underline', 'link'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['blockquote'],
                                ['clean'],
                            ],
                        },
                    });
                    if (hidden.value && hidden.value.trim() !== '') {
                        editor.clipboard.dangerouslyPasteHTML(hidden.value);
                    }
                    mount.dataset.quillReady = '1';
                    mount._quillInstance = editor;
                });
                document.querySelectorAll('form').forEach(function (form) {
                    if (form.dataset.quillBound === '1') {
                        return;
                    }
                    if (!form.querySelector('.psc-quill-mount')) {
                        return;
                    }
                    form.dataset.quillBound = '1';
                    form.addEventListener('submit', function () {
                        form.querySelectorAll('.psc-quill-mount').forEach(function (mount) {
                            const inputId = mount.dataset.quillInput;
                            const hidden = inputId ? document.getElementById(inputId) : null;
                            const instance = mount._quillInstance;
                            if (hidden && instance) {
                                hidden.value = instance.root.innerHTML;
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
