<?php
// 获取报告数据API
require_once 'config.php';

// 设置内容类型为JSON
header('Content-Type: application/json');

// 获取特定日期的数据或所有数据
$date = isset($_GET['date']) ? $_GET['date'] : null;

// 读取报告数据
$reports = getReports();

// 如果指定了日期，只返回该日期的数据
if ($date && isset($reports[$date])) {
    echo json_encode([$date => $reports[$date]]);
} else if ($date) {
    // 如果指定了日期但没有数据，返回空对象
    echo json_encode([$date => []]);
} else {
    // 返回所有数据
    echo json_encode($reports);
}
