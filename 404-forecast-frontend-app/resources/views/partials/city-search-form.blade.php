<div id="autoCompleteCityForm">
    <form id="citySearchForm" action="{{ route('dashboard.cityCharts') }}" method="GET" role="search" autocomplete="off">
        <label for="cityInput" class="sr-only">City Name</label>
        <input
            type="text"
            id="cityInput"
            name="city"
            placeholder="Enter city..."
            autocomplete="off"
            aria-haspopup="listbox"
            aria-controls="citySuggestions"
            class="w-full border rounded px-3 py-2"
        >
        <button type="submit" class="btn btn-primary ml-2">Search</button>
    </form>
    <div id="citySuggestions" class="suggestions-box bg-white border mt-1 max-h-52 overflow-y-auto z-50" role="listbox"></div>
</div>

@push('head')
    <style>
        #autoCompleteCityForm { position: relative; max-width: 300px; margin: 20px auto; }
        #citySuggestions .suggestion-item { padding: 0.5rem; cursor: pointer; }
        #citySuggestions .suggestion-item:hover { background-color: #f0f0f0; }
    </style>
@endpush

@push('scripts')
    <script>
        const CITY_LOOKUP_URL = @json(route('dashboard.locations'));

        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('cityInput');
            const suggestionsBox = document.getElementById('citySuggestions');

            let debounce;
            input.addEventListener('input', () => {
                clearTimeout(debounce);
                debounce = setTimeout(async () => {
                    const query = input.value.trim();
                    if (!query) { suggestionsBox.innerHTML = ''; return; }
                    const res = await fetch(`${CITY_LOOKUP_URL}?city=${encodeURIComponent(query)}`);
                    const data = await res.json();
                    suggestionsBox.innerHTML = data.data.map(item =>
                        `<div class="suggestion-item" data-lat="${item.lat}" data-lon="${item.lon}">${item.name}, ${item.country}</div>`
                    ).join('');
                }, 300);
            });

            suggestionsBox.addEventListener('click', e => {
                if (e.target.classList.contains('suggestion-item')) {
                    input.value = e.target.textContent;
                    // Optionally store lat/lon for form submission
                    input.dataset.lat = e.target.dataset.lat;
                    input.dataset.lon = e.target.dataset.lon;
                    suggestionsBox.innerHTML = '';
                }
            });
        });
    </script>
@endpush
