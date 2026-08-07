<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư Mời Nhận Việc - <?= isset($offer) ? $offer->ma_offer : '' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.6;
            color: #000;
            background: #fff;
            padding: 20mm;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        @media print {
            @page {
                size: A4;
                margin: 20mm;
            }
            
            body {
                padding: 0;
            }
        }

    .preview-content-wrapper {
        flex: 1;
        overflow-y: auto;
        background: #cbd5e1;
        padding: 30px 20px;
        display: flex;
        justify-content: center;
    }

    /* A4 Document Style */
    .document-a4 {
        background: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        width: 210mm;
        min-height: 297mm;
        padding: 5mm;
        margin: 0 auto;
        font-family: 'Times New Roman', Times, serif;
        line-height: 1.6;
        color: #1e293b;
        position: relative;
    }

    /* Company Header */
    .doc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 3px solid #000;
    }

    .company-info h1 {
        font-size: 24px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #000;
        margin: 0 0 8px 0;
    }

    .company-info p {
        font-size: 13px;
        font-style: italic;
        color: #475569;
        margin: 4px 0;
    }

    .doc-meta {
        text-align: right;
    }

    .confidential-badge {
        font-size: 12px;
        font-weight: 700;
        border: 2px solid #000;
        padding: 6px 12px;
        display: inline-block;
        margin-bottom: 8px;
    }

    .doc-meta p {
        font-size: 11px;
        color: #64748b;
        margin: 4px 0;
        font-family: 'Courier New', monospace;
    }

    /* Document Title */
    .doc-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .doc-title h1 {
        font-size: 28px;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0 0 8px 0;
        color: #000;
        letter-spacing: 0.5px;
    }

    .doc-title p {
        font-size: 18px;
        font-style: italic;
        color: #64748b;
        font-family: Arial, sans-serif;
        font-weight: 300;
    }

    /* Document Body */
    .doc-body {
        font-size: 13px;
        text-align: justify;
    }

    .doc-body p {
        margin-bottom: 16px;
    }

    .highlight-name {
        font-weight: 700;
        font-size: 16px;
    }

    /* Info Table */
    .info-table {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        overflow: hidden;
        margin: 30px 0;
        font-size: 13px;
    }

    .info-table-row {
        display: flex;
        border-bottom: 1px solid #cbd5e1;
    }

    .info-table-row:last-child {
        border-bottom: none;
    }

    .info-table-label {
        flex: 0 0 35%;
        padding: 12px;
        background: #f8fafc;
        border-right: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        display: flex;
        align-items: center;
    }

    .info-table-value {
        flex: 1;
        padding: 12px;
        font-weight: 600;
    }

    /* Benefits Section */
    .section-title {
        font-weight: 700;
        font-size: 15px;
        text-transform: uppercase;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 8px;
        margin: 30px 0 20px 0;
    }

    .benefits-list {
        margin-left: 25px;
        margin-bottom: 20px;
    }

    .benefits-list li {
        margin-bottom: 12px;
        line-height: 1.8;
    }

    .benefits-list li strong {
        color: #334155;
    }

    .benefits-list li .note {
        font-size: 12px;
        font-style: italic;
        color: #64748b;
        margin-left: 8px;
    }

    .highlight-total {
        font-weight: 700;
        color: #1e40af;
        background: rgba(59, 130, 246, 0.1);
        padding: 6px 12px;
        border-radius: 4px;
        display: inline-block;
        margin: 4px 0;
    }

    .validity-text {
        margin-top: 30px;
    }

    .validity-text .underline {
        font-weight: 700;
        border-bottom: 1px solid #000;
        display: inline-block;
    }

    /* Signature Section */
    .signature-section {
        display: flex;
        justify-content: space-around;
        margin-top: 80px;
        padding: 0 40px;
    }

    .signature-box {
        text-align: center;
        width: 30%;
    }

    .signature-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 100px;
    }

    .signature-line {
        border-top: 1px solid #64748b;
        padding-top: 12px;
        margin: 0 auto;
        max-width: 200px;
    }

    .signature-name {
        font-weight: 700;
        margin-bottom: 4px;
    }

    .signature-title {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
    }

    .signature-note {
        font-size: 11px;
        font-style: italic;
        color: #94a3b8;
        margin-top: 4px;
    }

    /* Print Styles */
    @media print {
        body * {
            visibility: hidden;
        }

        .document-a4,
        .document-a4 * {
            visibility: visible;
        }

        .document-a4 {
            position: absolute;
            left: 0;
            top: 0;
            width: 210mm;
            min-height: 297mm;
            box-shadow: none;
            margin: 0;
            padding: 5mm;
        }

        .preview-header,
        .preview-actions,
        .modal-header,
        .modal-footer {
            display: none !important;
        }

        @page {
            size: A4;
            margin: 0;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .preview-modal .modal-dialog {
            width: 100%;
            margin: 0;
            height: 100vh;
        }

        .document-a4 {
            width: 100%;
            padding: 5mm;
        }

        .signature-section {
            flex-direction: column;
            gap: 60px;
        }

        .signature-box {
            width: 100%;
        }
    </style>
</head>
<body>
    <?= $content ?>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>