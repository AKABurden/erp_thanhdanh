<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SOP Vòng Đời Nhân Sự & Quản Trị Hệ Thống FOSO</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Tăng font size lên một chút để các lớp Tailwind rem-based lớn hơn ~5% */
    html {
        font-size: 105%;
        /* tăng nhẹ, ảnh hưởng đến tất cả lớp dùng rem (text-sm, text-base, etc.) */
    }

    /* Fallback cho phần tử dùng px trực tiếp (tăng rất nhẹ) */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f3f4f6;
        color: #1f2937;
        font-size: 15.8px;
        /* nhỏ gọn: tăng 1-2px so với mặc định */
    }

    .chart-container {
        position: relative;
        width: 100%;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        height: 300px;
        max-height: 400px;
    }

    @media (min-width: 768px) {
        .chart-container {
            height: 350px;
        }
    }

    .tab-btn.active {
        border-bottom: 2px solid #2563eb;
        color: #2563eb;
        font-weight: 600;
    }

    .tab-btn {
        color: #6b7280;
        transition: all 0.3s;
    }

    .tab-btn:hover {
        color: #374151;
    }

    /* Custom scrollbar for tables */
    .custom-scroll::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    .custom-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    /* Animation classes */
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .collapse {
        visibility: unset !important;
    }
</style>
<div id="wrapper">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class=" mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-8">
                <div class="flex items-center">
                    <a href="https://03121ftd.fmrp.vn/admin/dashboard"
                        class="hide text-2xl font-bold text-blue-800 tracking-tight">FOSO <span
                            class="text-slate-500 font-light">System</span></a>
                    <span
                        class="ml-4 px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full  sm:inline-block">SOP
                        Vòng Đời Nhân Sự v1.2</span>
                </div>
                <div class="text-sm text-gray-500  md:block">
                    Hệ thống quản trị quy trình & dữ liệu nhân sự tập trung
                </div>
            </div>
            <!-- Navigation Tabs -->
            <nav class="hide flex space-x-8 overflow-x-auto custom-scroll -mb-px" aria-label="Tabs">
                <button onclick="switchTab('overview')" id="tab-overview"
                    class="tab-btn active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Tổng Quan Quy
                    Trình</button>
                <button onclick="switchTab('recruitment')" id="tab-recruitment"
                    class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm">Module
                    Tuyển Dụng</button>
                <button onclick="switchTab('software')" id="tab-software"
                    class="hide tab-btn whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm">Yêu
                    Cầu Phần Mềm</button>
                <button onclick="switchTab('simulation')" id="tab-simulation"
                    class="hide tab-btn whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm">Giả
                    Lập Gate & 3P</button>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow bg-slate-50 py-8">
        <div class=" mx-auto px-4 sm:px-6 lg:px-8">

            <!-- SECTION 1: OVERVIEW (Tổng Quan) -->
            <div id="view-overview" class="fade-in block">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Vòng Đời Nhân Sự (The Lifecycle)</h2>
                    <p class="text-slate-600 mb-6">
                        Tổng quan hành trình từ khi phát sinh nhu cầu tuyển dụng đến khi nhân sự trở thành chính thức.
                        Quy trình được kiểm soát chặt chẽ bởi các "Gate" (Cổng chặn) để đảm bảo dữ liệu sạch và tuân thủ
                        pháp lý.
                    </p>

                    <!-- Interactive Flowchart Steps -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        <div onclick="showStepDetail('phase1')"
                            class="cursor-pointer bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-blue-600 uppercase">Giai đoạn 1</span>
                                <span class="text-xl">📋</span>
                            </div>
                            <h3 class="font-bold text-slate-800">Tuyển Dụng & Sàng Lọc</h3>
                            <p class="text-xs text-slate-500 mt-1">YCTD → Lọc CV → Phỏng vấn</p>
                            <div class="mt-2 text-xs font-mono text-blue-700 bg-white px-2 py-1 rounded inline-block">
                                Gate 1 & 2</div>
                        </div>

                        <div onclick="showStepDetail('phase2')"
                            class="cursor-pointer bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-amber-600 uppercase">Giai đoạn 2</span>
                                <span class="text-xl">🛡️</span>
                            </div>
                            <h3 class="font-bold text-slate-800">Offer & Onboarding</h3>
                            <p class="text-xs text-slate-500 mt-1">Thỏa thuận → Check hồ sơ → Sinh mã NV</p>
                            <div class="mt-2 text-xs font-mono text-amber-700 bg-white px-2 py-1 rounded inline-block">
                                Gate 3 (Onsite)</div>
                        </div>

                        <div onclick="showStepDetail('phase3')"
                            class="cursor-pointer bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-indigo-600 uppercase">Giai đoạn 3</span>
                                <span class="text-xl">🌱</span>
                            </div>
                            <h3 class="font-bold text-slate-800">Thử Việc & Đánh Giá</h3>
                            <p class="text-xs text-slate-500 mt-1">Lương 80% → KPI Thử việc → Ký Chính thức</p>
                            <div class="mt-2 text-xs font-mono text-indigo-700 bg-white px-2 py-1 rounded inline-block">
                                Gate 4</div>
                        </div>

                        <div onclick="showStepDetail('phase4')"
                            class="cursor-pointer bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg p-4 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-emerald-600 uppercase">Giai đoạn 4</span>
                                <span class="text-xl">💎</span>
                            </div>
                            <h3 class="font-bold text-slate-800">Chính Thức & 3P</h3>
                            <p class="text-xs text-slate-500 mt-1">Lương 3P → Tái ký HĐ → Thăng tiến</p>
                            <div
                                class="mt-2 text-xs font-mono text-emerald-700 bg-white px-2 py-1 rounded inline-block">
                                Gate Định Kỳ</div>
                        </div>
                    </div>

                    <!-- Dynamic Detail View -->
                    <div id="step-detail-panel" class="bg-slate-50 rounded-lg p-6 border border-slate-200 hidden">
                        <h3 id="step-title" class="text-lg font-bold text-slate-800 mb-2">Chi tiết giai đoạn</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-sm text-slate-600 mb-2">Quy trình (Steps):</h4>
                                <ul id="step-list" class="space-y-2 text-sm text-slate-700"></ul>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm text-slate-600 mb-2">Yêu cầu hệ thống (System Rules):
                                </h4>
                                <ul id="rule-list"
                                    class="space-y-2 text-sm text-red-600 bg-red-50 p-3 rounded border border-red-100">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-4">Pipeline Tuyển Dụng (Quy Trình S2 - S8)</h2>
                        <p class="text-slate-600 mb-6">Mô hình hóa luồng ứng viên đi qua hệ thống. Các bước S3, S4, S7
                            đóng
                            vai trò là "Gate" (Cổng lọc).</p>

                        <!-- Pipeline Visual -->
                        <div
                            class="flex flex-col md:flex-row justify-between items-center bg-slate-100 p-4 rounded-lg overflow-x-auto gap-4 mb-8">
                            <div class="flex-1 text-center min-w-[100px]">
                                <div class="bg-slate-300 text-slate-600 font-bold py-2 px-4 rounded mb-2">S2</div>
                                <div class="text-xs font-semibold">Ứng Tuyển</div>
                            </div>
                            <div class="text-slate-400">→</div>
                            <div class="flex-1 text-center min-w-[100px]">
                                <div
                                    class="bg-blue-200 text-blue-800 font-bold py-2 px-4 rounded mb-2 border-2 border-blue-400">
                                    S3 (Gate 1)</div>
                                <div class="text-xs font-semibold">Lọc CV</div>
                                <div class="text-[10px] text-slate-500">Check JD</div>
                            </div>
                            <div class="text-slate-400">→</div>
                            <div class="flex-1 text-center min-w-[100px]">
                                <div
                                    class="bg-blue-200 text-blue-800 font-bold py-2 px-4 rounded mb-2 border-2 border-blue-400">
                                    S4 (Gate 2)</div>
                                <div class="text-xs font-semibold">Phỏng Vấn</div>
                                <div class="text-[10px] text-slate-500">Scorecard</div>
                            </div>
                            <div class="text-slate-400">→</div>
                            <div class="flex-1 text-center min-w-[100px]">
                                <div class="bg-slate-300 text-slate-600 font-bold py-2 px-4 rounded mb-2">S5</div>
                                <div class="text-xs font-semibold">Offer</div>
                            </div>
                            <div class="text-slate-400">→</div>
                            <div class="flex-1 text-center min-w-[100px]">
                                <div class="bg-slate-300 text-slate-600 font-bold py-2 px-4 rounded mb-2">S6</div>
                                <div class="text-xs font-semibold">Nộp Hồ Sơ</div>
                            </div>
                            <div class="text-slate-400">→</div>
                            <div class="flex-1 text-center min-w-[100px]">
                                <div
                                    class="bg-red-200 text-red-800 font-bold py-2 px-4 rounded mb-2 border-2 border-red-500">
                                    S7 (Gate 3)</div>
                                <div class="text-xs font-semibold">Đối Chiếu</div>
                                <div class="text-[10px] text-slate-500">Onsite Audit</div>
                            </div>
                            <div class="text-slate-400">→</div>
                            <div class="flex-1 text-center min-w-[100px]">
                                <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-2">S8</div>
                                <div class="text-xs font-semibold">Mã NV</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- KPI Chart Section -->
                <div class="hide grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Mô hình Lỗi KPI (Error Impact)</h3>
                        <p class="text-sm text-slate-500 mb-4">Các lỗi vận hành ảnh hưởng trực tiếp đến KPI và Lương P3.
                        </p>
                        <div class="chart-container">
                            <canvas id="kpiChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Các Cột Mốc Quan Trọng (Milestones)</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                                    S0</div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-bold text-slate-800">Tạo YCTD</h4>
                                    <p class="text-xs text-slate-500">Phải có Mã JD & Ngân sách P1.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-sm">
                                    S7</div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-bold text-slate-800">Đối chiếu Hồ sơ (Onsite)</h4>
                                    <p class="text-xs text-slate-500">HR check 100% giấy tờ gốc. Gian dối = Loại.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    S8</div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-bold text-slate-800">Sinh Mã Nhân Viên</h4>
                                    <p class="text-xs text-slate-500">Chỉ sinh khi đủ hồ sơ + duyệt lương.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-sm">
                                    Tx</div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-bold text-slate-800">Tái ký Hợp đồng</h4>
                                    <p class="text-xs text-slate-500">T-30 ngày. Luôn đi kèm phụ lục lương.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: RECRUITMENT MODULE (Module Tuyển Dụng) -->
            <div id="view-recruitment" class="hidden fade-in">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-4">Pipeline Tuyển Dụng (Quy Trình S2 - S8)</h2>
                    <p class="text-slate-600 mb-6">Mô hình hóa luồng ứng viên đi qua hệ thống. Các bước S3, S4, S7 đóng
                        vai trò là "Gate" (Cổng lọc).</p>

                    <!-- Pipeline Visual -->
                    <div
                        class="flex flex-col md:flex-row justify-between items-center bg-slate-100 p-4 rounded-lg overflow-x-auto gap-4 mb-8">
                        <div class="flex-1 text-center min-w-[100px]">
                            <div class="bg-slate-300 text-slate-600 font-bold py-2 px-4 rounded mb-2">S2</div>
                            <div class="text-xs font-semibold">Ứng Tuyển</div>
                        </div>
                        <div class="text-slate-400">→</div>
                        <div class="flex-1 text-center min-w-[100px]">
                            <div
                                class="bg-blue-200 text-blue-800 font-bold py-2 px-4 rounded mb-2 border-2 border-blue-400">
                                S3 (Gate 1)</div>
                            <div class="text-xs font-semibold">Lọc CV</div>
                            <div class="text-[10px] text-slate-500">Check JD</div>
                        </div>
                        <div class="text-slate-400">→</div>
                        <div class="flex-1 text-center min-w-[100px]">
                            <div
                                class="bg-blue-200 text-blue-800 font-bold py-2 px-4 rounded mb-2 border-2 border-blue-400">
                                S4 (Gate 2)</div>
                            <div class="text-xs font-semibold">Phỏng Vấn</div>
                            <div class="text-[10px] text-slate-500">Scorecard</div>
                        </div>
                        <div class="text-slate-400">→</div>
                        <div class="flex-1 text-center min-w-[100px]">
                            <div class="bg-slate-300 text-slate-600 font-bold py-2 px-4 rounded mb-2">S5</div>
                            <div class="text-xs font-semibold">Offer</div>
                        </div>
                        <div class="text-slate-400">→</div>
                        <div class="flex-1 text-center min-w-[100px]">
                            <div class="bg-slate-300 text-slate-600 font-bold py-2 px-4 rounded mb-2">S6</div>
                            <div class="text-xs font-semibold">Nộp Hồ Sơ</div>
                        </div>
                        <div class="text-slate-400">→</div>
                        <div class="flex-1 text-center min-w-[100px]">
                            <div
                                class="bg-red-200 text-red-800 font-bold py-2 px-4 rounded mb-2 border-2 border-red-500">
                                S7 (Gate 3)</div>
                            <div class="text-xs font-semibold">Đối Chiếu</div>
                            <div class="text-[10px] text-slate-500">Onsite Audit</div>
                        </div>
                        <div class="text-slate-400">→</div>
                        <div class="flex-1 text-center min-w-[100px]">
                            <div class="bg-green-500 text-white font-bold py-2 px-4 rounded mb-2">S8</div>
                            <div class="text-xs font-semibold">Mã NV</div>
                        </div>
                    </div>

                    <!-- Pipeline Stats Chart -->
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Hiệu Suất Phễu Tuyển Dụng (Minh họa)</h3>
                    <div class="chart-container">
                        <canvas id="pipelineChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: SOFTWARE SPECS (Yêu Cầu Phần Mềm) -->
            <div id="view-software" class="hidden fade-in">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Yêu Cầu Phần Mềm: 05 Phiếu Điện Tử</h2>
                    <p class="text-slate-600 mb-6">
                        Để vận hành module tuyển dụng theo chuẩn "Khóa hệ" (Imprint), phần mềm FOSO cần xây dựng tối
                        thiểu 5 mẫu phiếu (Digital Forms) dưới đây. Các logic kiểm soát (Validation) là bắt buộc.
                    </p>

                    <div class="overflow-x-auto custom-scroll">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Tên Phiếu (Form)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Người Dùng</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                        Dữ liệu chính (Input)</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider text-red-600">
                                        Logic Khóa Hệ (Rules)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">01</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">YCTD
                                        (Hiring Request)</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">Trưởng BP</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <ul class="list-disc pl-4 text-xs">
                                            <li>Mã JD (Chọn từ list)</li>
                                            <li>Số lượng & Deadline</li>
                                            <li>Loại ngân sách</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600 font-medium text-xs bg-red-50">
                                        🚫 Không chọn Mã JD → Không cho Lưu.<br>
                                        🚫 Ngân sách P1 tự động load, không được sửa.
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">02</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">Phiếu
                                        Đánh Giá PV</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">Người PV</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <ul class="list-disc pl-4 text-xs">
                                            <li>Điểm năng lực (1-5)</li>
                                            <li>Nhận xét chuyên môn</li>
                                            <li>Kết quả: Đạt/Không đạt</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600 font-medium text-xs bg-red-50">
                                        🚫 Phải điền đủ điểm các mục bắt buộc.<br>
                                        🚫 "Không đạt" → Chặn không cho qua bước Offer.
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">03</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">Phiếu Đề
                                        Xuất Offer</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">HR Recruiter</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <ul class="list-disc pl-4 text-xs">
                                            <li>Lương P1 (Vùng)</li>
                                            <li>Lương P2 (Deal)</li>
                                            <li>Ngày nhận việc</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600 font-medium text-xs bg-red-50">
                                        🚫 Tổng P1+P2 không được vượt quá khung duyệt ở YCTD.
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">04</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">
                                        E-Profile Ứng Viên</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">Ứng Viên</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <ul class="list-disc pl-4 text-xs">
                                            <li>Thông tin cá nhân, MST, NH</li>
                                            <li>Upload ảnh bằng cấp</li>
                                            <li>Người phụ thuộc</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600 font-medium text-xs bg-red-50">
                                        🚫 Validate định dạng Email, SĐT, Ngày sinh.<br>
                                        🚫 Các trường (*) không được để trống.
                                    </td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">05</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">
                                        Checklist Hồ Sơ (Onsite)</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">HR Admin</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <ul class="list-disc pl-4 text-xs">
                                            <li>Checkbox: "Đã đối chiếu gốc"</li>
                                            <li>Trạng thái: Đạt/Thiếu/Gian dối</li>
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600 font-medium text-xs bg-red-50">
                                        🚫 <strong>CHẶN NÚT SINH MÃ NV</strong> nếu Checklist chưa tích đủ 100%.<br>
                                        ⚠️ Gian dối = Blacklist ngay.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: SIMULATION (Giả Lập) -->
            <div id="view-simulation" class="hidden fade-in">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Simulation 1: Gate 3 - Onsite Check -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-slate-800">Giả Lập Gate 3: Kiểm Soát Hồ Sơ</h2>
                            <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded">S7 → S8</span>
                        </div>
                        <p class="text-sm text-slate-600 mb-6">Thử đóng vai HR tiếp nhận nhân viên mới. Hãy tích vào các
                            mục đã đối chiếu.</p>

                        <div class="space-y-3 bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
                            <h4 class="font-bold text-sm text-slate-700 border-b pb-2 mb-2">Checklist Hồ Sơ Bắt Buộc:
                            </h4>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="chk-cccd" class="form-checkbox h-5 w-5 text-blue-600"
                                    onchange="updateGateStatus()">
                                <span class="text-sm text-slate-700">1. CCCD/CMND (Gốc + Photo)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="chk-degree" class="form-checkbox h-5 w-5 text-blue-600"
                                    onchange="updateGateStatus()">
                                <span class="text-sm text-slate-700">2. Bằng cấp (Bản sao công chứng)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="chk-health" class="form-checkbox h-5 w-5 text-blue-600"
                                    onchange="updateGateStatus()">
                                <span class="text-sm text-slate-700">3. Giấy khám sức khỏe (< 6 tháng)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="chk-bank" class="form-checkbox h-5 w-5 text-blue-600"
                                    onchange="updateGateStatus()">
                                <span class="text-sm text-slate-700">4. Thông tin tài khoản ngân hàng</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="chk-eform" class="form-checkbox h-5 w-5 text-blue-600"
                                    onchange="updateGateStatus()">
                                <span class="text-sm text-slate-700">5. Ứng viên đã điền đủ E-Form Online</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-lg border-2 border-dashed"
                            id="gate-result-box">
                            <div>
                                <h3 class="font-bold text-slate-400" id="gate-status-text">Đang chờ kiểm tra...</h3>
                                <p class="text-xs text-slate-400" id="gate-msg">Vui lòng hoàn tất đối chiếu.</p>
                            </div>
                            <button id="btn-create-id"
                                class="px-4 py-2 bg-slate-300 text-white font-bold rounded cursor-not-allowed transition-colors"
                                disabled>
                                Sinh Mã NV
                            </button>
                        </div>
                    </div>

                    <!-- Simulation 2: 3P Salary & KPI Risk -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-slate-800">Giả Lập Lương 3P & Phạt KPI</h2>
                            <span
                                class="px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded">Official</span>
                        </div>
                        <p class="text-sm text-slate-600 mb-6">Tính lương thực nhận dựa trên cấu trúc 3P và xem Lỗi
                            (Error KPI) trừ tiền như thế nào.</p>

                        <div class="space-y-4 mb-6">
                            <!-- Sliders -->
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>P1 (Vị Trí/Vùng)</span>
                                    <span class="font-bold" id="val-p1">10,000,000</span>
                                </div>
                                <input type="range" min="5000000" max="20000000" step="500000" value="10000000"
                                    class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer"
                                    oninput="updateSalary()" id="range-p1">
                            </div>

                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>P2 (Năng Lực/KPI)</span>
                                    <span class="font-bold" id="val-p2">5,000,000</span>
                                </div>
                                <input type="range" min="0" max="10000000" step="500000" value="5000000"
                                    class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer"
                                    oninput="updateSalary()" id="range-p2">
                            </div>

                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span>P3 (Thâm niên/Thưởng)</span>
                                    <span class="font-bold" id="val-p3">2,000,000</span>
                                </div>
                                <input type="range" min="0" max="5000000" step="100000" value="2000000"
                                    class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer"
                                    oninput="updateSalary()" id="range-p3">
                            </div>

                            <!-- Error Toggles -->
                            <div class="bg-red-50 p-3 rounded border border-red-100 mt-4">
                                <h4 class="text-xs font-bold text-red-800 uppercase mb-2">Các vi phạm phát sinh (Trừ
                                    KPI):</h4>
                                <div class="flex flex-col space-y-2">
                                    <label class="flex items-center justify-between cursor-pointer">
                                        <span class="text-sm text-red-700">E01: Nhập sai dữ liệu lương (-10%)</span>
                                        <input type="checkbox" id="err-e01" class="form-checkbox h-4 w-4 text-red-600"
                                            onchange="updateSalary()">
                                    </label>
                                    <label class="flex items-center justify-between cursor-pointer">
                                        <span class="text-sm text-red-700">E03: Thiếu Hợp đồng/Phụ lục (-20%)</span>
                                        <input type="checkbox" id="err-e03" class="form-checkbox h-4 w-4 text-red-600"
                                            onchange="updateSalary()">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Result -->
                        <div class="bg-slate-800 text-white p-4 rounded-lg">
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-xs text-slate-400">Tổng thu nhập ước tính</p>
                                    <h3 class="text-2xl font-bold text-yellow-400" id="total-salary">0 VND</h3>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-red-300">Tổng phạt KPI</p>
                                    <p class="font-bold text-red-400" id="penalty-amt">0%</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-slate-500">
                &copy; 2026 FOSO Human Resource System Simulation. Based on SOP_CT_VONGDOI_NHANSU_v1.
            </p>
        </div>
    </footer>
</div>
<?php init_tail(); ?>
<script>
    // --- DATA & CONTENT ---
    const phaseDetails = {
        phase1: {
            title: "Giai đoạn 1: Tuyển Dụng & Sàng Lọc",
            steps: [
                "S0: Tạo YCTD (Mã JD, Ngân sách bắt buộc).",
                "S1: Duyệt YCTD & Mở Pipeline.",
                "S2: Thu thập CV ứng viên.",
                "S3: Lọc CV (Gate 1 - Dựa trên tiêu chí cứng của JD).",
                "S4: Phỏng vấn & Đánh giá (Gate 2 - Scorecard)."
            ],
            rules: [
                "⚠️ Thiếu Mã JD -> Không cho tạo YCTD.",
                "⚠️ Deadline tuyển dụng < T+15 ngày -> Cảnh báo.",
                "⚠️ Kết quả PV 'Không đạt' -> Dừng quy trình."
            ]
        },
        phase2: {
            title: "Giai đoạn 2: Offer & Onboarding",
            steps: [
                "S5: Đề xuất Offer (Lương P1+P2) & Gửi thư mời.",
                "S6: Ứng viên nhận Offer & Điền E-Form.",
                "S7: Đối chiếu hồ sơ gốc tại công ty (Gate Onsite).",
                "S8: Sinh mã nhân viên & Tạo HĐ thử việc."
            ],
            rules: [
                "⛔ HR phải check 100% hồ sơ gốc mới được qua bước S7.",
                "⛔ Phát hiện gian dối -> Blacklist & Loại ngay.",
                "⛔ Chưa hoàn tất S7 -> Nút 'Sinh Mã NV' bị KHÓA."
            ]
        },
        phase3: {
            title: "Giai đoạn 3: Thử Việc",
            steps: [
                "S9: Nhân sự thử việc (Lương = 80% [P1+P2]).",
                "Đánh giá KPI thử việc (Cuối kỳ).",
                "Gate 4: Quyết định Ký chính thức hay Chấm dứt."
            ],
            rules: [
                "⚠️ Lương thử việc chưa bao gồm P3.",
                "⚠️ Không đạt KPI thử việc -> Không ký chính thức."
            ]
        },
        phase4: {
            title: "Giai đoạn 4: Chính Thức & Vận Hành",
            steps: [
                "S10/S11: Đánh giá định kỳ (6 tháng / 12 tháng).",
                "S12: Ký HĐ không xác định thời hạn.",
                "Tx: Tái ký Hợp đồng (T-30 ngày)."
            ],
            rules: [
                "⛔ Không có HĐ/Phụ lục -> Khóa lương (Không trả lương).",
                "⚠️ Lỗi vận hành (Thiếu HĐ, Sai BHXH) -> Trừ trực tiếp KPI."
            ]
        }
    };

    // --- CHART SETUP ---
    document.addEventListener('DOMContentLoaded', function () {
        // KPI Chart
        const ctxKPI = document.getElementById('kpiChart').getContext('2d');
        new Chart(ctxKPI, {
            type: 'doughnut',
            data: {
                labels: ['Kết quả công việc (70%)', 'Tuân thủ quy trình (30%)'],
                datasets: [{
                    data: [70, 30],
                    backgroundColor: ['#3b82f6', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Cấu trúc KPI & Rủi ro (Minh họa)'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.raw + '%';
                            },
                            afterLabel: function (context) {
                                if (context.label.includes('Tuân thủ')) return "Bao gồm các lỗi: Thiếu HĐ, Sai lương, Trễ deadline.";
                                return "";
                            }
                        }
                    }
                }
            }
        });

        // Pipeline Chart
        const ctxPipeline = document.getElementById('pipelineChart').getContext('2d');
        new Chart(ctxPipeline, {
            type: 'bar',
            data: {
                labels: ['S2: Ứng Tuyển', 'S3: Đạt Lọc', 'S4: Đạt PV', 'S5: Offer', 'S8: Nhận việc'],
                datasets: [{
                    label: 'Số lượng Ứng viên',
                    data: [100, 60, 20, 15, 12],
                    backgroundColor: ['#94a3b8', '#60a5fa', '#3b82f6', '#2563eb', '#10b981'],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Tỷ lệ chuyển đổi qua các Gate'
                    }
                }
            }
        });

        // Initialize Simulation
        updateSalary();
        showStepDetail('phase2'); // Default detail view
    });

    // --- TAB NAVIGATION ---
    function switchTab(tabId) {
        // Hide all views
        ['overview', 'recruitment', 'software', 'simulation'].forEach(id => {
            document.getElementById(`view-${id}`).classList.add('hidden');
            document.getElementById(`tab-${id}`).classList.remove('active', 'border-blue-600', 'text-blue-600');
            document.getElementById(`tab-${id}`).classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected view
        document.getElementById(`view-${tabId}`).classList.remove('hidden');
        const activeTab = document.getElementById(`tab-${tabId}`);
        activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    }

    // --- OVERVIEW INTERACTIONS ---
    function showStepDetail(phaseKey) {
        const data = phaseDetails[phaseKey];
        const panel = document.getElementById('step-detail-panel');

        panel.classList.remove('hidden');
        panel.classList.add('fade-in');

        document.getElementById('step-title').textContent = data.title;

        const stepList = document.getElementById('step-list');
        stepList.innerHTML = data.steps.map(s => `<li>• ${s}</li>`).join('');

        const ruleList = document.getElementById('rule-list');
        ruleList.innerHTML = data.rules.map(r => `<li>${r}</li>`).join('');
    }

    // --- SIMULATION 1: GATE 3 (CHECKLIST) ---
    function updateGateStatus() {
        const cccd = document.getElementById('chk-cccd').checked;
        const degree = document.getElementById('chk-degree').checked;
        const health = document.getElementById('chk-health').checked;
        const bank = document.getElementById('chk-bank').checked;
        const eform = document.getElementById('chk-eform').checked;

        const allChecked = cccd && degree && health && bank && eform;
        const box = document.getElementById('gate-result-box');
        const statusText = document.getElementById('gate-status-text');
        const msg = document.getElementById('gate-msg');
        const btn = document.getElementById('btn-create-id');

        if (allChecked) {
            box.classList.remove('border-dashed', 'border-slate-300');
            box.classList.add('bg-green-50', 'border-green-500', 'border-solid');

            statusText.textContent = "✅ ĐỦ ĐIỀU KIỆN (PASSED)";
            statusText.classList.remove('text-slate-400');
            statusText.classList.add('text-green-700');

            msg.textContent = "Gate 3 đã mở. Hệ thống cho phép sinh mã.";
            msg.classList.remove('text-slate-400');
            msg.classList.add('text-green-600');

            btn.disabled = false;
            btn.classList.remove('bg-slate-300', 'cursor-not-allowed');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer', 'shadow-lg');
        } else {
            box.classList.add('border-dashed', 'border-slate-300');
            box.classList.remove('bg-green-50', 'border-green-500', 'border-solid');

            statusText.textContent = "🔒 ĐANG KHÓA (LOCKED)";
            statusText.classList.add('text-slate-400');
            statusText.classList.remove('text-green-700');

            msg.textContent = `Còn thiếu ${5 - [cccd, degree, health, bank, eform].filter(Boolean).length} mục bắt buộc.`;
            msg.classList.add('text-slate-400');
            msg.classList.remove('text-green-600');

            btn.disabled = true;
            btn.classList.add('bg-slate-300', 'cursor-not-allowed');
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer', 'shadow-lg');
        }
    }

    // --- SIMULATION 2: 3P SALARY ---
    function formatCurrency(num) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(num);
    }

    function updateSalary() {
        // Get values
        const p1 = parseInt(document.getElementById('range-p1').value);
        const p2 = parseInt(document.getElementById('range-p2').value);
        const p3 = parseInt(document.getElementById('range-p3').value);

        // Update labels
        document.getElementById('val-p1').textContent = formatCurrency(p1).replace('₫', '');
        document.getElementById('val-p2').textContent = formatCurrency(p2).replace('₫', '');
        document.getElementById('val-p3').textContent = formatCurrency(p3).replace('₫', '');

        // Calculate Base
        let total = p1 + p2 + p3;

        // Apply Penalties
        let penaltyPercent = 0;
        if (document.getElementById('err-e01').checked) penaltyPercent += 10;
        if (document.getElementById('err-e03').checked) penaltyPercent += 20;

        const penaltyAmount = total * (penaltyPercent / 100);
        const finalTotal = total - penaltyAmount;

        // Render Result
        document.getElementById('total-salary').textContent = formatCurrency(finalTotal);
        document.getElementById('penalty-amt').textContent = `-${penaltyPercent}% (${formatCurrency(penaltyAmount)})`;

        if (penaltyPercent > 0) {
            document.getElementById('total-salary').classList.add('text-red-400');
            document.getElementById('total-salary').classList.remove('text-yellow-400');
        } else {
            document.getElementById('total-salary').classList.add('text-yellow-400');
            document.getElementById('total-salary').classList.remove('text-red-400');
        }
    }
</script>