<?php
// 导出Excel API - 使用简化的SimpleExcel类
require_once 'config.php';
require_once 'SimpleExcel.php';

// 设置为开发模式，显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 获取日期参数
$date = isset($_GET['date']) ? $_GET['date'] : getTodayDate();

// 记录日志到文件
file_put_contents(__DIR__ . '/export_log.txt', 
    date('Y-m-d H:i:s') . " - 导出Excel请求，日期参数: $date\n", 
    FILE_APPEND
);

// 根据日期获取短日期格式用于标题
$dateObj = new DateTime($date);
$shortDate = $dateObj->format('n.j');

// 读取报告数据
$reports = getReports();
$dateData = isset($reports[$date]) ? $reports[$date] : array();

// 记录找到的数据
file_put_contents(__DIR__ . '/export_log.txt', 
    date('Y-m-d H:i:s') . " - 找到的数据: " . json_encode(array_keys($dateData)) . "\n", 
    FILE_APPEND
);

// 部门列表（按指定顺序）
$departments = array(
    "景交车队", "餐饮", "商超", "酒店", "景点管理", "游客中心", "检票", "保洁"
);

// 创建Excel对象
$excel = new SimpleExcel();

// 设置标题和列
$excel->setTitle("运营管理部日报（{$shortDate}）")
      ->setColumns(array(
          array('name' => '二级部', 'width' => 100),
          array('name' => '今日主要工作', 'width' => 200),
          array('name' => '发现及需要解决的问题', 'width' => 200),
          array('name' => '明日工作计划', 'width' => 200),
          array('name' => '数据', 'width' => 150),
          array('name' => '备注', 'width' => 150),
      ));

// 添加部门数据行
foreach ($departments as $dept) {
    $deptData = isset($dateData[$dept]) ? $dateData[$dept] : null;
    
    $excel->addRow(array(
        $dept,
        $deptData ? $deptData['todayWork'] : '无',
        $deptData ? $deptData['problems'] : '无',
        $deptData ? $deptData['tomorrowPlan'] : '无',
        $deptData ? $deptData['data'] : '无',
        $deptData ? $deptData['remarks'] : '无'
    ));
}

// 输出Excel文件
$excel->output("运营管理部日报({$shortDate}).xls");
exit;
