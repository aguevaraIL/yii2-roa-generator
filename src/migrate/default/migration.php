<?php

/**
 * @var yii\web\View $this
 * @var roaresearch\yii2\gii\migrate\Generator $generator
 */

echo "<?php\n";

if ($generator->migrationNamespace) {?>

namespace <?= $generator->migrationNamespace ?>;
<?php } ?>

/**
 * Migration to create table '{{%<?= $generator->tableName ?>}}'
 */
class <?= $className ?> extends <?= $generator->migrationClass ?> 
{
    /**
     * @inhertidoc
     */
    public function getTableName(): string
    {
        return '<?= $generator->tableName ?>';
    }

    /**
     * @inhertidoc
     */
    public function columns(): array
    {
        return [
<?php   foreach ($generator->generatedColumns() as $column) { ?>
            <?= $column ?>,
<?php   } ?>
        ];
    }
<?php if ([] !== $foreignKeys = $generator->generatedForeignKeys()) { ?>

    /**
     * @inhertidoc
     */
    public function foreignKeys(): array
    {
        return [
<?php   foreach ($foreignKeys as $foreignKey) { ?>
            <?= $foreignKey ?>,
<?php   } ?>
        ];
    }
<?php } if ([] !== $uniqueKeys = $generator->generatedUniqueKeys()) { ?>

    /**
     * @inhertidoc
     */
    public function uniqueKeys(): array
    {
        return [
<?php   foreach ($uniqueKeys as $uniqueKey) { ?>
            <?= $uniqueKey ?>,
<?php   } ?>
        ];
    }
<?php }

if ([] !== $compositeKeys = $generator->generatedCompositeKeys()) { ?>

    /**
     * @inhertidoc
     */
    public function compositePrimaryKeys(): array
    {
        return [
<?php   foreach ($compositeKeys as $column) { ?>
            <?= $column ?>,
<?php   } ?>
        ];
    }
<?php } ?>
}
