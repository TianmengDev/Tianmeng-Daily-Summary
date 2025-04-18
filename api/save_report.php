<?php
// 保存报告API
require_once 'config.php';

// 设置内容类型为JSON
header('Content-Type: application/json');

// 获取POST数据（JSON格式）
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// 验证数据
if (!$data || !isset($data['department'])) {
    echo json_encode(['success' => false, 'message' => '数据格式错误']);
    exit;
}

// 获取当前日期
$date = getTodayDate();

// 读取现有报告数据
$reports = getReports();

// 如果今日的数据不存在，创建一个空数组
if (!isset($reports[$date])) {
    $reports[$date] = [];
}

// 设置更新时间
$data['updatedAt'] = date('c');

// 保存或更新部门报告
$reports[$date][$data['department']] = $data;

// 保存报告数据
saveReports($reports);

// 读取历史记录
$history = getHistory();

// 添加历史记录
$history[] = [
    'date' => $date,
    'department' => $data['department'],
    'timestamp' => date('c'),
    'content' => [
        'todayWork' => $data['todayWork'],
        'problems' => $data['problems'],
        'tomorrowPlan' => $data['tomorrowPlan'],
        'data' => $data['data'],
        'remarks' => $data['remarks']
    ]
];

// 保存历史记录
saveHistory($history);

// 返回成功信息
echo json_encode(['success' => true]);
