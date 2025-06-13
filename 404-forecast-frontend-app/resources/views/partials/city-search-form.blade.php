<div id="autoCompleteCityForm">
    <form id="citySearchForm" action="{{ route('dashboard.city.charts') }}" method="GET">
        <label for="cityInput">City Name</label>
        <input
            type="text"
            id="cityInput"
            name="city"
            placeholder="Enter city..."
            autocomplete="off"
            aria-haspopup="listbox"
            aria-controls="citySuggestions"
        >
        <button type="submit">Search</button>
    </form>
    <div id="citySuggestions" class="suggestions-box" role="listbox"></div>
</div>


<style>
    #autoCompleteCityForm {
        position: relative;
        width: 300px;
        margin: 20px auto;
    }

    #cityInput {
        width: 100%;
        padding: 10px;
        box-sizing: border-box;
    }

    #citySuggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
    }

    .suggestion-item {
        padding: 10px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #f0f0f0;
    }
</style>
<script>
    const CITY_LOOKUP_URL = @json(route('dashboard.city.locations'));
</script>

