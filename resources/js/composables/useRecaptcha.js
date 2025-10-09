import { ref, onMounted } from 'vue';

export function useRecaptcha() {
    const isLoaded = ref(false);
    const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;

    const loadScript = () => {
        return new Promise((resolve, reject) => {
            if (window.grecaptcha) {
                isLoaded.value = true;
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
            script.async = true;
            script.defer = true;
            script.onload = () => {
                isLoaded.value = true;
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    };

    const execute = async (action = 'contest_submit') => {
        // If reCAPTCHA is not configured or disabled, return null
        if (!siteKey || siteKey === 'your_recaptcha_site_key_here') {
            console.warn('reCAPTCHA not configured, skipping');
            return null;
        }

        try {
            if (!isLoaded.value) {
                await loadScript();
            }

            await window.grecaptcha.ready(() => {});

            const token = await window.grecaptcha.execute(siteKey, { action });
            return token;
        } catch (error) {
            console.error('reCAPTCHA execution failed:', error);
            return null;
        }
    };

    onMounted(() => {
        if (siteKey && siteKey !== 'your_recaptcha_site_key_here') {
            loadScript().catch(error => {
                console.error('Failed to load reCAPTCHA:', error);
            });
        }
    });

    return {
        isLoaded,
        execute,
    };
}
