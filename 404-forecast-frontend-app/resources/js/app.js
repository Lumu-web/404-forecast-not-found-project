import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';
import './namespace';
import './controllers/weather';
import axios from 'axios';
import './index.js';
import '../css/app.css';
import '../css/index.css';
import initOverlayMap from "./modules/overlayMap.js";
import RegisterForm from "./modules/register.js";
import LoginForm from "./modules/login.js";

window.bootstrap = bootstrap;

try {
    const userData = localStorage.getItem('user');
    if (userData) {
        window.AuthUser = JSON.parse(userData);
        window.isAuthenticated = true;
    } else {
        window.AuthUser = null;
        window.isAuthenticated = false;
    }
} catch (e) {
    console.error('Error parsing user data from localStorage', e);
    window.AuthUser = null;
    window.isAuthenticated = false;
}

axios.defaults.baseURL = import.meta.env.API_URL;
axios.defaults.withCredentials = true;
window.axios = axios;

document.addEventListener('DOMContentLoaded', function () {
    if (forecast.controllers.weather) {
        forecast.controllers.weather.init();
    }

    // Forms js
    try {
        new RegisterForm();
        new LoginForm()
    } catch (error) {
        console.error(error);
    }

    initOverlayMap('map', 'latitude', 'longitude');
});
