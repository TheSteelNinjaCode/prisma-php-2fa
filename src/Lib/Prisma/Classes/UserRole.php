<?php

namespace Lib\Prisma\Classes;

use Lib\Prisma\Model\IModel;
use Lib\Validator;
use Brick\Math\BigInteger;
use Brick\Math\BigDecimal;
use ReflectionClass;
use PDO;
use InvalidArgumentException;
use LogicException;
use Exception;

class UserRole implements IModel
{
    public ?int $id;
    public string $name;
    /** @var User[] */
    public ?array $user;

    // Additional properties
    public string $_tableName;
    public string $_primaryKey;
    public array $_compositeKeys;
    public array $_fieldsOnly;
    public array $_fields;
    public array $_fieldByRelationName;
    public array $_fieldsRelatedWithKeys;

    protected array $_fieldsRelated;
    protected array $_tableFieldsOnly;
    protected array $_fieldsCombined;

    private array $_primaryKeyFields;
    private array $_uniqueFields;
    private array $_primaryKeyAndUniqueFields;
    private PDO $_pdo;
    private string $_dbType;
    private string $_modelName;
    private array $_model;

    public function __construct(PDO $pdo)
    {
        $this->_model = [
            'name' => 'UserRole',
            'dbName' => NULL,
            'schema' => NULL,
            'fields' => [
                [
                    'name' => 'id',
                    'kind' => 'scalar',
                    'isList' => false,
                    'isRequired' => true,
                    'isUnique' => false,
                    'isId' => true,
                    'isReadOnly' => false,
                    'hasDefaultValue' => true,
                    'type' => 'Int',
                    'nativeType' => NULL,
                    'default' => [
                        'name' => 'autoincrement',
                        'args' => [],
                    ],
                    'isGenerated' => false,
                    'isUpdatedAt' => false,
                ],
                [
                    'name' => 'name',
                    'kind' => 'scalar',
                    'isList' => false,
                    'isRequired' => true,
                    'isUnique' => true,
                    'isId' => false,
                    'isReadOnly' => false,
                    'hasDefaultValue' => false,
                    'type' => 'String',
                    'nativeType' => NULL,
                    'isGenerated' => false,
                    'isUpdatedAt' => false,
                ],
                [
                    'name' => 'user',
                    'kind' => 'object',
                    'isList' => true,
                    'isRequired' => true,
                    'isUnique' => false,
                    'isId' => false,
                    'isReadOnly' => false,
                    'hasDefaultValue' => false,
                    'type' => 'User',
                    'nativeType' => NULL,
                    'relationName' => 'UserToUserRole',
                    'relationFromFields' => [],
                    'relationToFields' => [],
                    'isGenerated' => false,
                    'isUpdatedAt' => false,
                ],
            ],
            'primaryKey' => NULL,
            'uniqueFields' => [],
            'uniqueIndexes' => [],
            'isGenerated' => false,
        ];

        $this->_fields = array_column($this->_model['fields'], null, 'name');
        $this->_fieldByRelationName = array_column($this->_model['fields'], null, 'relationName');
        $this->_fieldsOnly = ['id', 'name'];
        $this->_tableFieldsOnly = ['id', 'name'];
        $this->_fieldsRelated = ['user'];
        $this->_fieldsRelatedWithKeys = [
            'user' => [
                "relationFromFields" => ['roleId'],
                "relationToFields" => ['id']
            ]
        ];
        $this->_primaryKey = 'id';
        $this->_compositeKeys = [''];
        $this->_primaryKeyFields = ['id'];
        $this->_uniqueFields = ['name'];
        $this->_primaryKeyAndUniqueFields = ['id', 'name'];
        $this->_fieldsCombined = ['id', 'name', 'user'];

        $this->_pdo = $pdo;
        $this->_dbType = $this->_pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $this->_modelName = 'UserRole';
        $this->_tableName = 'UserRole';

    }

    private function handleRelatedField(
        string $fieldName,
        array &$dataToCreate,
        array &$bindings,
        array &$insertFields,
        array &$placeholders
    ): void {
        foreach ($this->_fieldsRelatedWithKeys as $relationName => $relationFields) {
            if (in_array($fieldName, $relationFields['relationFromFields'])) {
                if (!array_key_exists($relationName, $dataToCreate)) {
                    throw new InvalidArgumentException("The required related field '$relationName' for '$fieldName' is missing in the provided data.");
                }

                $relatedClassName = "Lib\\Prisma\\Classes\\" . $relationName;
                $relationBindings = PPHPUtility::processRelation(
                    $relationName,
                    $dataToCreate[$relationName],
                    $relationFields['relationFromFields'],
                    $relationFields['relationToFields'],
                    $relatedClassName,
                    $this->_pdo
                );

                foreach ($relationBindings as $fromField => $value) {
                    $bindings[$fromField] = $value;
                    $insertFields[] = $fromField;
                    $placeholders[] = ":$fromField";
                }

                unset($dataToCreate[$relationName]);
            }
        }
    }
    
    /**
     * Creates a new UserRole in the database.
     *
     * This method is designed to insert a new UserRole record into the database using provided data.
     * It is capable of handling related records through the dynamically defined relations in the UserRole model.
     * The method allows for selective field return and including related models in the response, enhancing flexibility and control
     * over the output.
     *
     * @param:
     * - `array $data`: An associative array that contains the data for the new User record.
     *   The array may also include 'select' and 'include' keys for selective field retrieval
     *   and including related models in the result, respectively. The 'data' key within this array
     *   is required and contains the actual data for the User record.
     *
     * @return UserRoleData The newly created UserRoleData record.
     *
     * @throws:
     * - `Exception` if the 'data' key is not provided or is not an associative array.
     * - `Exception` if both 'include' and 'select' keys are used simultaneously.
     * - `Exception` for any error encountered during the creation process.
     *
     * Example:
     * ```
     * Example of creating a new UserRole with related profile and roles
     * $newUserRole = $prisma->UserRole->create([
     *   'data' => [
     *     'property' => 'value',
     * ]);
     * ```
     *
     * Notes:
     * - The method checks for required fields in the 'data' array and validates their types,
     *   ensuring data integrity before attempting to create the record.
     * - It supports complex operations such as connecting or creating related records based on
     *   predefined relations, offering a powerful way to manage related data efficiently.
     * - Transaction management is utilized to ensure that all database operations are executed
     *   atomically, rolling back changes in case of any error, thus maintaining data consistency.
     */
    public function create(array $data): object
    {
        if (!array_key_exists('data', $data)) {
            throw new InvalidArgumentException("The 'data' key is required when creating a new $this->_modelName.");
        }

        if (!is_array($data['data'])) {
            throw new InvalidArgumentException("The 'data' key must contain an associative array.");
        }

        if (!empty($data['include']) && !empty($data['select'])) {
            throw new LogicException("You cannot use both 'include' and 'select' simultaneously.");
        }

        $acceptedCriteria = ['data', 'select', 'include', 'omit'];
        PPHPUtility::checkForInvalidKeys($data, $acceptedCriteria, $this->_modelName);

        $dataToCreate = $data['data'];
        $select = $data['select'] ?? [];
        $include = $data['include'] ?? [];
        $omit = $data['omit'] ?? [];
        $primaryKeyField = '';
        $insertFields = [];
        $placeholders = [];
        $bindings = [];

        $quotedTableName = PPHPUtility::quoteColumnName($this->_dbType, $this->_tableName);

        PPHPUtility::checkFieldsExist(array_merge($dataToCreate, $select, $include), $this->_fields, $this->_modelName);

        try {
            $this->_pdo->beginTransaction();

            foreach ($this->_fields as $field) {
                $fieldName = $field['name'];
                $fieldType = $field['type'];
                $isRequired = $field['isRequired'] ?? false;
                $isObject = ($field['kind'] ?? '') === 'object';
                $isUpdatedAt = $field['isUpdatedAt'] ?? false;
                $dbName = $field['dbName'] ?? $fieldName;
                $isReadOnly = $field['isReadOnly'] ?? false;
                $hasDefaultValue = $field['hasDefaultValue'] ?? false;

                if ($isUpdatedAt) {
                    if (!array_key_exists($fieldName, $dataToCreate) || empty($dataToCreate[$fieldName])) {
                        $bindings[$dbName] = date('Y-m-d H:i:s');
                        $insertFields[] = $dbName;
                        $placeholders[] = ":$dbName";
                    } else {
                        $validateMethodName = lcfirst($fieldType);
                        $bindings[$dbName] = Validator::$validateMethodName($dataToCreate[$fieldName]);
                        $insertFields[] = $dbName;
                        $placeholders[] = ":$dbName";
                    }
                    continue;
                }

                if ($hasDefaultValue) {
                    if (!array_key_exists($fieldName, $dataToCreate) || empty($dataToCreate[$fieldName])) {
                        if (is_array($field['default']) && !empty($field['default']['name'])) {
                            switch ($field['default']['name']) {
                                case 'uuid':
                                    $bindings[$dbName] = \Symfony\Component\Uid\Uuid::v4();
                                    break;
                                case 'ulid':
                                    $bindings[$dbName] = \Symfony\Component\Uid\Ulid::generate();
                                    break;
                                case 'cuid':
                                    $bindings[$dbName] = \CaliCastle\Cuid::make();
                                    break;
                                case 'now':
                                    $bindings[$dbName] = date('Y-m-d H:i:s');
                                    break;
                                default:
                                    continue 2;
                            }
                        } elseif (!is_array($field['default'])) {
                            $validateMethodName = lcfirst($fieldType);
                            $bindings[$dbName] = Validator::$validateMethodName($field['default']);
                        } else {
                            continue;
                        }
                        $insertFields[] = $dbName;
                        $placeholders[] = ":$dbName";
                        continue;
                    }
                }

                if ($isObject) {
                    continue;
                }

                if ($isRequired && !array_key_exists($fieldName, $dataToCreate)) {
                    if ($isReadOnly) {
                        $this->handleRelatedField($fieldName, $dataToCreate, $bindings, $insertFields, $placeholders);
                        continue;
                    }

                    throw new InvalidArgumentException("The required field '$fieldName' is missing.");
                }

                if ($isReadOnly && !array_key_exists($fieldName, $dataToCreate)) {
                    if (!$isRequired) {
                        $this->handleRelatedField($fieldName, $dataToCreate, $bindings, $insertFields, $placeholders);
                        continue;
                    }
                }

                if (array_key_exists($fieldName, $dataToCreate)) {
                    $validateMethodName = lcfirst($fieldType);

                    if ($fieldType === 'Decimal') {
                        $scale = 30;
                        if (!empty($field['nativeType'][1])) {
                            $scale = intval($field['nativeType'][1][1]);
                        }

                        $validatedValue = Validator::$validateMethodName($dataToCreate[$fieldName], $scale);
                    } else {
                        $validatedValue = Validator::$validateMethodName($dataToCreate[$fieldName]);
                    }

                    $bindings[$dbName] = ($validatedValue instanceof BigInteger || $validatedValue instanceof BigDecimal)
                        ? $validatedValue->__toString()
                        : $validatedValue;

                    $insertFields[] = $dbName;
                    $placeholders[] = ":$dbName";
                } elseif (!$isRequired) {
                    $insertFields[] = $dbName;
                    $placeholders[] = "NULL";
                }
            }

            $fieldStr = implode(', ', $insertFields);
            $placeholderStr = implode(', ', $placeholders);

            if (!$this->_primaryKey && !empty($this->_compositeKeys)) {
                $primaryKeyField = implode(', ', $this->_compositeKeys);
            } else {
                $primaryKeyField = $this->_primaryKey;
            }

            $sql = ($this->_dbType == 'pgsql' || $this->_dbType == 'sqlite')
                ? "INSERT INTO $quotedTableName ($fieldStr) VALUES ($placeholderStr) RETURNING $primaryKeyField"
                : "INSERT INTO $quotedTableName ($fieldStr) VALUES ($placeholderStr)";

            $stmt = $this->_pdo->prepare($sql);

            foreach ($bindings as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            if ($this->_dbType == 'pgsql' || $this->_dbType == 'sqlite') {
                $lastInsertId = $stmt->fetch()[$primaryKeyField] ?? null;
            } elseif ($this->_dbType == 'mysql' && in_array($primaryKeyField, $this->_compositeKeys)) {
                $lastInsertId = [];
                foreach ($this->_compositeKeys as $key) {
                    if (isset($bindings[$key])) {
                        $lastInsertId[$key] = $bindings[$key];
                    }
                }
            } else {
                $lastInsertId = $this->_pdo->lastInsertId();
            }

            if (empty($lastInsertId) && isset($bindings[$primaryKeyField])) {
                $lastInsertId = $bindings[$primaryKeyField];
            }

            foreach ($this->_fieldsRelatedWithKeys as $relationName => $relationFields) {
                if (!array_key_exists($relationName, $dataToCreate) || !array_key_exists($relationName, $this->_fields)) {
                    continue;
                }

                $relationToFields   = $relationFields['relationToFields'];
                $relationFromFields = $relationFields['relationFromFields'];
                $relationField = $this->_fields[$relationName];
                $isList        = $relationField['isList'] ?? false;
                $relationType  = $relationField['type'];
                $relatedClass  = "Lib\\Prisma\Classes\\" . $relationType;

                $validActions  = ['create', 'connect', 'connectOrCreate'];
                $relationData  = $dataToCreate[$relationName];

                foreach ($relationData as $action => $records) {
                    if (!in_array($action, $validActions, true)) {
                        throw new Exception(
                            "Invalid relation action '$action'. Allowed: " . implode(', ', $validActions)
                        );
                    }

                    if ($isList) {
                        if (!is_array($records)) {
                            throw new Exception(
                                "Expected an array for '$relationName.$action' because isList = true."
                            );
                        }

                        $areAllKeysNumeric = array_keys($records) === range(0, count($records) - 1);
                        if (!$areAllKeysNumeric) {
                            $records = [$records];
                        }

                        foreach ($records as $index => $singleItem) {
                            if (!is_array($singleItem)) {
                                throw new Exception(
                                    "Expected each item in '$relationName.$action' to be an array, got something else."
                                );
                            }

                            $singleItem = PPHPUtility::mergeForeignKeysIfNeeded(
                                $singleItem,
                                $action,
                                $relationToFields,
                                $relationFromFields,
                                $lastInsertId,
                                $this->_fields
                            );

                            PPHPUtility::processRelation(
                                $relationName,
                                [$action => $singleItem],
                                $relationFromFields,
                                $relationToFields,
                                $relatedClass,
                                $this->_pdo,
                                false,
                            );
                        }
                    } else {
                        if (!is_array($records)) {
                            throw new Exception(
                                "Expected an object/array for '$relationName.$action' because isList = false."
                            );
                        }

                        $records = PPHPUtility::mergeForeignKeysIfNeeded(
                            $records,
                            $action,
                            $relationToFields,
                            $relationFromFields,
                            $lastInsertId,
                            $this->_fields
                        );

                        PPHPUtility::processRelation(
                            $relationName,
                            [$action => $records],
                            $relationFromFields,
                            $relationToFields,
                            $relatedClass,
                            $this->_pdo,
                            false,
                        );
                    }
                }
            }

            $selectOrInclude = '';
            $selectedFields = [];
            if (!empty($select)) {
                $selectOrInclude = 'select';
                $selectedFields = $select;
            } elseif (!empty($include)) {
                $selectOrInclude = 'include';
                $selectedFields = $include;
            }

            $query = [];
            if (is_array($lastInsertId)) {
                $query = ['where' => $lastInsertId];
            } else {
                $query = ['where' => [$primaryKeyField => $lastInsertId]];
            }

            if (!empty($selectedFields)) {
                $query[$selectOrInclude] = $selectedFields;
            }

            $result = $this->findUnique($query);
            $this->_pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->_pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Retrieves a single User record matching specified criteria.
     * 
     * Searches for a unique User based on the provided filter criteria within `$criteria`.
     * It returns the User data as either an associative array or an object, based on the `$format` parameter.
     * This method supports filtering (`where`), field selection (`select`), and inclusion of related models (`include`).
     * If no matching User is found, an empty array is returned. The method includes comprehensive error handling for invalid inputs and parameter conflicts.
     *
     * @param array $criteria Filter criteria with keys:
     *  - 'where': Conditions to filter User records.
     *  - 'select': Fields of the UserRole to return.
     *  - 'include': Related models to include in the result.
     * @return UserRoleData|null The UserRoleData record matching the criteria.
     * 
     * @throws Exception If 'where' condition is missing or not an associative array.
     * @throws Exception If both 'include' and 'select' are provided, as they are mutually exclusive.
     * @throws Exception If invalid or conflicting parameters are supplied.
     * 
     * @example
     * To find a User by ID, select specific fields, and include related models:
     * $user = $prisma->user->findUnique([
     *   'where' => ['id' => 'someUserId'],
     *   'select' => ['name' => true, 'email' => true, 'profile' => true],
     * ]);
     * 
     * @example
     * To find a User by email and include related models:
     * $user = $prisma->user->findUnique([
     *  'where' => ['email' => 'john@example.com'],
     *  'include' => ['profile' => true, 'posts' => true],
     * ]);
     */
    public function findUnique(array $criteria): ?object
    {
        if (!array_key_exists('where', $criteria)) {
            throw new InvalidArgumentException("The 'where' key is required when finding a unique record in $this->_modelName.");
        }

        if (!is_array($criteria['where'])) {
            throw new InvalidArgumentException("The 'where' key must contain an associative array.");
        }

        if (!empty($criteria['include']) && !empty($criteria['select'])) {
            throw new LogicException("You cannot use both 'include' and 'select' simultaneously.");
        }

        $acceptedCriteria = ['where', 'select', 'include', 'omit'];
        PPHPUtility::checkForInvalidKeys($criteria, $acceptedCriteria, $this->_modelName);

        $where = $criteria['where'];
        $select = $criteria['select'] ?? [];
        $include = $criteria['include'] ?? [];
        $omit = $criteria['omit'] ?? [];
        $includeForJoin = [];
        $primaryEntityFields = [];
        $relatedEntityFields = [];
        $includes = [];
        $conditions = [];
        $bindings = [];
        $joins = [];
        $selectFields = [];
        $orderBy = $criteria['orderBy'] ?? [];

        if (!empty($omit)) {
            PPHPUtility::checkFieldsExist($omit, $this->_fields, $this->_modelName);

            $fieldsAssoc = array_fill_keys($this->_fieldsOnly, true);

            $fieldsAssoc = array_diff_key($fieldsAssoc, $omit);

            if (isset($select) && is_array($select)) {
                $select = array_merge($select, $fieldsAssoc);
            } else {
                $select = $fieldsAssoc;
            }
        }

        $whereHasUniqueKey = false;
        foreach ($this->_primaryKeyAndUniqueFields as $key) {
            if (isset($where[$key])) {
                $whereHasUniqueKey = true;
                break;
            }
        }

        if (!$whereHasUniqueKey) {
            throw new Exception("No valid 'where' conditions provided for finding a unique record in $this->_modelName.");
        }

        $quotedTableName = PPHPUtility::quoteColumnName($this->_dbType, $this->_tableName);

        $timestamp = "";
        $hasPrimaryKey = false;
        foreach ($this->_primaryKeyFields as $key) {
            if (isset($select[$key])) {
                $hasPrimaryKey = true;
                break;
            }
        }

        $hasPrimaryKeyProcessed = false;
        foreach ($this->_fieldsRelated as $relationName) {
            if (!array_key_exists($relationName, $select)) {
                continue;
            }

            if (!$hasPrimaryKeyProcessed && !$hasPrimaryKey) {
                $primaryEntityFields = array_merge($primaryEntityFields, $this->_primaryKeyFields);
                $hasPrimaryKeyProcessed = true;
            }

            $includes[$relationName] = $select[$relationName];

            $relationKeyToSelect = $this->_fieldsRelatedWithKeys[$relationName] ?? null;
            if (!empty($relationKeyToSelect['relationFromFields'])) {
                $primaryEntityFields = array_merge($primaryEntityFields, $relationKeyToSelect['relationFromFields']);
            }
        }

        if (!empty($orderBy)) {
            foreach ($this->_fieldsRelated as $relationName) {
                if (isset($orderBy[$relationName])) {
                    $includeForJoin = array_merge($includeForJoin, [$relationName => true]);
                }
            }
        }

        PPHPUtility::checkIncludes($include, $relatedEntityFields, $includes, $this->_fields, $this->_modelName);
        PPHPUtility::checkFieldsExistWithReferences($select, $relatedEntityFields, $primaryEntityFields, $this->_fieldsRelated, $this->_fields, $this->_modelName, $timestamp);

        foreach ($this->_fieldsRelated as $relationName) {
            $field = $this->_fields[$relationName] ?? [];
            if (array_key_exists($relationName, $where) && $field) {
                $relatedClass = "Lib\\Prisma\\Classes\\" . $field['type'];
                $relatedInstance = new $relatedClass($this->_pdo);
                $tableName = PPHPUtility::quoteColumnName($this->_dbType, $relatedInstance->_tableName);
                $relatedFieldKeys = $this->_fieldsRelatedWithKeys[$relationName];
                if (!empty($relatedFieldKeys['relationFromFields']) && !empty($relatedFieldKeys['relationToFields'])) {
                    $joinConditions = [];

                    foreach ($relatedFieldKeys['relationFromFields'] as $index => $fromField) {
                        $toField = $relatedFieldKeys['relationToFields'][$index] ?? null;
                        if ($toField) {
                            $quotedFromField = PPHPUtility::quoteColumnName($this->_dbType, $fromField);
                            $quotedToField = PPHPUtility::quoteColumnName($this->_dbType, $toField);
                            $joinConditions[] = "$tableName.$quotedFromField = $quotedTableName.$quotedToField";
                        }
                    }

                    if (!empty($joinConditions)) {
                        $joins[] = "LEFT JOIN $tableName ON " . implode(" AND ", $joinConditions);
                    }

                    if ($where[$relationName] === null) {
                        $relationCondition = [$relatedFieldKeys['relationFromFields'][0] => null];
                    } else if (!empty($where[$relationName])) {
                        $relationCondition = is_array($where[$relationName])
                            ? array_combine($relatedFieldKeys['relationFromFields'], array_values($where[$relationName]))
                            : [$relatedFieldKeys['relationFromFields'][0] => $where[$relationName]];
                    }

                    PPHPUtility::processConditions($relationCondition, $conditions, $bindings, $this->_dbType, $tableName);

                    unset($where[$relationName]);
                } else {
                    throw new Exception("Relation field not properly defined for '$relationName'");
                }
            }
        }

        if (!empty($includeForJoin)) {
            PPHPUtility::buildJoinsRecursively(
                $includeForJoin,
                $quotedTableName,
                $joins,
                $selectFields,
                $this->_pdo,
                $this->_dbType,
                $this
            );
        }

        if (empty($primaryEntityFields)) {
            $selectFields = array_map(function ($field) use ($quotedTableName) {
                $quotedField = PPHPUtility::quoteColumnName($this->_dbType, $field);
                return "$quotedTableName.$quotedField";
            }, $this->_tableFieldsOnly);
        } else {
            $selectFields = array_map(function ($field) use ($quotedTableName) {
                $quotedField = PPHPUtility::quoteColumnName($this->_dbType, $field);
                return "$quotedTableName.$quotedField";
            }, $primaryEntityFields);
        }

        $sql = "SELECT " . implode(', ', $selectFields) . " FROM $quotedTableName";

        if (!empty($joins)) {
            $sql .= " " . implode(' ', $joins);
        }

        PPHPUtility::processConditions($where, $conditions, $bindings, $this->_dbType, $quotedTableName);

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        PPHPUtility::queryOptions($criteria, $sql, $this->_dbType, $quotedTableName);

        if (empty($conditions)) {
            throw new Exception("No valid 'where' conditions provided for finding a unique record in UserRole.");
        }

        $stmt = $this->_pdo->prepare($sql);
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $record = $stmt->fetch();

        if (!$record) {
            return null;
        }

        $record = PPHPUtility::populateIncludedRelations($record, $includes, $this->_fields, $this->_fieldsRelatedWithKeys, $this->_pdo);

        if (!empty($select)) {
            foreach (array_keys($record) as $key) {
                if (!isset($select[$key])) {
                    unset($record[$key]);
                }
            }
        }

        return (object) $record;
    }


    /**
     * Retrieves multiple User records based on specified filter criteria.
     *
     * This method allows for a comprehensive query with support for filtering, ordering, pagination,
     * selective field retrieval, cursor-based pagination, and including related models. It returns an empty array
     * if no Users match the criteria. This approach ensures flexibility and efficiency in fetching data
     * according to diverse requirements.
     *
     * @param array $criteria Query parameters including:
     *  - 'where': Filter criteria for records.
     *  - 'orderBy': Record ordering logic.
     *  - 'take': Number of records to return, useful for pagination.
     *  - 'skip': Number of records to skip, useful for pagination.
     *  - 'cursor': Cursor for pagination, identifying a specific record to start from.
     *  - 'select': Fields to include in the return value.
     *  - 'include': Related models to include in the result.
     *  - 'distinct': Returns only distinct records if set.
     * @return UserRoleData[] An array of UserRoleData objects or an empty array
     * 
     * @example
     * // Retrieve Users with cursor-based pagination:
     * $users = $prisma->user->findMany([
     *   'cursor' => ['id' => 'someUserId'],
     *   'take' => 5
     * ]);
     * 
     * // Select specific fields of Users:
     * $users = $prisma->user->findMany([
     *   'select' => ['name' => true, 'email' => true],
     *   'take' => 10
     * ]);
     * 
     * // Include related models in the results:
     * $users = $prisma->user->findMany([
     *   'include' => ['posts' => true],
     *   'take' => 5
     * ]);
     * 
     * @throws Exception If 'include' and 'select' are used together, as they are mutually exclusive.
     */
    public function findMany(array $criteria = []): array
    {
        if (isset($criteria['where'])) {
            if (!is_array($criteria['where']) || empty($criteria['where']))
                throw new Exception("No valid 'where' provided for finding multiple records.");
        }

        if (isset($criteria['include']) && isset($criteria['select'])) {
            throw new Exception("You can't use both 'include' and 'select' at the same time.");
        }

        $acceptedCriteria = ['where', 'orderBy', 'take', 'skip', 'cursor', 'select', 'include', 'distinct'];
        PPHPUtility::checkForInvalidKeys($criteria, $acceptedCriteria, $this->_modelName);

        $where = $criteria['where'] ?? [];
        $select = $criteria['select'] ?? [];
        $include = $criteria['include'] ?? [];
        $includeForJoin = [];
        $distinct = isset($criteria['distinct']) && $criteria['distinct'] ? 'DISTINCT' : '';
        $primaryEntityFields = [];
        $relatedEntityFields = [];
        $includes = [];
        $joins = [];
        $selectFields = [];
        $conditions = [];
        $bindings = [];
        $orderBy = $criteria['orderBy'] ?? [];

        $quotedTableName = PPHPUtility::quoteColumnName($this->_dbType, $this->_tableName);

        $timestamp = "";
        $hasPrimaryKey = false;
        foreach ($this->_primaryKeyFields as $key) {
            if (isset($select[$key])) {
                $hasPrimaryKey = true;
                break;
            }
        }

        $hasPrimaryKeyProcessed = false;
        foreach ($this->_fieldsRelated as $relationName) {
            if (!array_key_exists($relationName, $select)) {
                continue;
            }

            if (!$hasPrimaryKeyProcessed && !$hasPrimaryKey) {
                $primaryEntityFields = array_merge($primaryEntityFields, $this->_primaryKeyFields);
                $hasPrimaryKeyProcessed = true;
            }

            $includes[$relationName] = $select[$relationName];

            $relationKeyToSelect = $this->_fieldsRelatedWithKeys[$relationName] ?? null;
            if (!empty($relationKeyToSelect['relationFromFields'])) {
                $primaryEntityFields = array_merge($primaryEntityFields, $relationKeyToSelect['relationFromFields']);
            }
        }

        if (!empty($orderBy)) {
            foreach ($this->_fieldsRelated as $relationName) {
                if (isset($orderBy[$relationName])) {
                    $includeForJoin = array_merge($includeForJoin, [$relationName => true]);
                }
            }
        }

        PPHPUtility::checkIncludes($include, $relatedEntityFields, $includes, $this->_fields, $this->_modelName);
        PPHPUtility::checkFieldsExistWithReferences($select, $relatedEntityFields, $primaryEntityFields, $this->_fieldsRelated, $this->_fields, $this->_modelName, $timestamp);

        if (isset($criteria['cursor']) && is_array($criteria['cursor'])) {
            foreach ($criteria['cursor'] as $field => $value) {
                $select[$field] = ['>=' => $value];
                $fieldQuoted = PPHPUtility::quoteColumnName($this->_dbType, $field);
                $conditions[] = "$fieldQuoted >= :cursor_$field";
                $bindings[":cursor_$field"] = $value;
            }
            if (!isset($select['skip'])) {
                $select['skip'] = 1;
            }
        }

        foreach ($this->_fieldsRelated as $relationName) {
            $field = $this->_fields[$relationName] ?? [];
            if (array_key_exists($relationName, $where) && $field) {
                $relatedClass = "Lib\\Prisma\\Classes\\" . $field['type'];
                $relatedInstance = new $relatedClass($this->_pdo);
                $tableName = PPHPUtility::quoteColumnName($this->_dbType, $relatedInstance->_tableName);
                $relatedFieldKeys = $this->_fieldsRelatedWithKeys[$relationName];
                if (!empty($relatedFieldKeys['relationFromFields']) && !empty($relatedFieldKeys['relationToFields'])) {
                    $joinConditions = [];

                    foreach ($relatedFieldKeys['relationFromFields'] as $index => $fromField) {
                        $toField = $relatedFieldKeys['relationToFields'][$index] ?? null;
                        if ($toField) {
                            $quotedFromField = PPHPUtility::quoteColumnName($this->_dbType, $fromField);
                            $quotedToField = PPHPUtility::quoteColumnName($this->_dbType, $toField);
                            $joinConditions[] = "$tableName.$quotedFromField = $quotedTableName.$quotedToField";
                        }
                    }

                    if (!empty($joinConditions)) {
                        $joins[] = "LEFT JOIN $tableName ON " . implode(" AND ", $joinConditions);
                    }

                    if ($where[$relationName] === null) {
                        $relationCondition = [$relatedFieldKeys['relationFromFields'][0] => null];
                    } else if (!empty($where[$relationName])) {
                        $relationCondition = is_array($where[$relationName])
                            ? array_combine($relatedFieldKeys['relationFromFields'], array_values($where[$relationName]))
                            : [$relatedFieldKeys['relationFromFields'][0] => $where[$relationName]];
                    }

                    PPHPUtility::processConditions($relationCondition, $conditions, $bindings, $this->_dbType, $tableName);

                    unset($where[$relationName]);
                } else {
                    throw new Exception("Relation field not properly defined for '$relationName'");
                }
            }
        }

        if (!empty($includeForJoin)) {
            PPHPUtility::buildJoinsRecursively(
                $includeForJoin,
                $quotedTableName,
                $joins,
                $selectFields,
                $this->_pdo,
                $this->_dbType,
                $this
            );
        }

        if (empty($primaryEntityFields)) {
            $selectFields = array_map(function ($field) use ($quotedTableName) {
                $quotedField = PPHPUtility::quoteColumnName($this->_dbType, $field);
                return "$quotedTableName.$quotedField";
            }, $this->_tableFieldsOnly);
        } else {
            $selectFields = array_map(function ($field) use ($quotedTableName) {
                $quotedField = PPHPUtility::quoteColumnName($this->_dbType, $field);
                return "$quotedTableName.$quotedField";
            }, $primaryEntityFields);
        }

        $sql = "SELECT $distinct " . implode(', ', $selectFields) . " FROM $quotedTableName";

        if (!empty($joins)) {
            $sql .= " " . implode(' ', $joins);
        }

        PPHPUtility::processConditions($where, $conditions, $bindings, $this->_dbType, $quotedTableName);

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        PPHPUtility::queryOptions($criteria, $sql, $this->_dbType, $quotedTableName);

        $stmt = $this->_pdo->prepare($sql);
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $records = $stmt->fetchAll();

        if (!$records) {
            return [];
        }

        $records = PPHPUtility::populateIncludedRelations($records, $includes, $this->_fields, $this->_fieldsRelatedWithKeys, $this->_pdo);

        if (!empty($select)) {
            foreach ($records as &$record) {
                foreach (array_keys($record) as $key) {
                    if (!isset($select[$key])) {
                        unset($record[$key]);
                    }
                }
            }
            unset($record);
        }

        return array_map(fn($data) => (object) $data, $records);
    }

    /**
     * Retrieves the first User record that matches specified filter criteria.
     *
     * Designed to efficiently find and return the first User record matching the provided criteria.
     * This method is optimized for scenarios where only the first matching record is needed, reducing
     * overhead compared to fetching multiple records. It supports filtering, ordering, selective field
     * retrieval, and including related models. Returns an empty array if no match is found.
     *
     * Parameters:
     * - @param array $criteria Associative array of query parameters, which may include:
     *   - 'where': Filter criteria for searching User records.
     *   - 'orderBy': Specifies the order of records.
     *   - 'select': Fields to include in the result.
     *   - 'include': Related models to include in the results.
     *   - 'take': Limits the number of records returned, useful for limiting results to a single record or a specific number of records.
     *   - 'skip': Skips a number of records, useful in conjunction with 'take' for pagination.
     *   - 'cursor': Cursor-based pagination, specifying the record to start retrieving records from.
     *   - 'distinct': Ensures the query returns only distinct records based on the specified field(s).
     *
     * The inclusion of 'take', 'skip', 'cursor', and 'distinct' parameters extends the method's flexibility, allowing for more
     * controlled data retrieval strategies, such as pagination or retrieving unique records. It's important to note that while
     * some of these parameters ('take', 'skip', 'cursor') may not be commonly used with a method intended to fetch the first
     * matching record, they offer additional control for advanced query constructions.
     *
     * Returns:
     * @return UserRoleData|null The UserRoleData record matching the criteria.
     *
     * Examples:
     * // Find a User by email, returning specific fields:
     * $user = $prisma->user->findFirst([
     *   'where' => ['email' => 'user@example.com'],
     *   'select' => ['id', 'email', 'name']
     * ]);
     * // Find an active User, include their posts, ordered by name:
     * $user = $prisma->user->findFirst([
     *   'where' => ['active' => true],
     *   'orderBy' => 'name',
     *   'include' => ['posts' => true]
     * ]);
     *
     * Exception Handling:
     * - Throws Exception if 'include' and 'select' are used together, as they are mutually exclusive.
     * - Throws Exception if no valid 'where' filter is provided, ensuring purposeful searches.
     *
     * This method simplifies querying for a single record, offering control over the search through
     * filtering, sorting, and defining the scope of the returned data. It's invaluable for efficiently
     * retrieving specific records or subsets of fields.
     */
    public function findFirst(array $criteria = [], bool $format = false): ?object
    {
        if (isset($criteria['where'])) {
            if (!is_array($criteria['where']) || empty($criteria['where']))
                throw new Exception("No valid 'where' provided for finding multiple records.");
        }

        if (isset($criteria['include']) && isset($criteria['select'])) {
            throw new LogicException("You cannot use both 'include' and 'select' simultaneously.");
        }

        $acceptedCriteria = ['where', 'orderBy', 'take', 'skip', 'cursor', 'select', 'include', 'distinct'];
        PPHPUtility::checkForInvalidKeys($criteria, $acceptedCriteria, $this->_modelName);

        $where = $criteria['where'] ?? [];
        $select = $criteria['select'] ?? [];
        $include = $criteria['include'] ?? [];
        $includeForJoin = $criteria['include'] ?? [];
        $distinct = isset($criteria['distinct']) && $criteria['distinct'] ? 'DISTINCT' : '';
        $primaryEntityFields = [];
        $relatedEntityFields = [];
        $includes = [];
        $joins = [];
        $selectFields = [];
        $conditions = [];
        $bindings = [];
        $orderBy = $criteria['orderBy'] ?? [];

        $quotedTableName = PPHPUtility::quoteColumnName($this->_dbType, $this->_tableName);

        $timestamp = "";
        $hasPrimaryKey = false;
        foreach ($this->_primaryKeyFields as $key) {
            if (isset($select[$key])) {
                $hasPrimaryKey = true;
                break;
            }
        }

        $hasPrimaryKeyProcessed = false;
        foreach ($this->_fieldsRelated as $relationName) {
            if (!array_key_exists($relationName, $select)) {
                continue;
            }

            if (!$hasPrimaryKeyProcessed && !$hasPrimaryKey) {
                $primaryEntityFields = array_merge($primaryEntityFields, $this->_primaryKeyFields);
                $hasPrimaryKeyProcessed = true;
            }

            $includes[$relationName] = $select[$relationName];

            $relationKeyToSelect = $this->_fieldsRelatedWithKeys[$relationName] ?? null;
            if (!empty($relationKeyToSelect['relationFromFields'])) {
                $primaryEntityFields = array_merge($primaryEntityFields, $relationKeyToSelect['relationFromFields']);
            }
        }

        if (!empty($orderBy)) {
            foreach ($this->_fieldsRelated as $relationName) {
                if (isset($orderBy[$relationName])) {
                    $includeForJoin = array_merge($includeForJoin, [$relationName => true]);
                }
            }
        }

        PPHPUtility::checkIncludes($include, $relatedEntityFields, $includes, $this->_fields, $this->_modelName);
        PPHPUtility::checkFieldsExistWithReferences($select, $relatedEntityFields, $primaryEntityFields, $this->_fieldsRelated, $this->_fields, $this->_modelName, $timestamp);

        if (isset($criteria['cursor']) && is_array($criteria['cursor'])) {
            foreach ($criteria['cursor'] as $field => $value) {
                $select[$field] = ['>=' => $value];
                $fieldQuoted = PPHPUtility::quoteColumnName($this->_dbType, $field);
                $conditions[] = "$fieldQuoted >= :cursor_$field";
                $bindings[":cursor_$field"] = $value;
            }
            if (!isset($select['skip'])) {
                $select['skip'] = 1;
            }
        }

        foreach ($this->_fieldsRelated as $relationName) {
            $field = $this->_fields[$relationName] ?? [];
            if (array_key_exists($relationName, $where) && $field) {
                $relatedClass = "Lib\\Prisma\\Classes\\" . $field['type'];
                $relatedInstance = new $relatedClass($this->_pdo);
                $tableName = PPHPUtility::quoteColumnName($this->_dbType, $relatedInstance->_tableName);
                $relatedFieldKeys = $this->_fieldsRelatedWithKeys[$relationName];
                if (!empty($relatedFieldKeys['relationFromFields']) && !empty($relatedFieldKeys['relationToFields'])) {
                    $joinConditions = [];

                    foreach ($relatedFieldKeys['relationFromFields'] as $index => $fromField) {
                        $toField = $relatedFieldKeys['relationToFields'][$index] ?? null;
                        if ($toField) {
                            $quotedFromField = PPHPUtility::quoteColumnName($this->_dbType, $fromField);
                            $quotedToField = PPHPUtility::quoteColumnName($this->_dbType, $toField);
                            $joinConditions[] = "$tableName.$quotedFromField = $quotedTableName.$quotedToField";
                        }
                    }

                    if (!empty($joinConditions)) {
                        $joins[] = "LEFT JOIN $tableName ON " . implode(" AND ", $joinConditions);
                    }

                    if ($where[$relationName] === null) {
                        $relationCondition = [$relatedFieldKeys['relationFromFields'][0] => null];
                    } else if (!empty($where[$relationName])) {
                        $relationCondition = is_array($where[$relationName])
                            ? array_combine($relatedFieldKeys['relationFromFields'], array_values($where[$relationName]))
                            : [$relatedFieldKeys['relationFromFields'][0] => $where[$relationName]];
                    }

                    PPHPUtility::processConditions($relationCondition, $conditions, $bindings, $this->_dbType, $tableName);

                    unset($where[$relationName]);
                } else {
                    throw new Exception("Relation field not properly defined for '$relationName'");
                }
            }
        }

        if (!empty($includeForJoin)) {
            PPHPUtility::buildJoinsRecursively(
                $includeForJoin,
                $quotedTableName,
                $joins,
                $selectFields,
                $this->_pdo,
                $this->_dbType,
                $this
            );
        }

        if (empty($primaryEntityFields)) {
            $selectFields[] = "$quotedTableName.*";
        } else {
            $selectFields = array_map(function ($field) use ($quotedTableName) {
                $quotedField = PPHPUtility::quoteColumnName($this->_dbType, $field);
                return "$quotedTableName.$quotedField";
            }, $primaryEntityFields);
        }
        
        $sql = "SELECT $distinct " . implode(', ', $selectFields) . " FROM $quotedTableName";

        if (!empty($joins)) {
            $sql .= " " . implode(' ', $joins);
        }

        PPHPUtility::processConditions($where, $conditions, $bindings, $this->_dbType, $quotedTableName);

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        PPHPUtility::queryOptions($criteria, $sql, $this->_dbType, $quotedTableName);

        $sql .= " LIMIT 1";

        $stmt = $this->_pdo->prepare($sql);
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $record = $stmt->fetch();

        if (!$record) {
            return null;
        }

        $record = PPHPUtility::populateIncludedRelations($record, $includes, $this->_fields, $this->_fieldsRelatedWithKeys, $this->_pdo);

        if (!empty($select)) {
            foreach (array_keys($record) as $key) {
                if (!isset($select[$key])) {
                    unset($record[$key]);
                }
            }
        }

        return (object) $record;
    }

    /**
     * Updates a User in the database.
     *
     * This method updates an existing User record based on the provided filter criteria and
     * update data. It supports updating related records through relations defined in the User model,
     * such as 'userRole', 'product', 'post', and 'Profile'. Additionally, it allows for selective field
     * return and including related models in the response after the update.
     *
     * Workflow:
     * 1. Validates the presence of 'where' and 'data' keys in the input array.
     * 2. Checks for exclusivity between 'select' and 'include' keys, throwing an exception if both are present.
     * 3. Prepares the SQL UPDATE statement based on the provided criteria and data.
     * 4. Executes the update operation within a database transaction to ensure data integrity.
     * 5. Processes any specified relations (e.g., creating related records) as part of the update.
     * 6. Customizes the returned user record based on 'select' or 'include' parameters if specified.
     * 7. Commits the transaction and returns the updated user record, optionally with related data.
     *
     * The method ensures data integrity and consistency throughout the update process by employing
     * transactions. This approach allows for rolling back changes in case of an error, thereby preventing
     * partial updates or data corruption.
     *
     * Parameters:
     * - @param array $data An associative array containing the update criteria and data, which includes:
     *   - 'where': Filter criteria to identify the User to update.
     *   - 'data': The data to update in the User record.
     *   - 'select': Optionally, specifies a subset of fields to return.
     *   - 'include': Optionally, specifies related models to include in the result.
     * 
     * Returns:
     * @return UserRoleData|array|null The updated User record or an empty array if no match is found.
     * 
     * Example Usage:
     * // Example 1: Update a User's email and only return their 'id' and 'email' in the response
     * $updatedUserWithSelect = $prisma->user->update([
     *   'where' => ['id' => 'someUserId'],
     *   'data' => ['email' => 'new.email@example.com'],
     *   'select' => ['id' => true, 'email' => true]
     * ]);
     * 
     * // Example 2: Update a User's username and include their profile information in the response
     * $updatedUserWithInclude = $prisma->user->update([
     *   'where' => ['id' => 'someUserId'],
     *   'data' => ['username' => 'newUsername'],
     *   'include' => ['profile' => true]
     * ]);
     * 
     * Throws:
     * @throws Exception if both 'include' and 'select' are used simultaneously, or in case of any error during the update process.
     */
    public function update(array $data): array|object
    {
        if (!isset($data['where'])) {
            throw new Exception("The 'where' key is required in the update UserRole.");
        }

        if (!is_array($data['where'])) {
            throw new Exception("'where' must be an associative array.");
        }

        if (!isset($data['data'])) {
            throw new Exception("The 'data' key is required in the update UserRole.");
        }

        if (!is_array($data['data'])) {
            throw new Exception("'data' must be an associative array.");
        }

        if (isset($data['include']) && isset($data['select'])) {
            throw new LogicException("You cannot use both 'include' and 'select' simultaneously.");
        }

        $acceptedCriteria = ['where', 'data', 'select', 'include'];
        PPHPUtility::checkForInvalidKeys($data, $acceptedCriteria, $this->_modelName);

        $criteria = $data;
        $where = $data['where'];
        $select = $data['select'] ?? [];
        $include = $data['include'] ?? [];
        $data = $data['data'];

        $quotedTableName = PPHPUtility::quoteColumnName($this->_dbType, $this->_tableName);
        $sql = "UPDATE $quotedTableName SET ";
        $updateFields = [];
        $bindings = [];
    

        PPHPUtility::checkFieldsExist(array_merge($data, $select, $include), $this->_fields, $this->_modelName);

        try {
            $this->_pdo->beginTransaction();

            foreach ($this->_fields as $field) {
                $fieldName = $field['name'];
                $fieldType = $field['type'];
                $isRequired = $field['isRequired'] ?? false;
                $isObject = ($field['kind'] ?? '') === 'object';
                
                if (isset($data[$fieldName]) || !$isRequired) {
                    if (!array_key_exists($fieldName, $data)) continue;
                    if ($isObject) continue;
                    $validateMethodName = lcfirst($fieldType);
                    $validatedValue = Validator::$validateMethodName($data[$fieldName]);
                    $updateFields[] = PPHPUtility::quoteColumnName($this->_dbType, $fieldName) . " = :$fieldName";
                    $bindings[":$fieldName"] = $validatedValue;
                } else {
                    if (array_key_exists($fieldName, $data) && $isRequired) {
                        if ($isObject) continue;
                        $updateFields[] = PPHPUtility::quoteColumnName($this->_dbType, $fieldName) . " = NULL";
                    }
                }
            }
            
            if (!empty($updateFields)) {
                $sql .= implode(', ', $updateFields);
                $conditions = [];

                PPHPUtility::processConditions($where, $conditions, $bindings, $this->_dbType, $quotedTableName);

                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(' AND ', $conditions);
                }

                PPHPUtility::queryOptions($criteria, $sql, $this->_dbType, $quotedTableName);

                $stmt = $this->_pdo->prepare($sql);
                foreach ($bindings as $key => $value) {
                    $stmt->bindValue($key, $value);
                }

                $stmt->execute();
            }

            $selectOrInclude = '';
            $selectedFields = [];
            if (!empty($select)) {
                $selectOrInclude = 'select';
                $selectedFields = $select;
            } elseif (!empty($include)) {
                $selectOrInclude = 'include';
                $selectedFields = $include;
            }

            $query = ['where' => $where];

            if (!empty($selectedFields)) {
                $query[$selectOrInclude] = $selectedFields;
            }

            $result = $this->findFirst($query);
            $this->_pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $this->_pdo->rollBack(); // Rollback transaction on error
            throw $e;
        }
    }

    /**
     * Deletes a User from the database based on specified criteria.
     *
     * This method enables the deletion of an existing User record through filter criteria
     * defined in an associative array. Before deletion, it verifies the User's existence and
     * optionally returns the User's data pre-deletion. It ensures precise deletion by requiring
     * conditions that uniquely identify the User.
     * 
     * @param array $criteria An associative array containing the filter criteria to locate and delete the User.
     *                        The 'where' key within this array is mandatory and should uniquely identify a single User record.
     *                        Optionally, 'select' or 'include' keys may be provided (but not both) to specify which data to return
     *                        upon successful deletion.
     * @return UserRoleData|array On successful deletion, returns the deleted UserRoleData record.
     *               If the deletion is unsuccessful due to a non-existent UserRoleData or non-unique criteria, returns an
     *               array with 'modelName' and 'cause' keys, indicating the reason for failure.
     * 
     * @example
     * // Delete a User by ID and return the deleted User's data as an array
     * $deletedUser = $prisma->user->delete([
     *   'where' => ['id' => 'someUserId']
     * ]);
     * 
     * @example
     * // Delete a User by ID, selecting specific fields to return
     * $deletedUser = $prisma->user->delete([
     *   'where' => ['id' => 'someUserId'],
     *   'select' => ['name' => true, 'email' => true]
     * ]);
     * 
     * @example
     * // Delete a User by ID, including related records in the return value
     * $deletedUser = $prisma->user->delete([
     *   'where' => ['id' => 'someUserId'],
     *   'include' => ['posts' => true]
     * ]);
     * 
     * @throws Exception if the 'where' key is missing or not an associative array in `$criteria`.
     * @throws Exception if both 'include' and 'select' keys are present in `$criteria`, as they cannot be used simultaneously.
     * @throws Exception if there's an error during the deletion process or if the transaction fails,
     *                   indicating the nature of the error for debugging purposes.
     */
    public function delete(array $criteria): object|array
    {
        if (!isset($criteria['where'])) {
            throw new Exception("The 'where' key is required in the delete User.");
        }

        if (!is_array($criteria['where'])) {
            throw new Exception("'where' must be an associative array.");
        }

        if (isset($criteria['include']) && isset($criteria['select'])) {
            throw new LogicException("You cannot use both 'include' and 'select' simultaneously.");
        }

        $acceptedCriteria = ['where', 'select', 'include'];
        PPHPUtility::checkForInvalidKeys($criteria, $acceptedCriteria, $this->_modelName);

        $where = $criteria['where'];
        $select = $criteria['select'] ?? [];
        $include = $criteria['include'] ?? [];
        $whereClauses = [];
        $bindings = [];

        try {
            $this->_pdo->beginTransaction();

            $quotedTableName = PPHPUtility::quoteColumnName($this->_dbType, $this->_tableName);

            PPHPUtility::processConditions($where, $whereClauses, $bindings, $this->_dbType, $quotedTableName);

            $sql = "DELETE FROM $quotedTableName WHERE ";
            $sql .= implode(' AND ', $whereClauses);

            $stmt = $this->_pdo->prepare($sql);
            foreach ($bindings as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $selectOrInclude = '';
            $selectedFields = [];
            if (!empty($select)) {
                $selectOrInclude = 'select';
                $selectedFields = $select;
            } elseif (!empty($include)) {
                $selectOrInclude = 'include';
                $selectedFields = $include;
            }

            $query = ['where' => $where];

            if (!empty($selectedFields)) {
                $query[$selectOrInclude] = $selectedFields;
            }

            $deletedRow = $this->findFirst($query);

            $stmt->execute();
            $affectedRows = $stmt->rowCount();
            $this->_pdo->commit();

            return $affectedRows ? $deletedRow : ['modelName' => 'UserRole', 'cause' => 'Record to delete does not exist.'];
        } catch (\Exception $e) {
            $this->_pdo->rollBack();
            throw $e;
        }
    }}
