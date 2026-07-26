<script>
    (() => {
        const nativeFetch = window.fetch.bind(window);

        window.fetch = async (input, init = {}) => {
            const headers = new Headers(init.headers || {});
            if (!headers.has('Accept')) {
                headers.set('Accept', 'application/json');
            }

            const response = await nativeFetch(input, {
                ...init,
                headers,
                credentials: init.credentials || 'same-origin',
            });

            const redirectedToLogin = response.redirected
                && new URL(response.url, window.location.origin).pathname === @json(route('login', absolute: false));

            if (response.status === 401 || redirectedToLogin) {
                window.location.assign(@json(route('login', absolute: false)));
            }

            return response;
        };
    })();
</script>
