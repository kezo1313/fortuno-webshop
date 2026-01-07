import template from './sw-order-list-override.html.twig';

Shopware.Component.override('sw-order-list', {
    template,

    inject: ['fiscalizationApiService'],

    mixins: [
        Shopware.Mixin.getByName('notification')
    ],

    methods: {
        getOrderColumns() {
            const columns = this.$super('getOrderColumns');

            columns.push({
                property: 'customFields.fortuno_fiscal_zki',
                label: 'fortuno-fiscal.list.zki',
                allowResize: true,
                visible: true,
                align: 'left'
            });

            columns.push({
                property: 'customFields.fortuno_fiscal_jir',
                label: 'fortuno-fiscal.list.jir',
                allowResize: true,
                visible: true,
                align: 'left'
            });

            return columns;
        },

        onFiscalize(order) {
            if (!order || !order.id) return;

            this.createNotificationInfo({ message: 'Sending request...' });

            this.fiscalizationApiService.fiscalize(order.id)
                .then((response) => {
                    if (response.success) {
                        this.createNotificationSuccess({ message: 'Success!' });
                        this.getList(); // Ovo postoji samo u sw-order-list
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