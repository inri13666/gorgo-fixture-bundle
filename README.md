# Gorgo Fixture Bundle

Aims to debug fixtures for tests and loads fixtures data to the dev/prod databases

```shell script
php bin/console gorgo:fixtures:load --dry-run --fixture="\Oro\Bundle\ProductBundle\Tests\Functional\DataFixtures\LoadProductUnits"

+-------------------------+---------------------------------------------+
| Reference               | Type                                        |
+-------------------------+---------------------------------------------+
| product_unit.milliliter | Oro\Bundle\ProductBundle\Entity\ProductUnit |
| product_unit.liter      | Oro\Bundle\ProductBundle\Entity\ProductUnit |
| product_unit.bottle     | Oro\Bundle\ProductBundle\Entity\ProductUnit |
| product_unit.box        | Oro\Bundle\ProductBundle\Entity\ProductUnit |
+-------------------------+---------------------------------------------+
```
