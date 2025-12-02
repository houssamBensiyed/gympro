<?php
/**
 * ============================================
 * GYM MANAGEMENT PLATFORM
 * Database Connection (mysqli)
 * ============================================
 */

// Include configuration
require_once __DIR__ . '/config.php';

// ============================================
// DATABASE CONNECTION
// ============================================

/**
 * Get database connection
 * 
 * @return mysqli|null Database connection object
 */
function getDbConnection(): ?mysqli
{
    static $conn = null;
    
    if ($conn === null) {
        // Enable mysqli exception mode
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            $conn = new mysqli(
                DB_HOST,
                DB_USERNAME,
                DB_PASSWORD,
                DB_DATABASE,
                (int) DB_PORT
            );
            
            // Set charset
            $conn->set_charset(DB_CHARSET);
            
            // Set SQL mode
            $conn->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
            
        } catch (mysqli_sql_exception $e) {
            if (APP_DEBUG) {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                error_log("Database Connection Error: " . $e->getMessage());
                die("Database connection failed. Please try again later.");
            }
        }
    }
    
    return $conn;
}

/**
 * Close database connection
 * 
 * @param mysqli $conn Database connection
 * @return void
 */
function closeDbConnection(mysqli $conn): void
{
    $conn->close();
}

/**
 * Execute a SELECT query and return results
 * 
 * @param string $query SQL query
 * @param array $params Parameters for prepared statement
 * @param string $types Parameter types (i, d, s, b)
 * @return array Query results
 */
function dbSelect(string $query, array $params = [], string $types = ''): array
{
    $conn = getDbConnection();
    $results = [];
    
    try {
        $stmt = $conn->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        
        $stmt->close();
        
    } catch (mysqli_sql_exception $e) {
        error_log("Database Query Error: " . $e->getMessage());
        if (APP_DEBUG) {
            throw $e;
        }
    }
    
    return $results;
}

/**
 * Execute a SELECT query and return single row
 * 
 * @param string $query SQL query
 * @param array $params Parameters for prepared statement
 * @param string $types Parameter types
 * @return array|null Single row or null
 */
function dbSelectOne(string $query, array $params = [], string $types = ''): ?array
{
    $results = dbSelect($query, $params, $types);
    return $results[0] ?? null;
}

/**
 * Execute INSERT query
 * 
 * @param string $query SQL query
 * @param array $params Parameters
 * @param string $types Parameter types
 * @return int|false Insert ID or false on failure
 */
function dbInsert(string $query, array $params = [], string $types = ''): int|false
{
    $conn = getDbConnection();
    
    try {
        $stmt = $conn->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $insertId = $stmt->insert_id;
        $stmt->close();
        
        return $insertId;
        
    } catch (mysqli_sql_exception $e) {
        error_log("Database Insert Error: " . $e->getMessage());
        if (APP_DEBUG) {
            throw $e;
        }
        return false;
    }
}

/**
 * Execute UPDATE query
 * 
 * @param string $query SQL query
 * @param array $params Parameters
 * @param string $types Parameter types
 * @return int|false Affected rows or false on failure
 */
function dbUpdate(string $query, array $params = [], string $types = ''): int|false
{
    $conn = getDbConnection();
    
    try {
        $stmt = $conn->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $affectedRows;
        
    } catch (mysqli_sql_exception $e) {
        error_log("Database Update Error: " . $e->getMessage());
        if (APP_DEBUG) {
            throw $e;
        }
        return false;
    }
}

/**
 * Execute DELETE query
 * 
 * @param string $query SQL query
 * @param array $params Parameters
 * @param string $types Parameter types
 * @return int|false Affected rows or false on failure
 */
function dbDelete(string $query, array $params = [], string $types = ''): int|false
{
    return dbUpdate($query, $params, $types);
}

/**
 * Get count from a table
 * 
 * @param string $table Table name
 * @param string $condition WHERE condition
 * @param array $params Parameters
 * @param string $types Parameter types
 * @return int Count
 */
function dbCount(string $table, string $condition = '', array $params = [], string $types = ''): int
{
    $query = "SELECT COUNT(*) as count FROM " . $table;
    if (!empty($condition)) {
        $query .= " WHERE " . $condition;
    }
    
    $result = dbSelectOne($query, $params, $types);
    return (int) ($result['count'] ?? 0);
}

/**
 * Escape string for safe SQL usage
 * 
 * @param string $value Value to escape
 * @return string Escaped value
 */
function dbEscape(string $value): string
{
    $conn = getDbConnection();
    return $conn->real_escape_string($value);
}

/**
 * Begin transaction
 * 
 * @return bool Success
 */
function dbBeginTransaction(): bool
{
    $conn = getDbConnection();
    return $conn->begin_transaction();
}

/**
 * Commit transaction
 * 
 * @return bool Success
 */
function dbCommit(): bool
{
    $conn = getDbConnection();
    return $conn->commit();
}

/**
 * Rollback transaction
 * 
 * @return bool Success
 */
function dbRollback(): bool
{
    $conn = getDbConnection();
    return $conn->rollback();
}