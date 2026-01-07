const ApiService = Shopware.Classes.ApiService;

/**
 * Gateway for the Fortuno Fiscalization API
 */
class FiscalizationApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = '_action/fortuno') {
        // Postavljamo content-type
        super(httpClient, loginService, apiEndpoint, 'application/json');
        this.name = 'fiscalizationApiService';
    }

    /**
     * Šalje zahtjev za fiskalizaciju narudžbe
     * @param {String} orderId
     * @returns {Promise}
     */
    fiscalize(orderId) {
        const headers = this.getBasicHeaders(); // Ovo automatski dodaje Auth token

        // Ruta definirana u PHP-u: /api/_action/fortuno/fiscalize/{orderId}
        // ApiService automatski dodaje /api/ prefix ako je potrebno
        return this.httpClient.post(
            `/_action/fortuno/fiscalize/${orderId}`,
            {},
            { headers }
        ).then((response) => {
            return ApiService.handleResponse(response);
        });
    }
}

export default FiscalizationApiService;