/** Tarayıcı izni ile koordinat çerezi — backend sıralaması için. Bir kez hafızaya göre yenile. */
export function bindLocationCookies() {
    const has = document.cookie.includes('simdibildir_lat=') && document.cookie.includes('simdibildir_lng=');
    if (has) return;

    if (!('geolocation' in navigator)) return;

    navigator.geolocation.getCurrentPosition(
        pos => {
            const maxAge = 60 * 60 * 24 * 14;
            const mk = (n, v) =>
                `${n}=${encodeURIComponent(String(v))};path=/;max-age=${maxAge};SameSite=Lax`;
            document.cookie = mk('simdibildir_lat', pos.coords.latitude);
            document.cookie = mk('simdibildir_lng', pos.coords.longitude);

            if (!sessionStorage.getItem('simdibildir_geo_boot')) {
                sessionStorage.setItem('simdibildir_geo_boot', '1');
                window.location.reload();
            }
        },
        () => {},
        { enableHighAccuracy: false, timeout: 8000, maximumAge: 300_000 }
    );
}

document.addEventListener('DOMContentLoaded', bindLocationCookies);
