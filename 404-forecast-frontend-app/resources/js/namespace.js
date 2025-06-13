window.forecast = window.forecast || {};

forecast.namespace = function (ns) {
    let parts = ns.split('.');
    let parent = forecast;

    if (parts[0] === 'forecast') {
        parts = parts.slice(1);
    }

    for (let i = 0; i < parts.length; i++) {
        if (typeof parent[parts[i]] === 'undefined') {
            parent[parts[i]] = {};
        }
        parent = parent[parts[i]];
    }

    return parent;
};
