import Alpine from 'alpinejs';
import './geo';

Alpine.data('dsStoryViewer', () => ({
    visible: false,
    stories: [],
    currentIndex: 0,
    progress: 0,
    timer: null,
    touchStartX: 0,

    get currentStory() {
        return this.stories[this.currentIndex] ?? null;
    },

    open(stories, startIndex = 0) {
        if (!Array.isArray(stories) || stories.length === 0) {
            return;
        }
        this.stories = stories;
        this.currentIndex = Math.min(Math.max(0, startIndex), stories.length - 1);
        this.visible = true;
        document.body.style.overflow = 'hidden';
        // $nextTick bazı ortamlarda bileşen örneğinde eksik kalabiliyor; Alpine.nextTick garanti.
        Alpine.nextTick(() => this.startProgress());
    },

    closeViewer() {
        this.stopProgress();
        this.visible = false;
        document.body.style.overflow = '';
    },

    startProgress() {
        this.stopProgress();
        this.progress = 0;
        const story = this.currentStory;
        const duration = story?.media_type === 'video' ? 30000 : 7000;
        const interval = 50;
        const step = (interval / duration) * 100;
        this.timer = window.setInterval(() => {
            this.progress += step;
            if (this.progress >= 100) {
                this.nextStory();
            }
        }, interval);
    },

    stopProgress() {
        if (this.timer !== null) {
            window.clearInterval(this.timer);
            this.timer = null;
        }
    },

    nextStory() {
        this.stopProgress();
        if (this.currentIndex < this.stories.length - 1) {
            this.currentIndex += 1;
            this.startProgress();
        } else {
            this.closeViewer();
        }
    },

    prevStory() {
        this.stopProgress();
        if (this.currentIndex > 0) {
            this.currentIndex -= 1;
            this.startProgress();
        }
    },

    handleViewerClick(event) {
        const x = event.clientX;
        const third = window.innerWidth / 3;
        if (x < third) {
            this.prevStory();
        } else {
            this.nextStory();
        }
    },

    handleTouchStart(event) {
        this.touchStartX = event.touches[0].clientX;
    },

    handleTouchEnd(event) {
        const diff = this.touchStartX - event.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                this.nextStory();
            } else {
                this.prevStory();
            }
        }
    },
}));

Alpine.data('dsQuickComplaint', (initial = {}) => ({
    wizardStep:
        Number(initial.wizardStep) >= 1 && Number(initial.wizardStep) <= 3
            ? Number(initial.wizardStep)
            : 0,
    categoryId:
        initial.categoryId !== undefined && initial.categoryId !== null && String(initial.categoryId) !== ''
            ? String(initial.categoryId)
            : '',
    urls: typeof initial.urls === 'object' && initial.urls !== null ? initial.urls : {},
    cityId: String(initial.cityId ?? ''),
    districtId: String(initial.districtId ?? ''),
    districts: [],
    neighborhoods: [],
    neighborhoodTurkiyeId: String(initial.neighborhoodTurkiyeId ?? ''),
    neighborhoodName: String(initial.neighborhoodName ?? ''),
    latitude:
        initial.latitude !== undefined && initial.latitude !== null && String(initial.latitude) !== ''
            ? String(initial.latitude)
            : '',
    longitude:
        initial.longitude !== undefined && initial.longitude !== null && String(initial.longitude) !== ''
            ? String(initial.longitude)
            : '',
    institutionSearch: '',
    institutionHits: [],
    institutionSearchLoading: false,
    selectedInstitutions: Array.isArray(initial.selectedInstitutions) ? [...initial.selectedInstitutions] : [],
    suggestedInstitutions: Array.isArray(initial.suggestedInstitutions) ? [...initial.suggestedInstitutions] : [],
    speechSupported: false,
    speechListening: false,
    speechRecognition: null,
    speechBaseText: '',
    locationResolving: false,
    imagePreviewItems: [],
    videoPreviewItems: [],
    imagesDropActive: false,
    videosDropActive: false,

    async initQuick() {
        this.initSpeechInput();
        await this.loadDistricts();
        if (this.districtId) {
            await this.loadNeighborhoods();
        }
        this.syncPreviewImg();
        this.syncImagesToInput();
        this.syncVideosToInput();
    },

    initSpeechInput() {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) {
            this.speechSupported = false;
            return;
        }
        this.speechSupported = true;
        const rec = new SR();
        rec.lang = 'tr-TR';
        rec.continuous = true;
        rec.interimResults = true;
        rec.onresult = (event) => {
            const el = document.getElementById('quick-description');
            if (!el) {
                return;
            }
            let finalPart = '';
            let interimPart = '';
            for (let i = 0; i < event.results.length; i += 1) {
                const piece = event.results[i][0]?.transcript ?? '';
                if (event.results[i].isFinal) {
                    finalPart += piece;
                } else {
                    interimPart += piece;
                }
            }
            const base = this.speechBaseText || '';
            const combined = `${base}${finalPart}${interimPart}`.replace(/\s+/g, ' ').trim();
            el.value = combined;
            el.dispatchEvent(new Event('input', { bubbles: true }));
        };
        rec.onerror = () => {
            this.speechListening = false;
            window.dsToast?.('Ses tanıma başarısız oldu.', 'error');
        };
        rec.onend = () => {
            this.speechListening = false;
            const el = document.getElementById('quick-description');
            if (el?.value?.trim()) {
                this.speechBaseText = `${el.value.trim()} `;
            }
        };
        this.speechRecognition = rec;
    },

    toggleSpeechInput() {
        if (!this.speechSupported || !this.speechRecognition) {
            window.dsToast?.('Sesli yazma bu tarayıcıda desteklenmiyor.', 'info');
            return;
        }
        if (this.speechListening) {
            this.speechRecognition.stop();
            this.speechListening = false;
            const el = document.getElementById('quick-description');
            if (el) {
                this.speechBaseText = el.value?.trim() ? `${el.value.trim()} ` : '';
            }
            return;
        }
        try {
            const el = document.getElementById('quick-description');
            const current = el?.value?.trim() || '';
            this.speechBaseText = current ? `${current} ` : '';
            this.speechRecognition.start();
            this.speechListening = true;
            window.dsToast?.('Mikrofon açık — konuşabilirsiniz.', 'success');
        } catch {
            window.dsToast?.('Mikrofon izni gerekli.', 'error');
        }
    },

    flushPicker(id) {
        const el = document.getElementById(id);
        if (el) {
            el.value = '';
        }
    },

    syncImagesToInput() {
        const el = document.getElementById('quick-images-files');
        if (!el) {
            return;
        }
        const dt = new DataTransfer();
        this.imagePreviewItems.forEach((row) => {
            dt.items.add(row.file);
        });
        el.files = dt.files;
    },

    syncVideosToInput() {
        const el = document.getElementById('quick-videos-files');
        if (!el) {
            return;
        }
        const dt = new DataTransfer();
        this.videoPreviewItems.forEach((row) => {
            dt.items.add(row.file);
        });
        el.files = dt.files;
    },

    mergeImageFiles(fileList) {
        const max = Number(window.__complaintMaxImages ?? 5);
        const maxBytes = Number(window.__complaintImageMaxKb ?? 6144) * 1024;
        const looksLikeImage = (file) =>
            /^image\/(jpeg|png|gif|webp)$/i.test(file.type) ||
            (!file.type && /\.(jpe?g|png|gif|webp)$/i.test(file.name));

        let badType = false;
        const files = Array.from(fileList || []);
        for (const file of files) {
            if (this.imagePreviewItems.length >= max) {
                window.dsToast?.(`En fazla ${max} fotoğraf ekleyebilirsin.`, 'error');
                break;
            }
            if (!looksLikeImage(file)) {
                badType = true;
                continue;
            }
            if (file.size > maxBytes) {
                window.dsToast?.(
                    `${file.name}: çok büyük (en fazla ${Math.round(maxBytes / 1024)} KB).`,
                    'error',
                );
                continue;
            }
            const url = URL.createObjectURL(file);
            this.imagePreviewItems.push({
                key: `img-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`,
                url,
                file,
            });
        }
        if (badType) {
            window.dsToast?.('Yalnızca JPEG, PNG, WebP veya GIF yüklenebilir.', 'info');
        }
        this.syncImagesToInput();
    },

    mergeVideoFiles(fileList) {
        const max = Number(window.__complaintMaxVideos ?? 2);
        const maxBytes = Number(window.__complaintVideoMaxKb ?? 35840) * 1024;
        let badType = false;
        const files = Array.from(fileList || []);
        for (const file of files) {
            if (this.videoPreviewItems.length >= max) {
                window.dsToast?.(`En fazla ${max} video ekleyebilirsin.`, 'error');
                break;
            }
            const okMime = /^video\/(mp4|webm)$/i.test(file.type);
            const okExt = /\.(mp4|webm)$/i.test(file.name);
            if (!okMime && !okExt) {
                badType = true;
                continue;
            }
            if (file.size > maxBytes) {
                window.dsToast?.(
                    `${file.name}: çok büyük (en fazla ${Math.round(maxBytes / 1024)} KB).`,
                    'error',
                );
                continue;
            }
            const url = URL.createObjectURL(file);
            this.videoPreviewItems.push({
                key: `vid-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`,
                url,
                file,
            });
        }
        if (badType) {
            window.dsToast?.('Yalnızca MP4 veya WebM yüklenebilir.', 'info');
        }
        this.syncVideosToInput();
    },

    addImagesFromPicker(e) {
        this.mergeImageFiles(e.target.files);
        this.flushPicker('quick-images-picker');
    },

    addVideosFromPicker(e) {
        this.mergeVideoFiles(e.target.files);
        this.flushPicker('quick-videos-picker');
    },

    onImagesDrop(e) {
        this.imagesDropActive = false;
        this.mergeImageFiles(e.dataTransfer?.files);
    },

    onVideosDrop(e) {
        this.videosDropActive = false;
        this.mergeVideoFiles(e.dataTransfer?.files);
    },

    imagesDropLeave(e) {
        const next = e.relatedTarget;
        if (next && e.currentTarget.contains(next)) {
            return;
        }
        this.imagesDropActive = false;
    },

    videosDropLeave(e) {
        const next = e.relatedTarget;
        if (next && e.currentTarget.contains(next)) {
            return;
        }
        this.videosDropActive = false;
    },

    removeImagePreview(key) {
        const idx = this.imagePreviewItems.findIndex((x) => x.key === key);
        if (idx === -1) {
            return;
        }
        const row = this.imagePreviewItems[idx];
        if (row.url && String(row.url).startsWith('blob:')) {
            URL.revokeObjectURL(row.url);
        }
        this.imagePreviewItems.splice(idx, 1);
        this.syncImagesToInput();
    },

    removeVideoPreview(key) {
        const idx = this.videoPreviewItems.findIndex((x) => x.key === key);
        if (idx === -1) {
            return;
        }
        const row = this.videoPreviewItems[idx];
        if (row.url && String(row.url).startsWith('blob:')) {
            URL.revokeObjectURL(row.url);
        }
        this.videoPreviewItems.splice(idx, 1);
        this.syncVideosToInput();
    },

    async onCityChanged() {
        this.districtId = '';
        this.neighborhoodTurkiyeId = '';
        this.neighborhoodName = '';
        this.neighborhoods = [];
        await this.loadDistricts();
        this.institutionHits = [];
    },

    async onDistrictChanged() {
        this.neighborhoodTurkiyeId = '';
        this.neighborhoodName = '';
        await this.loadNeighborhoods();
    },

    async loadDistricts() {
        if (!this.cityId || !this.urls.districts) {
            this.districts = [];
            return;
        }
        try {
            const r = await fetch(`${this.urls.districts}?city_id=${encodeURIComponent(this.cityId)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const j = await r.json();
            this.districts = Array.isArray(j.data) ? j.data : [];
            if (this.districtId && !this.districts.some((d) => String(d.id) === String(this.districtId))) {
                this.districtId = '';
            }
        } catch {
            this.districts = [];
            window.dsToast?.('İlçe listesi yüklenemedi.', 'error');
        }
    },

    async loadNeighborhoods() {
        if (!this.districtId || !this.urls.neighborhoods) {
            this.neighborhoods = [];
            return;
        }
        try {
            const r = await fetch(
                `${this.urls.neighborhoods}?district_id=${encodeURIComponent(this.districtId)}`,
                { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } },
            );
            const j = await r.json();
            this.neighborhoods = Array.isArray(j.data) ? j.data : [];
            if (
                this.neighborhoodTurkiyeId &&
                !this.neighborhoods.some((n) => String(n.id) === String(this.neighborhoodTurkiyeId))
            ) {
                this.neighborhoodTurkiyeId = '';
                this.neighborhoodName = '';
            } else if (this.neighborhoodTurkiyeId) {
                const row = this.neighborhoods.find((n) => String(n.id) === String(this.neighborhoodTurkiyeId));
                if (row?.name) {
                    this.neighborhoodName = row.name;
                }
            }
        } catch {
            this.neighborhoods = [];
            window.dsToast?.('Mahalle listesi yüklenemedi.', 'error');
        }
    },

    async runInstitutionSearch() {
        const q = String(this.institutionSearch || '').trim();
        if (q.length < 2 || !this.urls.institutions) {
            this.institutionHits = [];
            this.institutionSearchLoading = false;
            return;
        }
        this.institutionSearchLoading = true;
        try {
            let url = `${this.urls.institutions}?q=${encodeURIComponent(q)}`;
            const narrowCity = this.wizardStep >= 2 && this.cityId && String(this.cityId).trim() !== '';
            if (narrowCity) {
                url += `&city_id=${encodeURIComponent(this.cityId)}`;
            }
            const r = await fetch(url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!r.ok) {
                throw new Error('bad status');
            }
            const j = await r.json();
            const rows = Array.isArray(j.data) ? j.data : [];
            const sel = new Set(this.selectedInstitutions.map((x) => Number(x.id)));
            this.institutionHits = rows.filter((row) => row && !sel.has(Number(row.id)));
        } catch {
            this.institutionHits = [];
            window.dsToast?.('Kurum araması yapılamadı.', 'error');
        } finally {
            this.institutionSearchLoading = false;
        }
    },

    addInstitution(row) {
        if (!row || row.id === undefined || row.id === null) {
            return;
        }
        const id = Number(row.id);
        if (!Number.isFinite(id) || id < 1) {
            return;
        }
        if (this.selectedInstitutions.some((x) => Number(x.id) === id)) {
            return;
        }
        if (this.selectedInstitutions.length >= 20) {
            window.dsToast?.('En fazla 20 kurum seçebilirsin.', 'error');
            return;
        }
        this.selectedInstitutions.push({
            id,
            name: String(row.name || ''),
        });
        this.institutionSearch = '';
        this.institutionHits = [];
    },

    removeInstitution(id) {
        const n = Number(id);
        this.selectedInstitutions = this.selectedInstitutions.filter((x) => Number(x.id) !== n);
    },

    async pullLocation() {
        if (!('geolocation' in navigator)) {
            window.dsToast?.('Tarayıcı konum desteklemiyor.', 'error');
            return;
        }
        if (this.locationResolving) {
            return;
        }
        this.locationResolving = true;
        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                const lat = Math.round(pos.coords.latitude * 1e7) / 1e7;
                const lng = Math.round(pos.coords.longitude * 1e7) / 1e7;
                this.latitude = String(lat);
                this.longitude = String(lng);
                await this.resolveAddressFromCoords(lat, lng);
                this.locationResolving = false;
            },
            () => {
                this.locationResolving = false;
                window.dsToast?.('Konum izni reddedildi veya alınamadı.', 'error');
            },
            { enableHighAccuracy: true, timeout: 14000, maximumAge: 0 },
        );
    },

    async resolveAddressFromCoords(lat, lng) {
        if (!this.urls.reverse) {
            window.dsToast?.('Konum alındı.', 'success');
            return;
        }
        try {
            const r = await fetch(
                `${this.urls.reverse}?lat=${encodeURIComponent(lat)}&lng=${encodeURIComponent(lng)}`,
                { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } },
            );
            if (!r.ok) {
                throw new Error('reverse failed');
            }
            const j = await r.json();
            const data = j.data || {};
            if (data.city_id) {
                this.cityId = String(data.city_id);
                await this.loadDistricts();
            }
            if (data.district_id) {
                this.districtId = String(data.district_id);
                await this.loadNeighborhoods();
            }
            if (data.neighborhood_turkiye_id) {
                this.neighborhoodTurkiyeId = String(data.neighborhood_turkiye_id);
                this.neighborhoodName = String(data.neighborhood_name || '');
            }
            const parts = [data.city_name, data.district_name, data.neighborhood_name].filter(Boolean);
            if (parts.length > 0) {
                window.dsToast?.(`Adres: ${parts.join(' · ')}`, 'success');
            } else {
                window.dsToast?.('Konum alındı; il/ilçe eşleşmedi — listeden seçin.', 'info');
            }
        } catch {
            window.dsToast?.('Konum alındı; adres otomatik doldurulamadı.', 'info');
        }
    },

    clearLocation() {
        this.latitude = '';
        this.longitude = '';
    },

    mapEmbedSrc() {
        const lat = parseFloat(this.latitude);
        const lng = parseFloat(this.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return '';
        }
        const latF = Math.round(lat * 1e7) / 1e7;
        const lngF = Math.round(lng * 1e7) / 1e7;
        const z = 17;
        const dlat = 0.004 * Math.max(0.35, 2 ** Math.max(0, 17 - Math.min(19, z)));
        const dlng = dlat / Math.max(0.2, Math.cos((latF * Math.PI) / 180));
        const minLon = lngF - dlng;
        const minLat = latF - dlat;
        const maxLon = lngF + dlng;
        const maxLat = latF + dlat;
        const bboxStr = `${minLon},${minLat},${maxLon},${maxLat}`;
        return `https://www.openstreetmap.org/export/embed.html?bbox=${encodeURIComponent(bboxStr)}&layer=mapnik&marker=${latF}%2C${lngF}`;
    },

    hasMapCoords() {
        return this.mapEmbedSrc() !== '';
    },

    nextStep() {
        if (this.wizardStep === 1) {
            const desc = document.getElementById('quick-description')?.value?.trim() || '';
            if (!desc) {
                window.dsToast?.('Lütfen sorunu yazın.', 'error');
                return;
            }
            this.wizardStep = 2;
            return;
        }
        if (this.wizardStep === 2) {
            if (!this.cityId || String(this.cityId).trim() === '') {
                window.dsToast?.('Lütfen ili seçin.', 'error');
                return;
            }
            if (!this.districtId || String(this.districtId).trim() === '') {
                window.dsToast?.('Lütfen ilçe seçin.', 'error');
                return;
            }
            if (!this.neighborhoodTurkiyeId || String(this.neighborhoodTurkiyeId).trim() === '') {
                window.dsToast?.('Lütfen mahalle seçin.', 'error');
                return;
            }
            this.wizardStep = 3;
        }
    },

    prevStep() {
        if (this.wizardStep > 0) {
            this.wizardStep -= 1;
        }
    },

    startComplaintWizard() {
        this.wizardStep = 1;
    },

    onNeighborhoodPick(e) {
        const opt = e.target.selectedOptions[0];
        this.neighborhoodName = opt ? String(opt.textContent || '').trim() : '';
    },

    syncPreviewImg() {
        const urlEl = document.querySelector('#quick-complaint-form input[name="media_url"]');
        const u = urlEl?.value?.trim() || '';
        const preview = document.getElementById('quick-media-preview');
        if (preview) {
            preview.src = u || '';
            preview.classList.toggle('hidden', u === '');
        }
        const emptyHint = document.getElementById('quick-media-empty');
        if (emptyHint) {
            emptyHint.classList.toggle('hidden', u !== '');
        }
    },

    guardQuickComplaint(event) {
        if (this.wizardStep !== 3) {
            event.preventDefault();
            window.dsToast?.('Devam etmek için özet adımına gelin.', 'error');
            return;
        }
        const titleEl = document.getElementById('quick-title');
        const title = titleEl?.value?.trim() || '';
        if (!title) {
            event.preventDefault();
            window.dsToast?.('Lütfen kısa başlık yazın.', 'error');
            return;
        }
        if (!this.districtId || String(this.districtId).trim() === '') {
            event.preventDefault();
            window.dsToast?.('Lütfen ilçe seçin.', 'error');
            return;
        }
        if (!this.neighborhoodTurkiyeId || String(this.neighborhoodTurkiyeId).trim() === '') {
            event.preventDefault();
            window.dsToast?.('Lütfen mahalle seçin.', 'error');
            return;
        }
    },
}));

window.Alpine = Alpine;

Alpine.start();

function resolveStoryViewerRoot(el) {
    if (!el) {
        return null;
    }
    const A = window.Alpine;
    if (A && typeof A.$data === 'function') {
        try {
            const d = A.$data(el);
            if (d && typeof d.open === 'function') {
                return d;
            }
        } catch {
            /* Alpine $data bazen henüz hazır değil */
        }
    }
    const stack = el._x_dataStack;
    if (Array.isArray(stack) && stack.length > 0) {
        const raw = stack[0];
        if (raw && typeof raw.open === 'function') {
            return raw;
        }
    }
    return null;
}

window.dsOpenStory = function dsOpenStory(storyId, storiesPayload) {
    const el = document.getElementById('story-viewer');
    const list = Array.isArray(storiesPayload) ? storiesPayload : [];
    if (list.length === 0) {
        window.dsToast?.('Bu listede açılacak hikâye verisi yok.', 'info');
        return;
    }
    const root = resolveStoryViewerRoot(el);
    if (!root) {
        window.dsToast?.('Hikâye oynatıcı hazır değil. Sayfayı yenileyin.', 'error');
        return;
    }
    const sid = String(storyId);
    const idx = list.findIndex((s) => String(s?.id) === sid);
    root.open(list, idx >= 0 ? idx : 0);
};

window.dsSharePost = function dsSharePost(postId) {
    const url = `${window.location.origin}/sikayet/${postId}`;
    window.dsSharePage(url, document.title);
};

/** Şikâyet / sayfa paylaşımı — giriş gerektirmez. */
window.dsSharePage = function dsSharePage(url, title) {
    const t = title && String(title).trim() !== '' ? String(title) : document.title;
    if (navigator.share) {
        navigator.share({ url, title: t }).catch(() => {});
    } else {
        navigator.clipboard.writeText(url).then(
            () => window.dsToast?.('Link kopyalandı', 'success'),
            () => window.dsToast?.('Link kopyalanamadı', 'error'),
        );
    }
};

window.dsToast = function dsToast(message, type = 'info') {
    const colors = {
        success: 'bg-success text-white',
        error: 'bg-danger text-white',
        info: 'bg-gray-900 text-white',
    };
    const toast = document.createElement('div');
    toast.className = `fixed bottom-24 left-1/2 z-[1001] max-w-[90vw] -translate-x-1/2 rounded-full px-5 py-3 text-sm font-semibold shadow-lg transition ${colors[type] ?? colors.info}`;
    toast.textContent = message;
    toast.setAttribute('role', 'status');
    document.body.appendChild(toast);
    window.setTimeout(() => toast.remove(), 3200);
};

window.dsOpenCreateStory = function dsOpenCreateStory() {
    window.dsToast?.('Hikâye oluşturma yakında eklenecek.', 'info');
};
