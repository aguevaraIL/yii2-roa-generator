<?php

namespace roaresearch\yii2\gii\migrate;

use roaresearch\yii2\migrate\CreateTableMigration as CreateTable;
use roaresearch\yii2\rmdb\migrations\{
    CreateEntity,
    CreatePersistentEntity,
    CreatePivot
};
use Yii;
use yii\{gii\CodeFile, helpers\Inflector};

/**
 * Generates migrations based on the library roaresearch/yii2-migrate
 *
 * @author Angel (Faryshta) Guevara <aguevara@invernaderolabs.com>
 */
class Generator extends \yii\gii\Generator
{
    /**
     * List of classes which will be presented on the form by default
     */
    public $defaultClasses = [
        CreatePivot::class,
        CreateEntity::class,
        CreatePersistentEntity::class,
        CreateTable::class,
    ];

    /**
     * @var string namespace where the migration will be generated.
     */
    public $migrationNamespace = '';

    /**
     * @var string filepath where the migration will be generated. Ignored if
     * `$migrationNamespace` is provided
     */
    public $migrationPath = '';

    /**
     * @var string class which the generated migration will extend
     */
    public $migrationClass = CreateEntity::class;

    /**
     * @var string table which the generated migration will create
     */
    public $tableName = '';

    /**
     * @var string[] columns defined by the generated migration
     * @see TODO LINK
     */
    public $columns = [];

    /**
     * @var string[] foreign keys defined by the generated migration
     * @see TODO LINK
     */
     public $foreignKeys = [];
 
    /**
     * @var string[] unique keys defined by the generated migration
     * @see TODO LINK
     */
    public $uniqueKeys = [];
 
    /**
     * @var string[] composite primary keys defined by the generated migration
     * @see TODO LINK
     */
    public $compositePrimaryKeys = [];

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'RMDB Migrate Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'Generates an RMDB migration to create database tables.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['migrationClass', 'tableName', 'columns'], 'required'],
            [
                ['migrationPath'],
                'required',
                'message' => 'Either migrationPath or migrationNamespace ' .
                    'must be defined',
                'when' => function () {
                    return empty($this->migrationNamespace);
                },
            ],
            [
                [
                    'migrationNamespace',
                    'migrationPath',
                    'migrationClass',
                    'tableName',
                ],
                'string',
            ],
            [
                [
                    'columns',
                    'foreignKeys',
                    'uniqueKeys',
                    'compositePrimaryKeys',
                ],
                function ($attribute) {
                    if (is_string($value = $this->$attribute)) {
                        $this->$attribute = preg_split(
                            '/\s*,\s*(?![^()]*\))/',
                            $value
                        );
                    }
                },
            ],
            [
                [
                    'columns',
                    'foreignKeys',
                    'uniqueKeys',
                    'compositePrimaryKeys',
                ],
                'each',
                'rule' => ['string'],
            ],
            [
                ['columns'],
                'each',
                'rule' => [
                    'match',
                    // start with letter and then contains letters, numbers or
                    // any of the signs ( ) :
                    'pattern' => '/^[a-z][\w\(\)\:]*$/i',
                ],
            ],
            [
                ['foreignKeys'],
                'each',
                'rule' => [
                    'match',
                    // start with letter and then contains letters, numbers 
                    // whitespace or any of the signs ( ) :
                    'pattern' => '/^[a-z][\s\w\(\)\:]*$/i',
                ],
            ],
            [
                ['uniqueKeys'],
                'each',
                'rule' => [
                    'match',
                    // start with letter and then contains letters, numbers or
                    // whitespace
                    'pattern' => '/^[a-z][\:\w]*$/i',
                ],
            ],
            [
                ['compositePrimaryKeys'],
                'each',
                'rule' => [
                    'match',
                    // start with letter and then contains letters or numbers
                    'pattern' => '/^[a-z][\w]*$/i',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'migrationNamespace' => 'Migration Namespace',
            'migrationPath' => 'Migration Path',
            'migrationClass' => 'Migration Class',
            'tableName' => 'Table Name',
            'columns' => 'Columns',
            'foreignKeys' => 'Foreign Keys',
            'uniqueKeys' => 'Unique Keys',
            'compositePrimaryKeys' => 'Composite Primary Keys',
        ];
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return [
            'migrationNamespace' => '(Optional) The migration belong in a PHP ' .
                'namespace.',
            'migrationPath' => '(Optional) The migration has no namespace and ' .
                'belong in an aliased path. Ignored if [Migration Namespac] ' .
                'is provided.',
            'migrationClass' => 'The class which the generated migration will ' .
                'extend',
            'tableName' => 'Table name the migration will create.',
            'columns' => 'Each field becomes a column in the table, syntax ' .
                'can be found !TODO LINK',
            'foreignKeys' => 'Each field becomes a foreign key in the table, ' .
                'syntax can be found !TODO LINK',
            'uniqueKeys' => 'Each field becomes a unique key, syntax can be ' .
                ' found !TODO LINK',
            'compositePrimaryKeys' => 'Used when the table primary key ' .
                ' consist on multiple columsn. Each field is a column name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['migration.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        [$className, $classPath] = $this->classPath();

        return [
            new CodeFile(
                $classPath,
                $this->render('migration.php', ['className' => $className])
            )
        ];
    }

    /**
     * Parses the `$columns` input into the migration format
     * @return string[]
     */
    public function generatedColumns(): array
    {
        return array_map(
            function ($column) {
                $parts = preg_split('/\s*:\s*/', $column);
                $columnName = array_shift($parts);

                foreach ($parts as &$part) {
                    if (!preg_match('/^(.+?)\(([^(]+)\)$/', $part)) {
                        $part .= '()';
                    }
                }

                return "'$columnName' => \$this->" . implode('->', $parts);
            },
            $this->columns
        );
    }

    /**
     * Parses the `$foreignKeys` input into the migration format
     * @return string[]
     */
    public function generatedForeignKeys(): array
    {
        return array_map(
            function ($key) {
                $parts = preg_split('/\s*:\s*/', $key);
                $keyName = array_shift($parts);

                if (sizeof($parts) === 1 && false === strpos($parts[0], '(')) {
                    return "'$keyName' => '{$parts[0]}'";
                }

                $result = "'$keyName' => [";
                foreach ($parts as $part) {
                    preg_match('/(?P<I>\w*)\((?P<K>\w*)\)/', $part, $matches);

                    $result .= "'{$matches['I']}' => '{$matches['K']}', ";
                }

                return $result . "]";
            },
            $this->foreignKeys
        );
    }

    /**
     * Parses the `$uniqueKeys` input into the migration format
     * @return string[]
     */
    public function generatedUniqueKeys(): array
    {
        return array_map(
            function ($key) {
                $parts = preg_split('/\s*:\s*/', $key);
                $keyName = array_shift($parts);

                return "'$keyName' => ['" . implode("', '", $parts) . "']";
            },
            $this->uniqueKeys
        );
    }

    /**
     * Parses the `$compositePrimaryKeys` input into the migration format
     * @return string[]
     */
    public function generatedCompositeKeys(): array
    {
        return $this->compositePrimaryKeys;
    }

    /**
     * @return string[] the first index is the name of the class to be generated
     * and the second index is the file path.
     */
    private function classPath()
    {
        if ($this->migrationNamespace) {
            $className = 'M' . gmdate('ymdHis') .
                Inflector::camelize($this->tableName);
            $directory = Yii::getAlias(
                '@' . str_replace('\\', '/', $this->migrationNamespace) 
            );
        } else {
            $className = 'm' . gmdate('ymd_His') . '_' . $this->tableName;

            $directory = Yii::getAlias('@' . $this->migrationPath);
        }

        return [$className, $directory . '/' . $className . '.php'];
    }
}
