import '../namespace';
import Chart from 'chart.js/auto';

forecast.namespace('forecast.controllers');

forecast.controllers.weather = (function () {
    class WeatherCharts {
        customBottomLabelsPlugin = {
            id: 'customBottomLabels',
            afterDraw(chart, args, options) {
                const {ctx, chartArea: {top, bottom, left, width}} = chart;
                ctx.save();
                ctx.font = '14px sans-serif';
                ctx.fillStyle = 'black';
                ctx.textAlign = 'center';

                const tempMin = options.tempMin ?? 'N/A';
                const tempMax = options.tempMax ?? 'N/A';
                const weatherDate = options.weatherDate ?? 'N/A';

                const xCenter = left + width / 2;
                const yPosition = bottom + 30;
                const yPositionTop = top + 60;

                ctx.fillText(`Min Temp: ${tempMin}°C`, xCenter - 80, yPosition);
                ctx.fillText(`Max Temp: ${tempMax}°C`, xCenter + 80, yPosition);
                ctx.fillText(`Update: ${weatherDate}`, xCenter, yPositionTop);

                ctx.restore();
            }
        };

        constructor(currentCtx, forecastCtx) {
            this.currentCtx = currentCtx;
            this.forecastCtx = forecastCtx;
            this.currentChart = null;
            this.forecastChart = null;
        }

        init() {
            if (this.currentCtx && window.weatherResponse) {
                this.initCurrentChart(window.weatherResponse);
            }
            if (this.forecastCtx && window.forecastResponse) {
                this.initForecastChart(window.forecastResponse);
            }
        }

        initCurrentChart(data) {
            const tempMin = this.temperatureToCelsius(data.main.temp_min);
            const tempMax = this.temperatureToCelsius(data.main.temp_max);
            const weatherDate = data.dt;

            this.currentChart = new Chart(this.currentCtx, {
                type: "radar",
                data: {
                    labels: ["Temperature", "Feels Like", "Humidity", "Wind Speed"],
                    datasets: [{
                        label: "Current Weather: " + data.name,
                        data: [
                            this.temperatureToCelsius(data.main.temp),
                            this.temperatureToCelsius(data.main.feels_like),
                            data.main.humidity,
                            data.wind.speed
                        ],
                        fill: true,
                        backgroundColor: "#ff6361",
                        borderColor: "#003f5c",
                        pointBackgroundColor: "#003f5c",
                    }]
                },
                options: {
                    layout: {
                        padding: {
                            bottom: 50
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Weather Data'
                        },
                        customBottomLabels: {
                            tempMin,
                            tempMax,
                            weatherDate,
                        }
                    }
                },
                plugins: [this.customBottomLabelsPlugin]
            });
        }

        initForecastChart(data) {
            this.forecastChart = new Chart(this.forecastCtx, {
                type: "line",
                data: {
                    labels: data.list.map(item => item.dt_txt),
                    datasets: [{
                        label: 'Temperature (°C)',
                        data: data.list.map(item => this.temperatureToCelsius(item.main.temp)),
                        borderColor: "#003f5c",
                        backgroundColor: "rgba(0, 63, 92, 0.2)",
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: '5-Day Temperature Forecast'
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxTicksLimit: 10,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        }

        updateCurrentChart(data) {
            if (!this.currentChart) return;
            this.currentChart.data.datasets[0].label = data.name;
            this.currentChart.data.datasets[0].data = [
                this.temperatureToCelsius(data.main.temp),
                this.temperatureToCelsius(data.main.feels_like),
                data.main.humidity,
                data.wind.speed
            ];
            this.currentChart.options.plugins.customBottomLabels.tempMin = this.temperatureToCelsius(data.main.temp_min);
            this.currentChart.options.plugins.customBottomLabels.tempMax = this.temperatureToCelsius(data.main.temp_max);
            this.currentChart.options.plugins.customBottomLabels.weatherDate = data.dt;
            this.currentChart.update();
        }

        updateForecastChart(data) {
            if (!this.forecastChart) return;
            this.forecastChart.data.labels = data.list.map(item => item.dt_txt);
            this.forecastChart.data.datasets[0].data = data.list.map(item => this.temperatureToCelsius(item.main.temp));
            this.forecastChart.update();
        }

        temperatureToCelsius(temp) {
            return Math.round(temp - 273.15);
        }

        destroy() {
            if (this.currentChart) {
                this.currentChart.destroy();
                this.currentChart = null;
            }
            if (this.forecastChart) {
                this.forecastChart.destroy();
                this.forecastChart = null;
            }
        }
    }

    function debounce(fn, delay = 300) {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                fn.apply(null, args);
            }, delay);
        };
    }

    function setupCityAutocomplete(inputId = 'cityInput', suggestionBoxId = 'citySuggestions', formId = 'citySearchForm') {
        const input = document.getElementById(inputId);
        const suggestionBox = document.getElementById(suggestionBoxId);
        const form = document.getElementById(formId);

        if (!input || !suggestionBox || !form) return;

        input.addEventListener('keyup', debounce(function () {
            const query = input.value;

            if (query.length < 3) {
                suggestionBox.innerHTML = '';
                return;
            }

            let hiddenLat = document.getElementById('hidden-lat');
            let hiddenLon = document.getElementById('hidden-lon');

            if (!hiddenLat) {
                hiddenLat = document.createElement('input');
                hiddenLat.type = 'hidden';
                hiddenLat.id = 'hidden-lat';
                hiddenLat.name = 'latitude';
                form.appendChild(hiddenLat); // You can append to a form instead
            }

            if (!hiddenLon) {
                hiddenLon = document.createElement('input');
                hiddenLon.type = 'hidden';
                hiddenLon.id = 'hidden-lon';
                hiddenLon.name = 'longitude';
                form.appendChild(hiddenLon); // You can append to a form instead
            }

            fetch(`${CITY_LOOKUP_URL}?city=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionBox.innerHTML = ''; // clear previous suggestions

                    // Create UL and set ARIA role
                    const ul = document.createElement('ul');
                    ul.setAttribute('role', 'listbox');
                    ul.classList.add('suggestions-list');

                    data.flat().forEach(city => {
                        const locationParts = [city.name];
                        if (city.state) locationParts.push(city.state);
                        locationParts.push(city.country);

                        const displayText = locationParts.join(', ');

                        const li = document.createElement('li');
                        li.textContent = displayText;
                        li.setAttribute('role', 'option');
                        li.setAttribute('tabindex', '0'); // Allow keyboard focus
                        li.classList.add('suggestion');

                        li.addEventListener('click', () => {
                            input.value = displayText;
                            suggestionBox.innerHTML = '';
                            hiddenLat.value = Number(city.lat).toFixed(3);
                            hiddenLon.value = Number(city.lon).toFixed(3);
                        });

                        li.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                li.click();
                            }
                        });

                        ul.appendChild(li);
                    });

                    suggestionBox.appendChild(ul);
                })
                .catch(err => {
                    console.error('City suggestion error:', err);
                });
        }, 300));

        // Form Submission (AJAX)
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            let hiddenLat = document.getElementById('hidden-lat');
            let hiddenLon = document.getElementById('hidden-lon');

            if (!hiddenLat || !hiddenLon) return;

            fetch(form.action + '?lat=' + encodeURIComponent(hiddenLat.value) + '&lon=' + encodeURIComponent(hiddenLon.value), {

                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(async res => {
                    if (!res.ok) {
                        const text = await res.text();
                        throw new Error(text);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.current && data.forecast) {
                        window.weatherResponse = data.current;
                        window.forecastResponse = data.forecast;

                        forecast.controllers.weather.updateCurrentChart(data.current);
                        forecast.controllers.weather.updateForecastChart(data.forecast);
                    } else {
                        console.error('Unexpected API format', data);
                    }
                })
                .catch(err => {
                    console.error('City search error:', err);
                    alert('Could not load weather data for that city.' + err.message);
                });
        });
    }

    let weatherChartsInstance = null;

    function init() {
        const currentCtx = document.getElementById('currentChart');
        const forecastCtx = document.getElementById('forecastChart');

        if (weatherChartsInstance) {
            weatherChartsInstance.destroy();
        }

        weatherChartsInstance = new WeatherCharts(currentCtx, forecastCtx);
        weatherChartsInstance.init();

        // Initialize city autocomplete
        setupCityAutocomplete();
    }

    return {
        init,
        updateCurrentChart: (data) => weatherChartsInstance?.updateCurrentChart(data),
        updateForecastChart: (data) => weatherChartsInstance?.updateForecastChart(data),
        destroy: () => weatherChartsInstance?.destroy()
    };
})();
