<?php
// 测试日期参数和数据获取
require_once 'config.php';

// 设置为开发模式，显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 设置内容类型为HTML
header('Content-Type: text/html; charset=utf-8');

// 获取日期参数
$date = isset($_GET['date']) ? $_GET['date'] : getTodayDate();

// 输出测试信息
echo "<h1>日期参数测试</h1>";
echo "<p>接收到的日期参数: " . htmlspecialchars($date) . "</p>";

// 读取报告数据
$reports = getReports();
echo "<h2>可用的日期列表</h2>";
echo "<ul>";
foreach (array_keys($reports) as $reportDate) {
    echo "<li>" . htmlspecialchars($reportDate) . "</li>";
}
echo "</ul>";

// 显示选定日期的数据
echo "<h2>选定日期的数据</h2>";
if (isset($reports[$date])) {
    echo "<table border='1'>";
    echo "<tr><th>部门</th><th>数据</th></tr>";
    foreach ($reports[$date] as $dept => $data) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($dept) . "</td>";
        echo "<td><pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>该日期没有数据: " . htmlspecialchars($date) . "</p>";
}

// 读取历史数据
$history = getHistory();
echo "<h2>历史记录</h2>";

// 过滤指定日期的历史记录
$filteredHistory = [];
foreach ($history as $item) {
    if ($item['date'] === $date) {
        $filteredHistory[] = $item;
    }
}

if (count($filteredHistory) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>部门</th><th>时间戳</th><th>内容</th></tr>";
    foreach ($filteredHistory as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['department']) . "</td>";
        echo "<td>" . htmlspecialchars($item['timestamp']) . "</td>";
        echo "<td><pre>" . htmlspecialchars(json_encode($item['content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>该日期没有历史记录: " . htmlspecialchars($date) . "</p>";
}

echo "<h2>测试链接</h2>";
echo "<ul>";
echo "<li><a href='test_date.php'>今日数据</a></li>";
echo "<li><a href='test_date.php?date=2023-03-18'>2023-03-18数据</a></li>";
echo "<li><a href='test_date.php?date=2023-03-19'>2023-03-19数据</a></li>";
echo "</ul>"; 