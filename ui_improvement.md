Use a small sidebar with clear business words. Keep technical settings hidden under one **Settings** page.

## Main Navigation

```text
Dashboard
POS
Orders
Menu
Inventory
Reports
Settings
```

## All Pages / Modules

| Page             | Main purpose                      | Simple UI                                                                  |
| ---------------- | --------------------------------- | -------------------------------------------------------------------------- |
| **Dashboard**    | See today’s business status       | Today sales, orders, cash, KHQR, low stock, recent orders                  |
| **POS**          | Cashier creates orders            | Product categories, product grid, cart, payment popup, receipt             |
| **Orders**       | Find and manage orders            | Search receipt, date, status, payment; view, cancel/refund with permission |
| **Menu**         | Manage food and drinks            | Categories and products in one screen; price, photo, availability toggle   |
| **Digital Menu** | Manage public QR menu             | Preview menu, download/print QR code, enable/disable products              |
| **Inventory**    | Manage ingredients/products stock | Current stock, stock in/out, low-stock labels, stock history               |
| **Reports**      | Review sales performance          | Date filter, total sales, payment breakdown, top products, export          |
| **Settings**     | Configure the shop                | Shop details, payments, users, notifications, receipt, system options      |

## Dashboard

```text
Today - Sunday, 22 June

Sales             Orders            Cash              KHQR
$125.50           42                $45.00            $80.50

[ + New Order ]

Recent Orders                         Low Stock
#POS-00124  $4.50  Paid               Milk       3 left
#POS-00123  $6.00  Preparing          Cups       10 left
#POS-00122  $3.00  Completed          Coffee     5 left
```

## POS Page

```text
Categories          Products                         Current Order
[All] [Coffee]      [Iced Latte] [Americano]        Table / Takeaway
[Tea] [Food]        [Cappuccino] [Croissant]        ------------------
                                                  Iced Latte      $3.00
                                                  Less ice
                                                  Extra shot      $0.50
                                                  ------------------
                                                  Total           $3.50
                                                  [ Pay $3.50 ]
```

Payment popup:

```text
Choose payment

[ Cash ]  [ Scan KHQR ]  [ ABA Pay ]

[ Print Receipt ]  [ Complete Order ]
```

## Menu Page

Do not separate Categories and Products into several difficult pages. Use tabs inside one page.

```text
Menu

[ Products ] [ Categories ]

Search products...                 [ + Add Product ]

Photo      Product          Category    Price     Available
[image]    Iced Latte       Coffee      $3.00     [ ON ]
[image]    Croissant        Food        $2.50     [ ON ]
```

Click product -> compact slide-over:

```text
Edit Product

Photo       [ Upload ]
Name        [ Iced Latte ]
Category    [ Coffee v ]
Base price  [ 3.00 ] [ USD v ]
Available   [ ON ]

[ Save ]
```

## Inventory Page

```text
Inventory                              [ + Stock In ]

Item             In Stock       Status       Action
Coffee beans     2 kg           Low          Adjust
Fresh milk       12 bottles     Good         Adjust
Paper cups       8 pcs          Low          Adjust
```

Click **Adjust**:

```text
Adjust Stock - Fresh Milk

[ + Add stock ] [ - Use / Waste ]

Quantity [ 5 ]
Reason   [ New delivery v ]

[ Save ]
```

## Reports Page

```text
Reports

[ Today v ] [ Export ]

Total Sales      Cash        KHQR       Orders       Refunds
$125.50          $45.00      $80.50     42           $3.00

Sales by Product
Iced Latte       18 sold     $54.00
Americano        12 sold     $30.00
Croissant        10 sold     $25.00
```

## Settings: One Page With Tabs

```text
Settings

[ Shop ] [ Payments ] [ Notifications ] [ Users ] [ Receipt ]

Shop: name, logo, address, phone, currency, tax
Payments: cash, KHQR, ABA/card configuration
Notifications: email/Telegram enable, recipients, test message
Users: add staff, role, active/disabled account
Receipt: header, footer, printer choice, test print
```

For normal shop staff, only show:

```text
POS
Orders
```

For manager:

```text
Dashboard
POS
Orders
Menu
Inventory
Reports
```

For owner/admin: show every page including **Settings**.
