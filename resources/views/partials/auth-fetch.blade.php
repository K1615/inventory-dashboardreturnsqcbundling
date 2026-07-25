<script>
    (() => {
        const nativeFetch = window.fetch.bind(window);
        const loginUrl = @json(route('login'));

        window.fetch = async (input, init = {}) => {
            const requestUrl = new URL(
                typeof input === 'string' || input instanceof URL ? input : input.url,
                window.location.href,
            );
            const options = { ...init };
            const headers = new Headers(options.headers || {});
            const method = (options.method || (input instanceof Request ? input.method : 'GET')).toUpperCase();

            if (requestUrl.origin === window.location.origin) {
                options.credentials ??= 'same-origin';
                headers.set('Accept', 'application/json');

                if (! ['GET', 'HEAD', 'OPTIONS'].includes(method) && ! headers.has('X-CSRF-TOKEN')) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (token) {
                        headers.set('X-CSRF-TOKEN', token);
                    }
                }
            }

            options.headers = headers;
            const response = await nativeFetch(input, options);
            const redirectedToLogin = response.redirected
                && new URL(response.url, window.location.href).pathname === new URL(loginUrl).pathname;

            if (response.status === 401 || response.status === 419 || redirectedToLogin) {
                window.location.assign(loginUrl);

                return new Promise(() => {});
            }

            return response;
        };
    })();
</script>
