@php
    $templateConfigs = [
        'classic' => ['border' => '#d6d3d1', 'style' => 'dashed', 'accent' => '#44403c'],
        'minimal' => ['border' => '#e7e5e4', 'style' => 'solid', 'accent' => '#44403c'],
        'detailed' => ['border' => '#44403c', 'style' => 'solid', 'accent' => '#44403c'],
        'compact' => ['border' => '#d6d3d1', 'style' => 'solid', 'accent' => '#44403c'],
        'branded' => ['border' => '#8b5cf6', 'style' => 'dashed', 'accent' => '#8b5cf6'],
    ];
    $tc = $templateConfigs[$template] ?? $templateConfigs['classic'];
    $borderColor = $tc['border'];
    $borderStyle = $tc['style'];
    $accentColor = $tc['accent'];
    $decorative = $template === 'branded';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Receipt</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: ui-monospace, 'SF Mono', Menlo, monospace;
            background: #f5f5f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .receipt {
            max-width: 320px;
            width: 100%;
            background: white;
            border-radius: {{ $decorative ? '12px' : '8px' }};
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            color: #44403c;
            {{ $decorative ? 'border:2px solid '.$accentColor.';' : '' }}
        }
        .receipt hr { border: none; border-top: 2px {{ $borderStyle }} {{ $borderColor }}; margin: 8px 0; }
        .receipt .thin { border-top-width: 1px; }
        .receipt .solid { border-top-style: solid; border-color: {{ $accentColor }}; }
        .text-center { text-align: center; }
        .text-muted { color: #78716c; }
        .text-xs { font-size: 11px; }
        .text-sm { font-size: 12px; }
        .text-lg { font-size: 15px; }
        .font-bold { font-weight: 700; }
        .mb-2 { margin-bottom: 8px; }
        .mb-1 { margin-bottom: 4px; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; font-size: 12px; }
        td:last-child { text-align: right; white-space: nowrap; }
        th { font-size: 11px; color: #a8a29e; text-transform: uppercase; text-align: left; padding: 4px 0; }
        th:last-child { text-align: right; }
        .note-box {
            margin-top: 8px; padding: 8px; background: #f5f5f4;
            border-radius: 6px; font-size: 12px; color: #78716c;
        }
        .print-btn {
            margin-top: 20px;
            padding: 10px 24px;
            background: #1d4ed8;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-family: system-ui, sans-serif;
            cursor: pointer;
        }
        .print-btn:hover { background: #1e40af; }
        .template-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 6px;
            color: {{ $accentColor }};
            background: {{ $accentColor }}15;
        }
        @media print {
            body { background: white; padding: 0; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        @if($decorative)
            <div class="text-center text-xs" style="color:{{ $accentColor }};margin-bottom:6px">· &nbsp;· &nbsp;· &nbsp;· &nbsp;· &nbsp;· &nbsp;· &nbsp;·</div>
        @endif

        <div class="text-center">
            <span class="template-badge">{{ $template }}</span>
            <div class="text-lg font-bold" style="letter-spacing:1px;color:{{ $decorative ? $accentColor : '#44403c' }}">{{ $shopName }}</div>
            @if($address)
                <div class="text-xs text-muted">{{ $address }}</div>
            @endif
            @if($phone)
                <div class="text-xs text-muted">Tel: {{ $phone }}</div>
            @endif
        </div>

        <hr>

        @if($header)
            <div class="text-center text-xs text-muted mb-2">{{ $header }}</div>
            <hr class="thin">
        @endif

        <div class="text-xs text-muted mb-2">
            <div>Order #: TEST-{{ date('Ymd') }}-0001</div>
            <div>{{ date('d M Y, H:i') }}</div>
            <div>Type: Dine-in | Table: 5</div>
            <div>Cashier: Admin</div>
        </div>

        <hr class="thin">

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price ({{ $currencySymbol }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sampleItems as $item)
                    <tr>
                        <td>{{ $item['name'] }} <span class="text-muted">x{{ $item['qty'] }}</span></td>
                        <td>{{ number_format($item['price'] * $item['qty'], 2) }}</td>
                    </tr>
                    <tr><td colspan="2" style="padding:0 0 4px 8px;font-size:11px;color:#a8a29e">+ Extra shot</td></tr>
                @endforeach
            </tbody>
        </table>

        <hr class="thin">

        <table>
            <tr><td class="text-muted">Subtotal</td><td class="text-muted">{{ number_format($subtotal, 2) }}</td></tr>
            <tr><td class="text-muted">Discount</td><td class="text-muted">-{{ number_format($discount, 2) }}</td></tr>
            <tr><td class="text-muted">Tax</td><td class="text-muted">{{ number_format($tax, 2) }}</td></tr>
        </table>

        <hr class="solid">

        <table class="text-lg font-bold">
            <tr><td>TOTAL</td><td>{{ number_format($total, 2) }}</td></tr>
        </table>

        <hr class="solid">

        <div class="text-sm text-muted mb-2">
            <div class="flex justify-between"><span>Paid</span><span>{{ number_format($paid, 2) }}</span></div>
            <div class="flex justify-between"><span>Change</span><span>{{ number_format($change, 2) }}</span></div>
        </div>

        <div class="note-box">
            <strong>Notes:</strong> Please bring to table 5
        </div>

        <hr class="thin">

        @if($footer)
            <div class="text-center text-xs text-muted mt-1">{{ $footer }}</div>
        @endif

        <hr>

        @if($decorative)
            <div class="text-center text-xs" style="color:{{ $accentColor }};margin-top:4px">· &nbsp;· &nbsp;· &nbsp;· &nbsp;· &nbsp;· &nbsp;· &nbsp;·</div>
        @endif

        <div class="text-center text-xs text-muted">Test Receipt &middot; {{ $template }} template</div>
    </div>

    <div style="text-align:center;margin-top:4px;font-size:10px;color:#a8a29e">Printed {{ date('d M Y, H:i') }}</div>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>

    <script>
        if (window.location.search.includes('print')) {
            window.print();
        }
    </script>
</body>
</html>
