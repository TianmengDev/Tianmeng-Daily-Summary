document.addEventListener('DOMContentLoaded', function() {
    // 设置当前日期
    const currentDate = new Date();
    const dateOptions = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
    document.getElementById('currentDate').textContent = currentDate.toLocaleDateString('zh-CN', dateOptions);
    
    // 存储部门列表
    const departments = [
        "景交车队", "餐饮", "商超", "酒店", "景点管理", "游客中心", "检票", "保洁"
    ];
    
    // 管理员密码
    const adminPassword = "tianmeng123"; // 可以更改为任何你想要的密码
    
    // 获取今日日期的字符串格式 (YYYY-MM-DD)
    function getTodayDateString() {
        const today = new Date();
        return `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    }
    
    // 获取今日日期的短格式 (MM.DD)
    function getTodayShortDateString() {
        const today = new Date();
        return `${today.getMonth() + 1}.${today.getDate()}`;
    }
    
    // 显示加载指示器
    function showLoading() {
        document.getElementById('loadingIndicator').classList.add('active');
    }
    
    // 隐藏加载指示器
    function hideLoading() {
        document.getElementById('loadingIndicator').classList.remove('active');
    }
    
    // 表单提交处理
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // 获取表单数据
        const department = document.getElementById('department').value;
        let todayWork = document.getElementById('todayWork').value.trim();
        let problems = document.getElementById('problems').value.trim();
        let tomorrowPlan = document.getElementById('tomorrowPlan').value.trim();
        let data = document.getElementById('data').value.trim();
        let remarks = document.getElementById('remarks').value.trim();
        
        // 检查必选部门
        if (!department) {
            alert('请选择部门！');
            return;
        }
        
        // 检查是否至少填写了一个内容
        if (!todayWork && !problems && !tomorrowPlan && !data && !remarks) {
            alert('请至少填写一项内容！');
            return;
        }
        
        // 空字段自动填充"无"
        todayWork = todayWork || "无";
        problems = problems || "无";
        tomorrowPlan = tomorrowPlan || "无";
        data = data || "无";
        remarks = remarks || "无";
        
        // 准备要发送的数据
        const reportData = {
            department,
            todayWork,
            problems,
            tomorrowPlan,
            data,
            remarks
        };
        
        // 显示加载指示器
        showLoading();
        
        // 发送数据到服务器
        fetch('api/save_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(reportData)
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                alert(`${department}部门日报提交成功！`);
                document.getElementById('reportForm').reset();
            } else {
                alert(`提交失败：${data.message}`);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('提交失败，请稍后重试或联系管理员。');
        });
    });
    
    // 管理员登录模态框
    const adminModal = document.getElementById('adminModal');
    const adminLoginBtn = document.getElementById('adminLogin');
    const closeAdminModal = document.getElementsByClassName('close')[0];
    const loginBtn = document.getElementById('loginBtn');
    
    adminLoginBtn.addEventListener('click', function() {
        adminModal.style.display = 'block';
    });
    
    closeAdminModal.addEventListener('click', function() {
        adminModal.style.display = 'none';
    });
    
    loginBtn.addEventListener('click', function() {
        const password = document.getElementById('adminPassword').value;
        if (password === adminPassword) {
            adminModal.style.display = 'none';
            showAdminPanel();
        } else {
            alert('密码错误，请重试！');
        }
    });
    
// 管理员面板
    const adminPanel = document.getElementById('adminPanel');
    const closeAdminBtn = document.getElementById('closeAdmin');
    const exportExcelBtn = document.getElementById('exportExcel');
    const exportJSONBtn = document.getElementById('exportJSON');
    const reportDateInput = document.getElementById('reportDate');
    const viewByDateBtn = document.getElementById('viewByDate');
    const viewTodayBtn = document.getElementById('viewToday');
    const currentViewDateSpan = document.getElementById('currentViewDate');
    
    // 设置日期输入框默认值为今天
    function setDefaultDate() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        reportDateInput.value = `${yyyy}-${mm}-${dd}`;
    }
    
    function showAdminPanel() {
        adminPanel.style.display = 'block';
        setDefaultDate();
        // 初始查看今天的数据
        updateViewDate('今日');
        updateDepartmentStatus(getTodayDateString());
        updateSubmissionHistory(getTodayDateString());
    }
    
    // 更新当前查看的日期显示
    function updateViewDate(dateText) {
        currentViewDateSpan.textContent = dateText;
    }
    
    closeAdminBtn.addEventListener('click', function() {
        adminPanel.style.display = 'none';
    });
    
    // 按选定日期查看
    viewByDateBtn.addEventListener('click', function() {
        const selectedDate = reportDateInput.value;
        if (!selectedDate) {
            alert('请选择日期');
            return;
        }
        
        console.log('用户选择的日期:', selectedDate);
        
        // 确保日期格式正确 (YYYY-MM-DD)
        const formattedDate = selectedDate.split('T')[0]; // 处理可能的时间部分
        console.log('格式化后的日期:', formattedDate);
        
        const dateObj = new Date(formattedDate);
        const displayDate = dateObj.toLocaleDateString('zh-CN', { year: 'numeric', month: 'numeric', day: 'numeric' });
        
        updateViewDate(displayDate);
        updateDepartmentStatus(formattedDate);
        updateSubmissionHistory(formattedDate);
    });
    
    // 查看今日数据
    viewTodayBtn.addEventListener('click', function() {
        setDefaultDate();
        updateViewDate('今日');
        updateDepartmentStatus(getTodayDateString());
        updateSubmissionHistory(getTodayDateString());
    });
    
    // 更新部门提交状态 - 修改为接受日期参数
    function updateDepartmentStatus(date) {
        const departmentStatus = document.getElementById('departmentStatus');
        departmentStatus.innerHTML = '<div class="loading">加载中...</div>';
        
        console.log('正在更新部门状态，日期参数:', date);
        
        fetch('api/get_reports.php?date=' + date)
        .then(response => response.json())
        .then(data => {
            console.log('部门状态API返回数据:', data);
            departmentStatus.innerHTML = '';
            const dateData = data[date] || {};
            
            if (Object.keys(dateData).length === 0) {
                departmentStatus.innerHTML = '<div class="no-data">该日期没有提交记录</div>';
                return;
            }
            
            departments.forEach(dept => {
                const statusItem = document.createElement('div');
                statusItem.className = 'status-item';
                
                const deptName = document.createElement('span');
                deptName.textContent = dept;
                
                const status = document.createElement('span');
                if (dateData[dept]) {
                    status.textContent = '已提交';
                    status.className = 'submitted';
                    const updatedTime = new Date(dateData[dept].updatedAt).toLocaleTimeString('zh-CN');
                    status.textContent += ` (${updatedTime})`;
                } else {
                    status.textContent = '未提交';
                    status.className = 'not-submitted';
                }
                
                statusItem.appendChild(deptName);
                statusItem.appendChild(status);
                departmentStatus.appendChild(statusItem);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            departmentStatus.innerHTML = '<div class="error">加载失败，请刷新重试</div>';
        });
    }
    
    // 更新提交历史记录 - 修改为接受日期参数
    function updateSubmissionHistory(date) {
        const submissionHistoryEl = document.getElementById('submissionHistory');
        submissionHistoryEl.innerHTML = '<div class="loading">加载中...</div>';
        
        console.log('正在更新提交历史记录，日期参数:', date);
        
        fetch('api/get_history.php?date=' + date)
        .then(response => response.json())
        .then(history => {
            console.log('历史记录API返回数据:', history);
            submissionHistoryEl.innerHTML = '';
            
            // 判断返回的数据是对象而不是数组的情况
            let filteredHistory = [];
            
            if (Array.isArray(history)) {
                // 如果是数组，按原来的方式处理
                filteredHistory = history
                    .filter(item => item.date === date)
                    .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            } else if (typeof history === 'object' && history !== null) {
                // 如果是对象，直接使用对应日期的数据
                const dateData = history[date] || {};
                
                // 将对象中的每个部门数据转换为历史记录格式的数组
                for (const dept in dateData) {
                    if (dateData.hasOwnProperty(dept)) {
                        filteredHistory.push({
                            department: dept,
                            date: date,
                            timestamp: dateData[dept].updatedAt || new Date().toISOString(),
                            content: {
                                todayWork: dateData[dept].todayWork || '无',
                                problems: dateData[dept].problems || '无',
                                tomorrowPlan: dateData[dept].tomorrowPlan || '无',
                                data: dateData[dept].data || '无',
                                remarks: dateData[dept].remarks || '无'
                            }
                        });
                    }
                }
                
                // 按时间排序
                filteredHistory.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            }
            
            console.log('过滤后的历史记录:', filteredHistory);
            
            if (filteredHistory.length === 0) {
                const noHistoryItem = document.createElement('div');
                noHistoryItem.className = 'history-item';
                noHistoryItem.textContent = '该日期暂无提交记录';
                submissionHistoryEl.appendChild(noHistoryItem);
                return;
            }
            
            filteredHistory.forEach(item => {
                const historyItem = document.createElement('div');
                historyItem.className = 'history-item';
                
                const header = document.createElement('div');
                header.style.marginBottom = '10px';
                header.innerHTML = `<strong>${item.department}</strong> - ${new Date(item.timestamp).toLocaleString('zh-CN')}`;
                
                const content = document.createElement('div');
                content.innerHTML = `
                    <p><strong>今日主要工作:</strong> ${item.content.todayWork}</p>
                    <p><strong>发现及需要解决的问题:</strong> ${item.content.problems}</p>
                    <p><strong>明日工作计划:</strong> ${item.content.tomorrowPlan}</p>
                    <p><strong>数据:</strong> ${item.content.data}</p>
                    <p><strong>备注:</strong> ${item.content.remarks || '无'}</p>
                `;
                
                historyItem.appendChild(header);
                historyItem.appendChild(content);
                submissionHistoryEl.appendChild(historyItem);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            submissionHistoryEl.innerHTML = '<div class="error">加载失败，请刷新重试</div>';
        });
    }
    
    // 修改导出按钮事件 - 使用当前选择的日期
    exportExcelBtn.addEventListener('click', function() {
        let selectedDate = reportDateInput.value || getTodayDateString();
        
        // 确保日期格式正确 (YYYY-MM-DD)
        selectedDate = selectedDate.split('T')[0]; // 处理可能的时间部分
        
        console.log('导出Excel，使用日期:', selectedDate);
        
        // 使用新窗口打开导出链接，以便看到实际请求的URL
        const exportUrl = `api/export_excel.php?date=${selectedDate}`;
        console.log('导出Excel URL:', exportUrl);
        
        window.open(exportUrl, '_blank');
    });
    
    // 修改导出JSON按钮事件 - 使用当前选择的日期
    exportJSONBtn.addEventListener('click', function() {
        let selectedDate = reportDateInput.value || getTodayDateString();
        
        // 确保日期格式正确 (YYYY-MM-DD)
        selectedDate = selectedDate.split('T')[0]; // 处理可能的时间部分
        
        console.log('导出JSON，使用日期:', selectedDate);
        
        fetch(`api/get_reports.php?date=${selectedDate}`)
        .then(response => response.json())
        .then(data => {
            console.log('JSON导出数据:', data);
            const selectedData = data[selectedDate] || {};
            
            // 创建并下载JSON文件
            const dataStr = JSON.stringify(selectedData, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(dataBlob);
            
            // 格式化日期为短格式，用于文件名
            const dateObj = new Date(selectedDate);
            const shortDate = `${dateObj.getMonth() + 1}.${dateObj.getDate()}`;
            
            const a = document.createElement('a');
            a.href = url;
            a.download = `运营管理部日报(${shortDate}).json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('导出失败，请稍后重试。');
        });
    });
    
    // 点击模态框外部关闭模态框
    window.addEventListener('click', function(event) {
        if (event.target === adminModal) {
            adminModal.style.display = 'none';
        }
    });
});
