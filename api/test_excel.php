<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PhpSpreadsheet测试</h1>";

echo "<h2>1. 检查目录结构</h2>";
$phpspreadsheet_dir = __DIR__ . '/PhpSpreadsheet/src';
if (is_dir($phpspreadsheet_dir)) {
    echo "PhpSpreadsheet目录存在: $phpspreadsheet_dir<br>";
    
    $files = scandir($phpspreadsheet_dir);
    echo "目录内容: <pre>" . print_r($files, true) . "</pre>";
    
    // 检查关键文件
    $spreadsheet_file = $phpspreadsheet_dir . '/Spreadsheet.php';
    if (file_exists($spreadsheet_file)) {
        echo "核心Spreadsheet.php文件存在<br>";
    } else {
        echo "错误: 核心Spreadsheet.php文件不存在!<br>";
    }
} else {
    echo "错误: PhpSpreadsheet目录不存在!<br>";
}

echo "<h2>2. 测试自动加载</h2>";

// 创建临时自动加载器
function phpspreadsheet_autoloader($class) {
    $prefix = 'PhpOffice\\PhpSpreadsheet\\';
    $base_dir = __DIR__ . '/PhpSpreadsheet/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
        echo "成功加载: $file<br>";
    } else {
        echo "无法找到文件: $file<br>";
    }
}

// 注册自动加载器
spl_autoload_register('phpspreadsheet_autoloader');

echo "<h2>3. 尝试创建Spreadsheet对象</h2>";
try {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    echo "成功创建Spreadsheet对象!<br>";
} catch (Exception $e) {
    echo "创建Spreadsheet对象时出错: " . $e->getMessage() . "<br>";
}
