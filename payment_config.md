Use a clean Filament interface with two pages: **Payment Methods** and **Payment Configuration**.

## 1. Payment Methods List

`/admin/payment-methods`

```text
Payment Methods                                      [+ Add Method]

Method        Provider              Currency     Status       Actions
Cash          Manual                KHR, USD     Enabled      Edit
Scan KHQR     Bakong / Third Party  KHR, USD     Enabled      Edit
ABA PayWay    ABA                    USD          Disabled     Edit
Card          Stripe                 USD          Disabled     Edit
```

Actions:

* Enable/disable toggle
* Edit
* Duplicate configuration
* Send test payment / test QR
* Set display order

## 2. Add / Edit Payment Method Form

Use tabs to avoid one huge ugly form.

```text
[General] [Provider] [Credentials] [Rules] [Test]

General
  Display Name       [ Scan KHQR                 ]
  Payment Type       [ KHQR                  v  ]
  Enabled            [ ON ]
  Display Order      [ 2 ]
  Icon               [ QR icon ]

Provider
  Provider Type      [ Third-party KHQR API  v  ]
  Dynamic QR         [ ON ]
  QR Expiry          [ 5 ] minutes
  Currencies         [✓ KHR] [✓ USD]

Credentials
  API Base URL       [ https://api.example.com ]
  Merchant ID        [ merchant_123            ]
  API Key            [ ************************ ]
  Webhook Secret     [ ************************ ]

Rules
  Minimum Amount     [ 0.01 ]
  Maximum Amount     [             ]
  Available for      [✓ POS] [✓ QR Customer Order]
  Branches           [ All Branches          v  ]

Test
  Test Mode          [ ON ]
  [ Generate Test QR ]  [ Send Test Webhook ]
```

## POS Payment Popup

When the cashier clicks **Checkout**, show only enabled methods:

```text
Checkout - Order #POS-000124
Total: $6.50

[ Cash ]    [ Scan KHQR ]    [ ABA Pay ]

Selected: Scan KHQR

        [ Dynamic KHQR image ]

Amount: $6.50
Expires in: 04:52
Status: Waiting for payment...

[ Cancel Payment ]   [ Check Status ]
```

After server verification:

```text
Payment confirmed

KHQR: $6.50
Reference: KHQR-20260622-124
Paid at: 15:42

[ Print Receipt ] [ Complete Order ]
```

## Filament Components

| Need                         | Filament component                            |
| ---------------------------- | --------------------------------------------- |
| Enable/disable               | `Toggle`                                      |
| Provider selection           | `Select`                                      |
| Currency options             | `CheckboxList`                                |
| API secrets                  | `TextInput::make()->password()->revealable()` |
| QR expiry / limits           | `TextInput::numeric()`                        |
| POS/QR ordering availability | `CheckboxList`                                |
| Configuration groups         | `Tabs`                                        |
| “Test QR” action             | `Action`                                      |
| Payment status               | `BadgeColumn`                                 |

Make the credentials fields appear only for API-based methods. For `Cash`, hide Provider and Credentials tabs entirely.
