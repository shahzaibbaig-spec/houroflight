@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-6xl rounded-4 bg-white p-4 p-md-5 shadow-sm">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <h1 class="h2 mb-1 fw-bold">E-Resources Library</h1>
                <p class="mb-0 text-muted">Download and print worksheets from the Hour of Light resource collection.</p>
                <p class="mt-2 mb-0 small text-muted">
                    {{ number_format($resourceCount) }} resources indexed
                    @if($catalogGeneratedAt)
                        | Updated {{ \Illuminate\Support\Carbon::parse($catalogGeneratedAt)->format('Y-m-d H:i') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-lg-6">
                <label for="resource-search" class="form-label fw-semibold">Global Search</label>
                <input
                    id="resource-search"
                    type="search"
                    class="form-control"
                    placeholder="Search by worksheet title, class, subject, or keyword"
                    autocomplete="off"
                >
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="resource-class" class="form-label fw-semibold">Class</label>
                <select id="resource-class" class="form-select">
                    <option value="">All classes</option>
                    @foreach($classFilters as $classFilter)
                        <option value="{{ $classFilter }}">{{ $classFilter }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="resource-subject" class="form-label fw-semibold">Subject</label>
                <select id="resource-subject" class="form-select">
                    <option value="">All subjects</option>
                    @foreach($subjectFilters as $subjectFilter)
                        <option value="{{ $subjectFilter }}">{{ $subjectFilter }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
            <button id="resource-reset" type="button" class="btn btn-outline-secondary btn-sm">Reset Filters</button>
            <span id="resource-status" class="small text-muted" role="status" aria-live="polite"></span>
        </div>

        <div id="resource-results" class="row g-3 mt-2"></div>

        <div id="resource-empty" class="alert alert-light border mt-3 d-none" role="alert">
            No worksheets found for the selected filters. Try broader keywords or clear filters.
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
            <button id="resource-prev" type="button" class="btn btn-outline-primary btn-sm">Previous</button>
            <span id="resource-page" class="small text-muted"></span>
            <button id="resource-next" type="button" class="btn btn-outline-primary btn-sm">Next</button>
        </div>
    </section>

    <script>
        (() => {
            const initialPayload = @json($initialPayload);
            const searchUrl = @json(route('e-resources.search'));
            const downloadBase = @json(url('/e-resources/download'));

            const state = {
                q: initialPayload?.query?.q || '',
                class: initialPayload?.query?.class || '',
                subject: initialPayload?.query?.subject || '',
                page: initialPayload?.query?.page || 1,
                perPage: initialPayload?.query?.per_page || 12,
                loading: false,
            };

            const el = {
                search: document.getElementById('resource-search'),
                class: document.getElementById('resource-class'),
                subject: document.getElementById('resource-subject'),
                reset: document.getElementById('resource-reset'),
                status: document.getElementById('resource-status'),
                results: document.getElementById('resource-results'),
                empty: document.getElementById('resource-empty'),
                prev: document.getElementById('resource-prev'),
                next: document.getElementById('resource-next'),
                page: document.getElementById('resource-page'),
            };

            const escapeHtml = (value) => {
                const str = value == null ? '' : String(value);
                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            };

            const buildQuery = () => {
                const params = new URLSearchParams();
                if (state.q) params.set('q', state.q);
                if (state.class) params.set('class', state.class);
                if (state.subject) params.set('subject', state.subject);
                params.set('page', String(state.page));
                params.set('per_page', String(state.perPage));
                return params;
            };

            const resourceCard = (item) => {
                const title = escapeHtml(item.title || 'Untitled Resource');
                const classLabel = escapeHtml(item.class || 'General');
                const subjectLabel = escapeHtml(item.subject || 'General');
                const bundle = escapeHtml(item.source_bundle || '');
                const folder = escapeHtml(item.source_folder || '');
                const extension = escapeHtml((item.extension || '').toUpperCase());
                const size = escapeHtml(item.size_human || '');
                const downloadUrl = `${downloadBase}/${encodeURIComponent(item.id)}`;
                const printUrl = `${downloadUrl}?disposition=inline`;

                return `
                    <div class="col-md-6 col-xl-4">
                        <article class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <h3 class="h6 fw-bold mb-2">${title}</h3>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge text-bg-primary">${classLabel}</span>
                                    <span class="badge text-bg-secondary">${subjectLabel}</span>
                                </div>
                                <p class="small text-muted mb-2">${bundle}</p>
                                <p class="small text-muted mb-3">${folder}</p>
                                <div class="mt-auto d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <small class="text-muted">${extension} | ${size}</small>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-primary" href="${downloadUrl}">Download</a>
                                        <a class="btn btn-sm btn-outline-primary" href="${printUrl}" target="_blank" rel="noopener">Open / Print</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                `;
            };

            const render = (payload) => {
                const items = Array.isArray(payload?.items) ? payload.items : [];
                const pagination = payload?.pagination || {};
                const total = Number(pagination.total || 0);
                const currentPage = Number(pagination.current_page || 1);
                const lastPage = Number(pagination.last_page || 1);
                state.page = currentPage;

                if (items.length === 0) {
                    el.results.innerHTML = '';
                    el.empty.classList.remove('d-none');
                } else {
                    el.results.innerHTML = items.map(resourceCard).join('');
                    el.empty.classList.add('d-none');
                }

                el.status.textContent = `${total} result${total === 1 ? '' : 's'} found`;
                el.page.textContent = `Page ${currentPage} of ${lastPage}`;
                el.prev.disabled = !pagination.has_prev;
                el.next.disabled = !pagination.has_next;
            };

            const fetchResults = async () => {
                if (state.loading) {
                    return;
                }

                state.loading = true;
                el.status.textContent = 'Searching...';
                try {
                    const params = buildQuery();
                    const response = await fetch(`${searchUrl}?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        el.status.textContent = 'Unable to load results right now.';
                        return;
                    }

                    const payload = await response.json();
                    render(payload);
                } catch (error) {
                    el.status.textContent = 'Unable to load results right now.';
                } finally {
                    state.loading = false;
                }
            };

            let debounceTimer = null;
            const debounceFetch = () => {
                if (debounceTimer) {
                    clearTimeout(debounceTimer);
                }
                debounceTimer = setTimeout(() => {
                    fetchResults().catch(() => {
                        el.status.textContent = 'Unable to load results right now.';
                    });
                }, 250);
            };

            el.search.value = state.q;
            el.class.value = state.class;
            el.subject.value = state.subject;

            el.search.addEventListener('input', () => {
                state.q = el.search.value.trim();
                state.page = 1;
                debounceFetch();
            });

            el.class.addEventListener('change', () => {
                state.class = el.class.value;
                state.page = 1;
                fetchResults().catch(() => {
                    el.status.textContent = 'Unable to load results right now.';
                });
            });

            el.subject.addEventListener('change', () => {
                state.subject = el.subject.value;
                state.page = 1;
                fetchResults().catch(() => {
                    el.status.textContent = 'Unable to load results right now.';
                });
            });

            el.prev.addEventListener('click', () => {
                if (state.page > 1) {
                    state.page -= 1;
                    fetchResults().catch(() => {
                        el.status.textContent = 'Unable to load results right now.';
                    });
                }
            });

            el.next.addEventListener('click', () => {
                state.page += 1;
                fetchResults().catch(() => {
                    el.status.textContent = 'Unable to load results right now.';
                });
            });

            el.reset.addEventListener('click', () => {
                state.q = '';
                state.class = '';
                state.subject = '';
                state.page = 1;

                el.search.value = '';
                el.class.value = '';
                el.subject.value = '';

                fetchResults().catch(() => {
                    el.status.textContent = 'Unable to load results right now.';
                });
            });

            render(initialPayload);
        })();
    </script>
@endsection
