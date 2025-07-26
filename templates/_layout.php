<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Usage Analysis Report</title>
    <style>
        /* General Body & Layout Styling */
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            margin: 20px; 
            background-color: #f4f7f9; 
            color: #333; 
            line-height: 1.6;
        }

        /* Headings */
        h1, h2 { 
            color: #2c3e50; 
            border-bottom: 2px solid #e0e0e0; 
            padding-bottom: 10px;
            font-weight: 600;
        }
        
        h1 {
            font-size: 2em;
        }
        
        h2 {
            font-size: 1.5em;
            margin-top: 40px;
        }

        /* Table Styling */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px; 
            background-color: #ffffff; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden; /* Ensures border-radius is respected by children */
        }
        
        th, td { 
            border: 1px solid #e0e0e0; 
            padding: 12px 15px; 
            text-align: left; 
            vertical-align: top;
        }
        
        th { 
            background-color: #f2f2f2; 
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) { 
            background-color: #f9fafb; 
        }

        /* Specific Cell Styling */
        .selector-cell { 
            font-family: "SF Mono", "Fira Code", "Fira Mono", "Roboto Mono", monospace; 
            color: #c82829; /* Rød for selektor */
            font-weight: 500;
        }
        
        .filepath-cell { 
            font-size: 0.9em; 
            color: #555; 
            font-family: "SF Mono", "Fira Code", "Fira Mono", "Roboto Mono", monospace;
        }
        
        .linenum-cell { 
            text-align: right; 
            width: 80px; 
            color: #007bff;
            font-family: "SF Mono", "Fira Code", "Fira Mono", "Roboto Mono", monospace;
        }
        
        .code-cell { 
            font-family: "SF Mono", "Fira Code", "Fira Mono", "Roboto Mono", monospace; 
            white-space: pre-wrap; 
            word-break: break-all; 
            background-color: #fdfdfd; 
            font-size: 0.85em; 
            color: #333;
        }

        /* Info & Status Boxes */
        .error { 
            color: #d8000c; 
            background-color: #ffbaba;
            border-left: 5px solid #d8000c;
            padding: 15px;
            font-weight: bold;
        }
        
        .info, .summary { 
            margin-bottom: 20px; 
            padding: 15px; 
            background-color: #e7f3fe; 
            border-left: 5px solid #2196F3;
            border-radius: 4px;
        }
        
        .summary p {
            margin: 5px 0;
        }
        
        /* Footer */
        .footer-text {
            margin-top: 40px; 
            font-size: 0.8em; 
            text-align: center; 
            color: #777;
        }

    </style>
</head>
<body>
    <h1>CSS Usage Analysis Report</h1>
    
    <?= $summarySection ?? '' ?>
    
    <?= $usedSelectorsSection ?? '' ?>
    
    <?= $unusedSelectorsSection ?? '' ?>
    
    <p class="footer-text">Report generated: <?= date('Y-m-d H:i:s') ?></p>
</body>
</html>
