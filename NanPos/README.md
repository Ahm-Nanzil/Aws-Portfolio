[![Latest Stable Version](https://poser.pugx.org/blair2004/possoft/v)](https://packagist.org/packages/blair2004/possoft) [![Total Downloads](https://poser.pugx.org/blair2004/possoft/downloads)](https://packagist.org/packages/blair2004/possoft) [![Latest GitHub Release](https://img.shields.io/github/v/release/Blair2004/PosSOFT)](https://github.com/Blair2004/PosSOFT/releases) [![License](https://img.shields.io/github/license/Blair2004/PosSOFT)](LICENSE)

<p align="center">
  <img src="https://user-images.githubusercontent.com/5265663/162700085-40ed00ca-9154-42cb-850a-ccf1c2db2d5d.png" alt="PosSOFT"/>
</p>

# PosSOFT

PosSOFT is a modern, extensible point-of-sale platform for retail stores, restaurants, cafés, warehouses, and other product- or service-based businesses. It brings sales, inventory, customers, purchasing, accounting, reporting, and staff operations together in one application.

The current ecosystem is built around the PosSOFT 6.2.x core and includes first-party modules, Windows applications, cloud hosting, developer tools, and the [My PosSOFT](https://my.possoft.com/en) marketplace.

### Deployment
PosSOFT is partnering with InstaPods to offer you a smooth deployment experience with PosSOFT.

[![Deploy On Nexo Cloud](https://my.possoft.com/deploy-on-nexo-cloud-button-light.svg)](https://possoft.cloud?utm_channel=github.com&utm_campaign=readme)
[![Deploy On InstaPods](https://instapods.com/deploy-button.svg)](https://app.instapods.com/dashboard/pods/create?app=possoft&ref=possoft)

## What the core provides

The PosSOFT core includes:

- Product, category, unit, tax, and customer management
- POS sales, orders, payments, receipts, refunds, and registers
- Inventory, procurement, stock history, and low-stock workflows
- Accounting and operational reporting, including configurable stock and cost visibility
- Customer groups, rewards, coupons, and store configuration
- User roles, permissions, activity tracking, and audit-friendly workflows
- Barcode and scale barcode support
- Media management, dashboard widgets, and extensibility APIs
- A module system for adding business-specific features without changing the core

PosSOFT is designed to start as a single-store POS and grow with the business. Multi-store, restaurant, service, workforce, rental, AI, printing, and other workflows are delivered through modules.

## Marketplace modules and ecosystem

The modules below are distributed separately from the PosSOFT core through the [PosSOFT Marketplace](https://my.possoft.com/en/marketplace). They are not included in this repository; install them from the marketplace when they match your PosSOFT version.

<p align="center">
<img src="https://my.possoft.com/storage/2026/09/neoxpos-marketplace.jpg">
</p>

| Module | Adds |
| --- | --- |
| [**BellSera**](https://my.possoft.com/en/marketplace/item/bellsera-spa-salon-booking-management-for-possoft) | Service appointments, public booking, staff and room availability, walk-in and booking POS order types, service queues, and reporting |
| [**Bulk Editor**](https://my.possoft.com/en/marketplace/item/bulk-editor-for-possoft) | Bulk editing tools for supported PosSOFT resources |
| [**Gastro**](https://my.possoft.com/en/marketplace/item/gastro-restaurant-extension-for-possoft) | Restaurant tables, waiter and chef workflows, kitchen screens, order routing, and product modifiers |
| [**Google Authenticator**](https://my.possoft.com/en/marketplace/item/google-authenticator) | Time-based two-factor authentication for user accounts |
| [**MultiStore**](https://my.possoft.com/en/marketplace/item/multistore-for-possoft) | Multiple isolated stores managed from one PosSOFT installation and database |
| [**Options Export/Import**](https://my.possoft.com/en/marketplace/item/options-importexport) | Exporting and importing module settings between installations |
| [**Oxen**](https://my.possoft.com/en/marketplace/item/oxen-ai-agent-for-possoft) | Permission-aware AI assistance, approved store-management actions, reports, and MCP access |
| [**PIN Login**](https://my.possoft.com/en/marketplace/item/pin-login-module-for-possoft) | Fast POS PIN login and inactivity security locking |
| [**NPS Adapter**](https://my.possoft.com/en/marketplace/item/nps-adapter-for-possoft) | PosSOFT-side integration for local and network printing workflows |
| [**QR Menu**](https://my.possoft.com/en/marketplace/item/qr-menu-digital-restaurant-menu-for-gastro) | Branded, mobile-friendly digital menus generated from the PosSOFT catalog |
| [**Quick Store Configuration**](https://my.possoft.com/en/marketplace) | Guided setup for essential store settings |
| [**Rental**](https://my.possoft.com/en/marketplace/item/possoft-rental) | Rental reservations, agreements, inventory movements, returns, maintenance, and notifications |
| [**Sales Commissions**](https://my.possoft.com/en/marketplace/item/sales-commissions) | Commission rules and sales-staff commission tracking |
| [**WorkForce**](https://my.possoft.com/en/marketplace) | Kiosk attendance, breaks, schedules, timesheets, corrections, and private reports |

Some modules have optional integrations, such as SMS, email, Nexo Print Server, or PIN Login. Check the marketplace listing for each module's supported PosSOFT core range and dependencies.

The [PosSOFT Marketplace](https://my.possoft.com/en/marketplace) is the authoritative catalog for compatible extensions, native applications, bundles, releases, and licenses. Alongside the modules above, examples of currently available ecosystem products include:

- **[Raw Material Tracker](https://my.possoft.com/en/marketplace/item/raw-material-tracker)** — recipes, ingredient stock deduction, unit conversion, cost tracking, and consumption history.
- **[Racks Manager](https://my.possoft.com/en/marketplace/item/racks-manager)** — structured warehouse storage with racks, shelves, and stock movement controls.
- **[Hotel Booking](https://my.possoft.com/en/marketplace/item/hotel-booking)** — reservations, rooms, pricing, invoices, POS payments, and calendar synchronization.
- **[PosSOFT Rental](https://my.possoft.com/en/marketplace/item/possoft-rental)** — rental operations for businesses that need reservations and returns.
- **[QR Menu](https://my.possoft.com/en/marketplace/item/qr-menu-digital-restaurant-menu-for-gastro)** — customer-facing digital menus for restaurants, cafés, bars, hotels, and bakeries.
- **[Oxen](https://my.possoft.com/en/marketplace/item/oxen-ai-agent-for-possoft)** — AI assistance grounded in store data with permission checks and approval-controlled mutations.
- **[Options Import/Export](https://my.possoft.com/en/marketplace/item/options-importexport)** — portable module configuration.

For installation and updates, use **Modules → Upload Module** in PosSOFT and confirm that the module supports your core version. Download modules from trusted sources such as [My PosSOFT](https://my.possoft.com/en/marketplace).

## Cloud, self-hosted, and Windows deployments

![cloud-hosting](https://camo.githubusercontent.com/0cbe76dab0c6299fbd4baa706bf0e40515c3d83e7814ae2cddf446e7521d87bb/68747470733a2f2f6d792e6e65786f706f732e636f6d2f73746f726167652f323032362f30372f636861746770742d696d6167652d6a756c2d32382d323032362d31312d30392d32392d616d2e706e67)

Choose the operating model that fits your business:

- **[PosSOFT Cloud](https://possoft.cloud)** — managed hosting with the infrastructure handled for you.
- **Self-hosted PosSOFT** — deploy on your own server or hosting provider for full control. The supported environment includes PHP 8.2+, MariaDB 10.11 or MySQL 8.4, Nginx or Apache, Composer, scheduled tasks, and a queue worker such as Supervisor.
- **[PosSOFT for Windows](https://my.possoft.com/en/marketplace/item/possoft-for-windows)** — an all-in-one local Windows environment with PosSOFT, Nginx, HTTPS, SQLite, and printing tools preconfigured.
- **[PosSOFT Client for Windows](https://my.possoft.com/en/marketplace/item/possoft-client-for-windows)** — securely connects additional computers on the same local network to a PosSOFT for Windows server.
- **[Nexo Print Server](https://my.possoft.com/en/marketplace/item/nexo-print-server)** — direct Windows printing for ESC/POS-compatible thermal printers, used with the NPS Adapter module.
- **[Nexo SaaS](https://my.possoft.com/en/marketplace/item/nexo-saas-build-and-manage-a-possoft-hosting-business/details)** — deploy and manage multiple PosSOFT installations, subscriptions, domains, backups, and module bundles as a hosted service.

See the [official documentation](https://my.possoft.com/en/documentation) for environment, installation, Windows, cloud, and local-network guides.

## Quick start for developers

PosSOFT is a Laravel application using Vue, Vite, Tailwind CSS, and a first-class module architecture. The root project currently targets PHP 8.2+ and Node.js 24.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
npm install
npm run build
```

Configure the database and application URL in `.env`, then run the normal Laravel migrations and workers required by your deployment. The exact production setup depends on whether you use queues, scheduled reports, Reverb, multi-store, printing, or other modules.

Create a new module with the built-in generator:

```bash
php artisan make:module
```

Custom module source normally lives under `modules/` and contains its own routes, providers, migrations, resources, assets, settings, and tests. Start with the [module creation guide](https://my.possoft.com/en/documentation/developers-guide/module-creation/creating-module), then see the [module config guide](https://my.possoft.com/en/documentation/developers-guide/module-creation/module-config-file), [dashboard menu guide](https://my.possoft.com/en/documentation/developers-guide/module-creation/dashboard-menus), and [PosSOFT API documentation](https://docs.api.possoft.com).

PosSOFT also documents extension points for dashboard menus and widgets, Vue components, POS cart buttons and product mutation, receipts and invoices, settings, events, and MCP integrations.

## Demo and resources

- [Try PosSOFT](https://demo.possoft.com)
- [My PosSOFT](https://my.possoft.com/en)
- [Official documentation](https://my.possoft.com/en/documentation)
- [Marketplace](https://my.possoft.com/en/marketplace)
- [PosSOFT REST API](https://docs.api.possoft.com)
- [GitHub releases](https://github.com/Blair2004/PosSOFT/releases)

## License

PosSOFT is open source software licensed under the [GNU General Public License v3.0](LICENSE).
