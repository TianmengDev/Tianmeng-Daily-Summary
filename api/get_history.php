<?php
// 获取历史记录API
require_once 'config.php';

// 设置为开发模式，显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 设置内容类型为JSON
header('Content-Type: application/json');

// 获取特定日期的历史记录或所有历史记录
$date = isset($_GET['date']) ? $_GET['date'] : null;

// 记录日志到文件
file_put_contents(__DIR__ . '/history_log.txt', 
    date('Y-m-d H:i:s') . " - 获取历史请求，日期参数: " . ($date ? $date : "无") . "\n", 
    FILE_APPEND
);

// 读取历史数据
$history = getHistory();

// 记录历史数据总数
file_put_contents(__DIR__ . '/history_log.txt', 
    date('Y-m-d H:i:s') . " - 历史记录总数: " . count($history) . "\n", 
    FILE_APPEND
);

// 尝试获取报告数据作为备选
$reports = getReports();
file_put_contents(__DIR__ . '/history_log.txt', 
    date('Y-m-d H:i:s') . " - 可用的报告日期: " . implode(", ", array_keys($reports)) . "\n", 
    FILE_APPEND
);

// 如果指定了日期，只返回该日期的历史记录
if ($date) {
    // 从历史记录中过滤
    $filteredHistory = [];
    foreach ($history as $item) {
        if ($item['date'] === $date) {
            $filteredHistory[] = $item;
        }
    }
    
    // 如果历史记录为空，但报告中有该日期的数据，转换报告数据为历史记录格式
    if (empty($filteredHistory) && isset($reports[$date])) {
        foreach ($reports[$date] as $dept => $data) {
            $filteredHistory[] = array(
                'department' => $dept,
                'date' => $date,
                'timestamp' => isset($data['updatedAt']) ? $data['updatedAt'] : date('c'),
                'content' => array(
                    'todayWork' => isset($data['todayWork']) ? $data['todayWork'] : '无',
                    'problems' => isset($data['problems']) ? $data['problems'] : '无',
                    'tomorrowPlan' => isset($data['tomorrowPlan']) ? $data['tomorrowPlan'] : '无',
                    'data' => isset($data['data']) ? $data['data'] : '无',
                    'remarks' => isset($data['remarks']) ? $data['remarks'] : '无'
                )
            );
        }
    }
    
    file_put_contents(__DIR__ . '/history_log.txt', 
        date('Y-m-d H:i:s') . " - 日期 $date 的历史记录数: " . count($filteredHistory) . "\n", 
        FILE_APPEND
    );
    
    // 确保始终返回数组格式
    echo json_encode($filteredHistory);
} else {
    // 返回所有历史记录
    file_put_contents(__DIR__ . '/history_log.txt', 
        date('Y-m-d H:i:s') . " - 返回所有历史记录\n", 
        FILE_APPEND
    );
    
    echo json_encode($history);
}
