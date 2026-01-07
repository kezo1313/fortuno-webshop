// PAZI OVDJE: importamo DETAIL template, ne list
import template from './sw-order-detail-override.html.twig';

Shopware.Component.override('sw-order-detail', {
    template,

    inject: ['fiscalizationApiService'],

    mixins: [
        Shopware.Mixin.getByName('notification')
    ],

    methods: {
        onFiscalizeCurrent() {
            this.createNotificationInfo({ message: 'Sending request...' });

            // Koristimo this.orderId koji je dostupan u detaljima
            this.fiscalizationApiService.fiscalize(this.orderId)
                .then((response) => {
                    if (response.success) {
                        this.createNotificationSuccess({ message: 'Success!' });
                        this.reloadEntityData(); // Ovo postoji samo u sw-order-detail
                    } else {
                        this.createNotificationError({ message: 'Error: ' + (response.message || 'Unknown') });
                    }
                })
                .catch((exception) => {
                    const errorMsg = exception.response?.data?.message || exception.message;
                    this.createNotificationError({ message: 'Service Error: ' + errorMsg });
                });
        }
    }
});