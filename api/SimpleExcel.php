<?php
/**
 * 简单的Excel生成类
 * 不需要任何外部依赖，直接生成XML格式的Excel 2003文件
 * 兼容 PHP 5.4+
 */
class SimpleExcel {
    private $rows = array();
    private $title = '';
    private $columns = array();
    
    /**
     * 设置标题
     * @param string $title Excel表格标题
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    /**
     * 设置列定义
     * @param array $columns 列定义数组
     */
    public function setColumns($columns) {
        $this->columns = $columns;
        return $this;
    }
    
    /**
     * 添加数据行
     * @param array $row 行数据
     */
    public function addRow($row) {
        $this->rows[] = $row;
        return $this;
    }
    
    /**
     * 添加多行数据
     * @param array $rows 多行数据数组
     */
    public function addRows($rows) {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
        return $this;
    }
    
    /**
     * 输出Excel文件内容
     * @param string $filename 输出的文件名
     */
    public function output($filename = 'export.xls') {
        // 设置头信息
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // 创建XML输出
        echo '<?xml version="1.0"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        echo 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        echo 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        echo 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
        echo 'xmlns:html="http://www.w3.org/TR/REC-html40">';
        
        // 样式定义
        echo '<Styles>';
        echo '<Style ss:ID="Default" ss:Name="Normal">';
        echo '<Alignment ss:Vertical="Top" ss:WrapText="1"/>';
        echo '<Font ss:FontName="宋体" ss:Size="11"/>';
        echo '</Style>';
        echo '<Style ss:ID="Header">';
        echo '<Alignment ss:Horizontal="Center" ss:Vertical="Center"/>';
        echo '<Font ss:FontName="宋体" ss:Size="14" ss:Bold="1"/>';
        echo '</Style>';
        echo '<Style ss:ID="TableHeader">';
        echo '<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>';
        echo '<Font ss:FontName="宋体" ss:Size="11" ss:Bold="1"/>';
        echo '<Interior ss:Color="#EEEEEE" ss:Pattern="Solid"/>';
        echo '</Style>';
        echo '</Styles>';
        
        echo '<Worksheet ss:Name="Sheet1">';
        echo '<Table>';
        
        // 列定义
        foreach ($this->columns as $column) {
            echo '<Column ss:Width="' . (isset($column['width']) ? $column['width'] : 100) . '"/>';
        }
        
        // 标题行（如果设置了标题）
        if ($this->title) {
            echo '<Row ss:Height="30">';
            echo '<Cell ss:MergeAcross="' . (count($this->columns) - 1) . '" ss:StyleID="Header"><Data ss:Type="String">' . htmlspecialchars($this->title) . '</Data></Cell>';
            echo '</Row>';
        }
        
        // 表头行
        echo '<Row ss:Height="25">';
        foreach ($this->columns as $column) {
            echo '<Cell ss:StyleID="TableHeader"><Data ss:Type="String">' . htmlspecialchars($column['name']) . '</Data></Cell>';
        }
        echo '</Row>';
        
        // 数据行
        foreach ($this->rows as $row) {
            echo '<Row>';
            foreach ($row as $cell) {
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
            }
            echo '</Row>';
        }
        
        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
    }
}
