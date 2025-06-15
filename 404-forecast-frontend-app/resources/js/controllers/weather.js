import '../namespace';

import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(ChartDataLabels);

forecast.namespace('forecast.controllers');

forecast.controllers.weather = (function () {
    class WeatherCharts {
        customBottomLabelsPlugin = {
            id: 'customBottomLabels',
            afterDraw(chart, args, options) {
                const {
                    ctx,
                    chartArea: { top, bottom, left, width }
                } = chart;
                ctx.save();
                ctx.font = '14px sans-serif';
                ctx.fillStyle = 'black';
                ctx.textAlign = 'center';

                const tempMin     = options.tempMin     ?? 'N/A';
                const tempMax     = options.tempMax     ?? 'N/A';
                const weatherDate = options.weatherDate ?? 'N/A';

                const xCenter       = left + width / 2;
                const yPositionBot  = bottom + 30;
                const yPositionTop  = top + 60;

                ctx.fillText(`Min Temp: ${tempMin}°C`, xCenter - 80, yPositionBot);
                ctx.fillText(`Max Temp: ${tempMax}°C`, xCenter + 80, yPositionBot);
                ctx.fillText(`Update: ${weatherDate}`, xCenter, yPositionTop);

                ctx.restore();
            }
        };

        constructor(currentMoodBarChartCtx, forecastCtx) {
            this.currentMoodBarChartCtx  = currentMoodBarChartCtx;
            this.forecastCtx = forecastCtx;
            this.currentMoodBarChart  = null;
            this.forecastChart = null;
        }

        init() {
            if (this.currentMoodBarChartCtx && window.currentMoodBarResponse) {
                this.initCurrentMoodBarChart(window.currentMoodBarResponse);
            }
            if (this.forecastCtx && window.forecastResponse) {
                // this.initForecastChart(window.forecastResponse);
            }
        }

        initCurrentMoodBarChart(snap) {

            const { name, label, temp, feels, humid, pres } = snap;

            const cityName         = [name];
            const labels     = [label];
            const tempsC     = [temp];
            const feelsC     = [feels];
            const humidities = [humid];
            const pressures  = [pres];

            // destroy old chart
            if (this.currentMoodBarChart) {
                this.currentMoodBarChart.destroy();
                this.currentMoodBarChart = null;
            }

            const ctx = this.currentMoodBarChartCtx.getContext('2d');
            this.currentMoodBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: '🌡 Temp (°C)',
                            data: tempsC,
                            backgroundColor: '#4e79a7',
                        },
                        {
                            label: '🤗 Feels Like (°C)',
                            data: feelsC,
                            backgroundColor: '#f28e2b',
                        },
                        {
                            label: '💧 Humidity (%)',
                            data: humidities,
                            backgroundColor: '#e15759',
                        },
                        {
                            label: '🔴 Pressure (hPa)',
                            data: pressures,
                            backgroundColor: '#76b7b2',
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: `Current Weather: ${cityName}`,
                            font: { size: 18 }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'right',
                            color: '#000',
                            formatter: v => v,
                            font: { weight: 'bold' }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: { display: true, text: 'Value' }
                        },
                        y: {
                            title: { display: true, text: 'Metric' }
                        }
                    }
                },
                // include custom bottom labels plugin for this chart
                plugins: [ this.customBottomLabelsPlugin ]
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
                        title: { display: true, text: '5-Day Temperature Forecast' }
                    },
                    scales: {
                        x: { ticks: { maxTicksLimit: 10, autoSkip: true } },
                        y: { beginAtZero: false }
                    }
                }
            });
        }

        updateCurrentMoodBarChart(snapshots) {
            if (!this.currentMoodBarChart) return;
            const labels     = snapshots.map(s => s.captured_at);
            const tempsC     = snapshots.map(s => Math.round(s.temperature - 273.15));
            const feelsC     = snapshots.map(s => Math.round(s.feels_like - 273.15));
            const humidities = snapshots.map(s => s.humidity);
            const pressures  = snapshots.map(s => s.pressure);

            this.currentMoodBarChart.data.labels               = labels;
            this.currentMoodBarChart.data.datasets[0].data     = tempsC;
            this.currentMoodBarChart.data.datasets[1].data     = feelsC;
            this.currentMoodBarChart.data.datasets[2].data     = humidities;
            this.currentMoodBarChart.data.datasets[3].data     = pressures;
            this.currentMoodBarChart.update();
        }

        updateForecastChart(data) {
            if (!this.forecastChart) return;
            this.forecastChart.data.labels            = data.list.map(item => item.dt_txt);
            this.forecastChart.data.datasets[0].data  = data.list.map(item => this.temperatureToCelsius(item.main.temp));
            this.forecastChart.update();
        }

        temperatureToCelsius(temp) {
            return Math.round(temp - 273.15);
        }

        destroy() {
            if (this.currentMoodBarChart) {
                this.currentMoodBarChart.destroy();
                this.currentMoodBarChart = null;
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
            timeoutId = setTimeout(() => { fn.apply(null, args); }, delay);
        };
    }

    function setupCityAutocomplete(inputId = 'cityInput', suggestionBoxId = 'citySuggestions', formId = 'citySearchForm') {
        const input         = document.getElementById(inputId);
        const suggestionBox = document.getElementById(suggestionBoxId);
        const form          = document.getElementById(formId);
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
                hiddenLat.id   = 'hidden-lat';
                hiddenLat.name = 'latitude';
                form.appendChild(hiddenLat);
            }
            if (!hiddenLon) {
                hiddenLon = document.createElement('input');
                hiddenLon.type = 'hidden';
                hiddenLon.id   = 'hidden-lon';
                hiddenLon.name = 'longitude';
                form.appendChild(hiddenLon);
            }

            fetch(`${CITY_LOOKUP_URL}?city=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionBox.innerHTML = '';
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
                        li.setAttribute('tabindex', '0');
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

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const hiddenLat = document.getElementById('hidden-lat');
            const hiddenLon = document.getElementById('hidden-lon');
            if (!hiddenLat || !hiddenLon) return;

            fetch(`${form.action}?lat=${encodeURIComponent(hiddenLat.value)}&lon=${encodeURIComponent(hiddenLon.value)}`, {
                headers: { 'Accept': 'application/json' }
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
                        window.weatherSnapshots    = data.currentSnapshots;
                        window.forecastResponse    = data.forecast;
                        forecast.controllers.weather.updateCurrentMoodBarChart(window.weatherSnapshots);
                        forecast.controllers.weather.updateForecastChart(data.forecast);
                    } else {
                        console.error('Unexpected API format', data);
                    }
                })
                .catch(err => {
                    console.error('City search error:', err);
                    alert('Could not load weather data for that city: ' + err.message);
                });
        });
    }

    let weatherChartsInstance = null;

    function init() {
        const currentMoodBarChartCtx  = document.getElementById('current-mood-chart');
        const forecastCtx = document.getElementById('forecastChart');

        if (weatherChartsInstance) weatherChartsInstance.destroy();

        weatherChartsInstance = new WeatherCharts(currentMoodBarChartCtx, forecastCtx);
        weatherChartsInstance.init();

        setupCityAutocomplete();
    }

    return {
        init,
        updateCurrentMoodBarChart:  (data) => weatherChartsInstance?.updateCurrentMoodBarChart(data),
        updateForecastChart: (data) => weatherChartsInstance?.updateForecastChart(data),
        destroy: () => weatherChartsInstance?.destroy()
    };
})();
