<?php
// 配置文件

// 数据文件路径
define('DATA_DIR', __DIR__ . '/../data');
define('REPORTS_FILE', DATA_DIR . '/reports.json');
define('HISTORY_FILE', DATA_DIR . '/history.json');

// 创建数据目录（如果不存在）
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// 初始化JSON文件（如果不存在）
if (!file_exists(REPORTS_FILE)) {
    file_put_contents(REPORTS_FILE, json_encode([]));
}

if (!file_exists(HISTORY_FILE)) {
    file_put_contents(HISTORY_FILE, json_encode([]));
}

// 解决跨域问题（如果需要）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 如果是OPTIONS请求，直接返回成功
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit;
}

// 公共函数
function getTodayDate() {
    return date('Y-m-d');
}

function getShortDate() {
    return date('n.j');
}

// 读取报告数据
function getReports() {
    if (file_exists(REPORTS_FILE)) {
        $data = file_get_contents(REPORTS_FILE);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// 读取历史数据
function getHistory() {
    if (file_exists(HISTORY_FILE)) {
        $data = file_get_contents(HISTORY_FILE);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// 保存报告数据
function saveReports($data) {
    file_put_contents(REPORTS_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// 保存历史数据
function saveHistory($data) {
    file_put_contents(HISTORY_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
