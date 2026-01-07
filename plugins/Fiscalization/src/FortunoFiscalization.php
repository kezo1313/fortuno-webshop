<?php declare(strict_types=1);

namespace Fortuno\Fiscalization;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Core\Framework\Context;

class FortunoFiscalization extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->createCustomFields($installContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $this->createCustomFields($activateContext->getContext());
    }

    private function createCustomFields(Context $context): void
    {
        $repo = $this->container->get('custom_field_set.repository');

        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->addFilter(new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter('name', 'fortuno_fiscalization'));

        if ($repo->searchIds($criteria, $context)->getTotal() > 0) {
            return;
        }

        $repo->create([
            [
                'name' => 'fortuno_fiscalization',
                'config' => [
                    'label' => [
                        'en-GB' => 'Fiscalization Data',
                        'hr-HR' => 'Podaci fiskalizacije'
                    ]
                ],
                'relations' => [
                    ['entityName' => 'order']
                ],
                'customFields' => [
                    [
                        'name' => 'fortuno_fiscal_jir',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => ['en-GB' => 'JIR', 'hr-HR' => 'JIR'],
                            'componentName' => 'sw-field', // I dalje koristimo sw-field za config, admin rendering to rješava
                            'customFieldType' => 'text'
                        ]
                    ],
                    [
                        'name' => 'fortuno_fiscal_zki',
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => ['en-GB' => 'ZKI', 'hr-HR' => 'ZKI'],
                            'componentName' => 'sw-field',
                            'customFieldType' => 'text'
                        ]
                    ],
                    [
                        'name' => 'fortuno_fiscal_date',
                        'type' => CustomFieldTypes::DATETIME,
                        'config' => [
                            'label' => ['en-GB' => 'Fiscalization Date', 'hr-HR' => 'Datum fiskalizacije'],
                            'componentName' => 'sw-field',
                            'customFieldType' => 'date'
                        ]
                    ]
                ]
            ]
        ], $context);
    }
}