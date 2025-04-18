<?php
// 自定义PhpSpreadsheet自动加载器

// 主命名空间
$namespaces = [
    'PhpOffice\\PhpSpreadsheet\\' => __DIR__ . '/src/',
    'Psr\\SimpleCache\\' => __DIR__ . '/../Psr/SimpleCache/'
];

// SPL自动加载函数
spl_autoload_register(function ($class) use ($namespaces) {
    foreach ($namespaces as $namespace => $dir) {
        // 检查类是否使用此命名空间
        $len = strlen($namespace);
        if (strncmp($namespace, $class, $len) !== 0) {
            continue;
        }
        
        // 获取相对类名
        $relative_class = substr($class, $len);
        
        // 转换为文件路径
        $file = $dir . str_replace('\\', '/', $relative_class) . '.php';
        
        // 如果文件存在，加载它
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    return false;
});
