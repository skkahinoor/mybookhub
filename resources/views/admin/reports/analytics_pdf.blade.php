<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analytics Report</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #1e3a8a; }
        .header p { margin: 5px 0 0; color: #6b7280; font-size: 12px; }
        .summary { margin-bottom: 30px; }
        .summary-box { display: inline-block; width: 45%; background: #f3f4f6; padding: 15px; border-radius: 5px; }
        .summary-box h3 { margin: 0 0 10px; font-size: 16px; color: #374151; }
        .summary-box .value { font-size: 20px; font-weight: bold; color: #3b82f6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; font-size: 12px; }
        th { background-color: #f9fafb; color: #374151; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

<div class="header">
    <h1>MyBookHub Analytics Report</h1>
    <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</p>
    <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
</div>

<div class="summary">
    <div class="summary-box">
        <h3>Total Unique Views</h3>
        <div class="value">{{ number_format($totalUniqueViews) }}</div>
    </div>
</div>

<h3>Top 50 Pages by Unique Views</h3>
<table>
    <thead>
        <tr>
            <th style="width: 5%">#</th>
            <th style="width: 40%">Page Title</th>
            <th style="width: 35%">URL</th>
            <th style="width: 10%" class="text-center">Module</th>
            <th style="width: 10%" class="text-center">Views</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topPages as $index => $page)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ Str::limit($page->page_title ?? 'Unknown', 40) }}</td>
            <td>{{ Str::limit($page->url, 40) }}</td>
            <td class="text-center">{{ ucfirst($page->module) }}</td>
            <td class="text-center">{{ number_format($page->unique_views) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No page views found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
