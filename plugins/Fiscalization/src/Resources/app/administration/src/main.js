import './module/sw-order';
import FiscalizationApiService from './service/fiscalization-api.service';

// Registracija servisa
Shopware.Application.addServiceProvider('fiscalizationApiService', (container) => {
    const initContainer = Shopware.Application.getContainer('init');

    return new FiscalizationApiService(initContainer.httpClient, container.loginService);
});
// Import snippeta
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

// Registracija (Ovisno o strukturi, ovo se može dodati i unutar module/sw-order/index.js, ali ovdje je globalno)
Shopware.Locale.extend('de-DE', deDE);
Shopware.Locale.extend('en-GB', enGB);