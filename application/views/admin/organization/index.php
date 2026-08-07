<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Chart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            padding: 30px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .header h1 {
            color: #2c3e50;
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 15px;
        }

        .chart {
            text-align: center;
            padding: 50px 30px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            min-height: 500px;
            overflow-x: auto;
        }

        .tree-node {
            display: inline-block;
            position: relative;
            text-align: center;
        }

        .node-container {
            display: inline-block;
            position: relative;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px 20px;
            width: 260px;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
            margin: 0 auto;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 12px 12px 0 0;
        }

        .blue {
            border: 3px solid #3498db;
            box-shadow: 0 6px 16px rgba(52,152,219,0.2);
        }

        .blue::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .green {
            border: 3px solid #2ecc71;
            box-shadow: 0 6px 16px rgba(46,204,113,0.2);
        }

        .green::before {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .orange {
            border: 3px solid #f39c12;
            box-shadow: 0 6px 16px rgba(243,156,18,0.2);
        }

        .orange::before {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .icon-user {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
        }

        .green .icon-user {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .orange .icon-user {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .name {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .title {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .dept {
            font-size: 12px;
            color: #95a5a6;
            font-style: italic;
            margin-top: 5px;
        }

        .btns {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #ecf0f1;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btns .btn {
            flex: 1;
            min-width: 70px;
            font-size: 12px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102,126,234,0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .btn-info:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(17,153,142,0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(238,9,121,0.4);
        }

        .children-wrapper {
            position: relative;
            padding-top: 60px;
        }

        .children-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            width: 2px;
            height: 30px;
            background: #bdc3c7;
            transform: translateX(-1px);
        }

        .children-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: nowrap;
            position: relative;
        }

        .children-container::before {
            content: '';
            position: absolute;
            top: -30px;
            left: 0;
            right: 0;
            height: 2px;
            background: #bdc3c7;
        }

        .children-container .tree-node {
            position: relative;
        }

        .children-container .tree-node::before {
            content: '';
            position: absolute;
            top: -30px;
            left: 50%;
            width: 2px;
            height: 30px;
            background: #bdc3c7;
            transform: translateX(-1px);
        }

        .children-container.single-child::before {
            display: none;
        }

        .children-container.single-child .tree-node::before {
            height: 30px;
        }

        .hide {
            display: none !important;
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .popup.active {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .popup-box {
            background: white;
            padding: 35px;
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.3s;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .popup-box h3 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .popup-btns {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .popup-btns .btn {
            flex: 1;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(17,153,142,0.3);
        }

        .btn-default {
            background: #ecf0f1;
            color: #7f8c8d;
        }

        .btn-default:hover {
            background: #bdc3c7;
        }

        @media (max-width: 1200px) {
            .children-container {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="header">
        <h1><i class="fa fa-sitemap"></i> Organization Chart</h1>
        <p>Click Toggle button to expand or collapse levels</p>
    </div>

    <div class="chart">
        <div class="tree-node">
            <div class="node-container">
                <div class="card blue">
                    <div class="icon-user">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="name">Nguyen Van A</div>
                    <div class="title">Chief Executive Officer</div>
                    <div class="dept">CEO Office</div>
                    <div class="btns">
                        <button class="btn btn-primary" onclick="openAdd('ceo-children', 1)">
                            <i class="fa fa-plus"></i> Add
                        </button>
                        <button class="btn btn-info" onclick="toggleChildren('ceo-children')">
                            <i class="fa fa-eye"></i> Toggle
                        </button>
                    </div>
                </div>
            </div>

            <div class="children-wrapper" id="ceo-children">
                <div class="children-container">
                    <div class="tree-node">
                        <div class="node-container">
                            <div class="card green">
                                <div class="icon-user">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="name">Tran Thi B</div>
                                <div class="title">Sales Director</div>
                                <div class="dept">Sales Department</div>
                                <div class="btns">
                                    <button class="btn btn-primary" onclick="openAdd('sales-children', 2)">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                    <button class="btn btn-info" onclick="toggleChildren('sales-children')">
                                        <i class="fa fa-eye"></i> Toggle
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteNode(this)">
                                        <i class="fa fa-trash"></i> Del
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="children-wrapper hide" id="sales-children">
                            <div class="children-container single-child">
                                <div class="tree-node">
                                    <div class="node-container">
                                        <div class="card orange">
                                            <div class="icon-user">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="name">Le Van C</div>
                                            <div class="title">Sales Manager</div>
                                            <div class="btns">
                                                <button class="btn btn-danger" onclick="deleteNode(this)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tree-node">
                                    <div class="node-container">
                                        <div class="card orange">
                                            <div class="icon-user">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="name">Le Van C</div>
                                            <div class="title">Sales Manager</div>
                                            <div class="btns">
                                                <button class="btn btn-danger" onclick="deleteNode(this)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tree-node">
                                    <div class="node-container">
                                        <div class="card orange">
                                            <div class="icon-user">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="name">Le Van C</div>
                                            <div class="title">Sales Manager</div>
                                            <div class="btns">
                                                <button class="btn btn-danger" onclick="deleteNode(this)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tree-node">
                        <div class="node-container">
                            <div class="card green">
                                <div class="icon-user">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="name">Hoang Van E</div>
                                <div class="title">Technology Director</div>
                                <div class="dept">Tech Department</div>
                                <div class="btns">
                                    <button class="btn btn-primary" onclick="openAdd('tech-children', 2)">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                    <button class="btn btn-info" onclick="toggleChildren('tech-children')">
                                        <i class="fa fa-eye"></i> Toggle
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteNode(this)">
                                        <i class="fa fa-trash"></i> Del
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="children-wrapper hide" id="tech-children">
                            <div class="children-container single-child">
                                <div class="tree-node">
                                    <div class="node-container">
                                        <div class="card orange">
                                            <div class="icon-user">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="name">Vu Thi F</div>
                                            <div class="title">Development Manager</div>
                                            <div class="btns">
                                                <button class="btn btn-danger" onclick="deleteNode(this)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tree-node">
                                    <div class="node-container">
                                        <div class="card orange">
                                            <div class="icon-user">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="name">Vu Thi F</div>
                                            <div class="title">Development Manager</div>
                                            <div class="btns">
                                                <button class="btn btn-danger" onclick="deleteNode(this)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tree-node">
                        <div class="node-container">
                            <div class="card green">
                                <div class="icon-user">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="name">Bui Van I</div>
                                <div class="title">HR Director</div>
                                <div class="dept">Human Resources</div>
                                <div class="btns">
                                    <button class="btn btn-primary" onclick="openAdd('hr-children', 2)">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                    <button class="btn btn-info" onclick="toggleChildren('hr-children')">
                                        <i class="fa fa-eye"></i> Toggle
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteNode(this)">
                                        <i class="fa fa-trash"></i> Del
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="children-wrapper hide" id="hr-children">
                            <div class="children-container single-child">
                                <div class="tree-node">
                                    <div class="node-container">
                                        <div class="card orange">
                                            <div class="icon-user">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="name">Cao Thi K</div>
                                            <div class="title">Recruitment Manager</div>
                                            <div class="btns">
                                                <button class="btn btn-danger" onclick="deleteNode(this)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tree-node">
                        <div class="node-container">
                            <div class="card green">
                                <div class="icon-user">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="name">Bui Van I</div>
                                <div class="title">HR Director</div>
                                <div class="dept">Human Resources</div>
                                <div class="btns">
                                    <button class="btn btn-primary" onclick="openAdd('hr-children', 2)">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                    <button class="btn btn-info" onclick="toggleChildren('hr-children')">
                                        <i class="fa fa-eye"></i> Toggle
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteNode(this)">
                                        <i class="fa fa-trash"></i> Del
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="children-wrapper hide" id="hr-children">
                            <div class="children-container single-child">
                                <div class="tree-node">
                                    <div class="node-container">
                                        <div class="card orange">
                                            <div class="icon-user">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div class="name">Cao Thi K</div>
                                            <div class="title">Recruitment Manager</div>
                                            <div class="btns">
                                                <button class="btn btn-danger" onclick="deleteNode(this)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="popup" id="addPopup">
    <div class="popup-box">
        <h3><i class="fa fa-plus-circle"></i> Add New Employee</h3>
        <div class="form-group">
            <label><i class="fa fa-user"></i> Full Name:</label>
            <input type="text" class="form-control" id="empName" placeholder="Enter full name">
        </div>
        <div class="form-group">
            <label><i class="fa fa-briefcase"></i> Position:</label>
            <input type="text" class="form-control" id="empTitle" placeholder="Enter position">
        </div>
        <div class="form-group">
            <label><i class="fa fa-building"></i> Department:</label>
            <input type="text" class="form-control" id="empDept" placeholder="Enter department (optional)">
        </div>
        <div class="popup-btns">
            <button class="btn btn-success" onclick="addEmployee()">
                <i class="fa fa-check"></i> Add Employee
            </button>
            <button class="btn btn-default" onclick="closeAdd()">
                <i class="fa fa-times"></i> Cancel
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    var currentParent = '';
    var currentLevel = 0;

    function toggleChildren(id) {
        var element = document.getElementById(id);
        if (element) {
            element.classList.toggle('hide');
            updateConnectorLines(element);
        }
    }

    function updateConnectorLines(wrapper) {
        var container = wrapper.querySelector('.children-container');
        if (container) {
            var childCount = container.querySelectorAll('.tree-node').length;
            if (childCount === 1) {
                container.classList.add('single-child');
            } else {
                container.classList.remove('single-child');
            }
        }
    }

    function openAdd(parent, level) {
        currentParent = parent;
        currentLevel = level;
        document.getElementById('addPopup').classList.add('active');
        document.getElementById('empName').value = '';
        document.getElementById('empTitle').value = '';
        document.getElementById('empDept').value = '';
        document.getElementById('empName').focus();
    }

    function closeAdd() {
        document.getElementById('addPopup').classList.remove('active');
    }

    function addEmployee() {
        var name = document.getElementById('empName').value.trim();
        var title = document.getElementById('empTitle').value.trim();
        var dept = document.getElementById('empDept').value.trim();

        if (!name || !title) {
            alert('Please enter name and position');
            return;
        }

        var colorClass = currentLevel === 1 ? 'green' : 'orange';
        var nodeId = 'node-' + Date.now();

        var htmlContent = '<div class="tree-node">';
        htmlContent += '<div class="node-container">';
        htmlContent += '<div class="card ' + colorClass + '">';
        htmlContent += '<div class="icon-user"><i class="fa fa-user"></i></div>';
        htmlContent += '<div class="name">' + name + '</div>';
        htmlContent += '<div class="title">' + title + '</div>';
        if (dept) {
            htmlContent += '<div class="dept">' + dept + '</div>';
        }
        htmlContent += '<div class="btns">';
        if (currentLevel === 1) {
            htmlContent += '<button class="btn btn-primary" onclick="openAdd(\'' + nodeId + '-children\', 2)"><i class="fa fa-plus"></i> Add</button>';
            htmlContent += '<button class="btn btn-info" onclick="toggleChildren(\'' + nodeId + '-children\')"><i class="fa fa-eye"></i> Toggle</button>';
        }
        htmlContent += '<button class="btn btn-danger" onclick="deleteNode(this)"><i class="fa fa-trash"></i> Delete</button>';
        htmlContent += '</div></div></div>';

        if (currentLevel === 1) {
            htmlContent += '<div class="children-wrapper hide" id="' + nodeId + '-children">';
            htmlContent += '<div class="children-container single-child"></div>';
            htmlContent += '</div>';
        }

        htmlContent += '</div>';

        var parentElement = document.getElementById(currentParent);
        if (parentElement) {
            var container = parentElement.querySelector('.children-container');
            if (container) {
                container.insertAdjacentHTML('beforeend', htmlContent);
                updateConnectorLines(parentElement);
            }
            parentElement.classList.remove('hide');
        }

        closeAdd();
    }

    function deleteNode(button) {
        if (confirm('Are you sure you want to delete this employee?')) {
            var treeNode = button.closest('.tree-node');
            if (treeNode) {
                var parentContainer = treeNode.parentElement;
                treeNode.style.opacity = '0';
                treeNode.style.transform = 'scale(0.8)';
                setTimeout(function() {
                    treeNode.remove();
                    if (parentContainer) {
                        var wrapper = parentContainer.closest('.children-wrapper');
                        if (wrapper) {
                            updateConnectorLines(wrapper);
                        }
                    }
                }, 300);
            }
        }
    }

    document.getElementById('addPopup').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAdd();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var allWrappers = document.querySelectorAll('.children-wrapper');
        allWrappers.forEach(function(wrapper) {
            updateConnectorLines(wrapper);
        });
    });
</script>
</body>
</html>