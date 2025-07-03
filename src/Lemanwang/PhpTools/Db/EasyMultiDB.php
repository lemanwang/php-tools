<?php
/**
 * Created by : UncleFreak
 * User: UncleFreak <00@z88j.com>
 * Date: 2022/1/21
 * Time: 17:39
 */

namespace Lemanwang\PhpTools\Db;
use PDO;
use \PDOException;
use \PDOStatement;
/**
 * 优化的数据库操作类
 * 支持 MySQL/PostgreSQL 双数据库
 * 支持多连接切换和连接池管理
 */
class EasyMultiDB
{
    // 数据库连接池
    private static $connections = [];

    // 数据库配置集合
    private static $configs = [
        'default' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => 3306,
            'database'  => 'test',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8mb4',
            'options'   => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
//                PDO::ATTR_PERSISTENT => false,
            ]
        ]
    ];


    // 当前实例使用的连接名
    private $connectionName;

    // PDO 实例
    private $pdo;

    // 最后执行的 SQL
    private $lastSql = '';

    // 最后执行的参数
    private $lastParams = [];

    /**
     * 构造函数
     * @param string $name 连接名称
     */
    public function __construct($name = 'default')
    {
        $this->switchConnection($name);
    }

    /**
     * 添加数据库配置（静态方法，全局配置）
     * @param string $name 连接名称
     * @param array $config 数据库配置
     */
    public static function addConfig($name, array $config)
    {
        self::$configs[$name] = array_merge(self::$configs['default'], $config);
    }

    /**
     * 切换当前数据库连接
     * @param string $name 连接名称
     * @return $this
     */
    public function switchConnection($name)
    {
        if (!isset(self::$configs[$name])) {
            throw new Exception("数据库配置 '{$name}' 不存在");
        }

        $this->connectionName = $name;

        // 获取或创建连接
        if (!isset(self::$connections[$name])) {
            self::$connections[$name] = $this->createConnection($name);
        }

        $this->pdo = self::$connections[$name];
        return $this;
    }

    /**
     * 创建新的数据库连接
     * @param string $name 连接名称
     * @return PDO
     */
    private function createConnection($name)
    {
        $config = self::$configs[$name];

        // 根据驱动类型构造 DSN
        $dsn = $this->buildDSN($config);

        try {
            return new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            throw new Exception("数据库连接失败: " . $e->getMessage());
        }
    }

    /**
     * 构建数据库 DSN
     * @param array $config
     * @return string
     */
    private function buildDSN(array $config)
    {
        $driver = strtolower($config['driver']);

        switch ($driver) {
            case 'mysql':
                return "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

            case 'pgsql':
                return "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";

            default:
                throw new Exception("不支持的数据库驱动: {$driver}");
        }
    }

    /**
     * 执行 SQL 查询
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, array $params = [])
    {
        $this->lastSql = $sql;
        $this->lastParams = $params;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * 获取单行结果
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    public function fetch($sql, array $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * 获取所有结果
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll($sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * 安全的分页查询方法
     * @param string $sql 基础 SQL 查询
     * @param int $page 页码 (从1开始)
     * @param int $pageSize 每页数量
     * @param array $params 查询参数
     * @return array
     */
    public function paginate($sql, $page = 1, $pageSize = 10, array $params = [])
    {
        $offset = max(0, ($page - 1) * $pageSize);
        $driver = self::$configs[$this->connectionName]['driver'] ?? 'mysql';

        // 生成正确的分页子句
        if ($driver === 'pgsql') {
            $pagedSql = $sql . " LIMIT $pageSize OFFSET $offset";
        } else {
            $pagedSql = $sql . " LIMIT $offset, $pageSize";
        }

        $data = $this->fetchAll($pagedSql, $params);

        // 获取总数
        $countSql = "SELECT COUNT(*) AS total_count FROM ($sql) AS total_table";
        $total = $this->fetchColumn($countSql, $params);

        return [
            'data' => $data,
            'total' => (int)$total,
            'current_page' => $page,
            'page_size' => $pageSize,
            'total_pages' => ceil($total / $pageSize)
        ];
    }

    /**
     * 获取数据库驱动类型
     * @return string
     */
    public function getDriver()
    {
        return self::$configs[$this->connectionName]['driver'] ?? 'mysql';
    }

    /**
     * 获取单列值
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function fetchColumn($sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * 插入数据
     * @param string $table
     * @param array $data
     * @return string 返回最后插入的ID
     */
    public function insert($table, array $data, $primaryKey = 'id')
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $params = array_values($data);

        $driver = $this->getDriver();

        if ($driver === 'pgsql') {
            // 使用 RETURNING 子句获取插入的ID
            $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders}) RETURNING {$primaryKey}";
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetch();
            return $result[$primaryKey];
        } else {
            // MySQL 和其他数据库使用 lastInsertId
            $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
            $this->query($sql, $params);
            return $this->pdo->lastInsertId();
        }
    }


    public function update($table, array $data, $where, array $params = [])
    {
        // 1. 验证输入
        if (empty($table) || !is_string($table)) {
            throw new InvalidArgumentException('表名必须是非空字符串');
        }

        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException('更新数据必须是非空数组');
        }

        // 2. 构建 SET 部分（使用命名参数避免冲突）
        $set = [];
        $setValues = [];
        $paramIndex = 0;

        foreach ($data as $key => $value) {
            // 使用唯一参数名避免冲突
            $paramName = ":set_{$key}_{$paramIndex}";
            $set[] = "{$key} = {$paramName}";
            $setValues[$paramName] = $value;
            $paramIndex++;
        }

        // 3. 处理 WHERE 条件（支持命名参数和位置参数）
        $whereValues = [];
        $processedWhere = $this->processWhereClause($where, $params, $whereValues);

        // 4. 合并所有参数
        $allParams = array_merge($setValues, $whereValues);

        // 5. 构建 SQL
        $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $processedWhere";

//        var_dump($sql);
//        var_dump($allParams);exit;
        // 6. 执行查询
        $stmt = $this->query($sql, $allParams);

        return $stmt->rowCount();
    }

    /**
     * 处理 WHERE 子句，支持多种格式
     */
    protected function processWhereClause($where, $params, &$bindValues)
    {
        // 情况1: WHERE 子句已经是完整字符串
        if (is_string($where)) {
            // 检查是否是关联数组（命名参数）
            if ($this->isAssoc($params)) {
                $bindValues = $params;
                return $where;
            }

            // 位置参数: 转换为命名参数
            $whereParts = preg_split('/\?/', $where, -1, PREG_SPLIT_NO_EMPTY);
            $processedWhere = '';
            $paramIndex = 0;

            foreach ($whereParts as $i => $part) {
                $processedWhere .= $part;
                if ($i < count($whereParts) - 1) {
                    $paramName = ":where_{$paramIndex}";
                    $processedWhere .= $paramName;
                    $bindValues[$paramName] = $params[$i];
                    $paramIndex++;
                }
            }

            return $processedWhere;
        }

        // 情况2: WHERE 是条件数组
        if (is_array($where)) {
            $conditions = [];
            foreach ($where as $key => $value) {
                $paramName = ":where_{$key}";
                $conditions[] = "{$key} = {$paramName}";
                $bindValues[$paramName] = $value;
            }
            return implode(' AND ', $conditions);
        }

        throw new InvalidArgumentException('WHERE 条件必须是字符串或数组');
    }

    /**
     * 检查是否为关联数组
     */
    protected function isAssoc(array $arr)
    {
        if ([] === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }


    /**
     * 删除数据
     * @param string $table
     * @param string $where
     * @param array $params
     * @return int 受影响的行数
     */
    public function delete($table, $where, array $params = [])
    {
        // 1. 输入验证
        if (empty($table) || !is_string($table)) {
            throw new InvalidArgumentException('表名必须是非空字符串');
        }

        // 2. 处理 WHERE 条件（支持多种格式）
        $whereValues = [];
        $processedWhere = $this->processWhereClause($where, $params, $whereValues);

        // 3. 构建安全删除语句
        $sql = "DELETE FROM {$table} WHERE {$processedWhere}";

//        var_dump($sql);exit;
        // 4. 执行查询
        $stmt = $this->query($sql, $whereValues);

        return $stmt->rowCount();
    }

    /**
     * 开始事务
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * 回滚事务
     * @return bool
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * 获取最后执行的 SQL
     * @return string
     */
    public function getLastSql()
    {
        return $this->lastSql;
    }

    /**
     * 获取最后执行的参数
     * @return array
     */
    public function getLastParams()
    {
        return $this->lastParams;
    }

    /**
     * 关闭所有数据库连接（静态方法）
     */
    public static function closeAllConnections()
    {
        foreach (self::$connections as $name => $connection) {
            $connection = null;
            unset(self::$connections[$name]);
        }
    }
}

