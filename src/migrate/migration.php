<?php


echo "<?php\n";

if ($generator->migrationNamespace) {?>

namespace <?= $generator->migrationNamespace ?>;

<?php

}

?>

class <?= $className ?> extends <?= $generator->migrationClass ?>
{
     public function getTableName(): string
     {
         return '<?= $generator->tableName ?>';
     }

    public function columns(): array
    {
        return [
    <?php foreach ($generator->generatedColumns() as $column) { ?>
            <?= $column ?>,
    <?php } ?>
        ];
    }
<?php if ([] !== $foreignKeys = $generator->generatedForeignKeys()) { ?>

    public function foreignKeys(): array
    {
        return [
    <?php foreach ($foreignKeys as $foreignKey) { ?>
            <?= $foreignKey ?>,
    <?php } ?>
        ];
    }
<?php } if ([] !== $uniqueKeys = $generator->generatedUniqueKeys()) { ?>

    public function uniqueKeys(): array
    {
        return [
    <?php foreach ($uniqueKeys as $uniqueKey) { ?>
             <?= $uniqueKey ?>,
    <?php } ?>
        ];
    }
<?php }

if ([] !== $uniqueKeys = $generator->generatedUniqueKeys()) { ?>

    public function uniqueKeys(): array
    {
        return [
    <?php foreach ($uniqueKeys as $uniqueKey) { ?>
            <?= $uniqueKey ?>,
    <?php } ?>
        ];
    }
<?php }

if ([] !== $compositeKeys = $generator->generateCompositeKeys()) { ?>

    public function compositePrimaryKeys(): array
    {
        return [
    <?php foreach ($compositeKeys as $column) { ?>
            <?= $column ?>,
    <?php } ?>
        ];
    }
<?php } ?>
}
