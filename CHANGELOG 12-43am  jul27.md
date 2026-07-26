# Changelog — Inventory Dashboard Module

## Alerts & Reorders
- **Overstocked items can't create a PO.** When an item's status is "Overstock," the row shows a disabled "Overstocked" label instead of the "Create PO" button, so no purchase order can be raised until stock drops back below the max limit.
- **Item list is now expandable.** The Master Inventory Thresholds table now collapses to the first 8 items with a "Show N more items" toggle underneath, instead of one long scrolling list. Expanding/collapsing works instantly, and the list resets to collapsed whenever a quick filter card is clicked.

## Dashboard
- **Warehouse, Stock Movement, and Inventory Items logs now live on the Dashboard.** The Category, Warehouse, and System Log panels on the dashboard pull live data and can be expanded into a detail modal.
- **Fixed the Inventory Flow Trends chart date bug.** It was previously plotting *future* dates (today → +5 days), so it always showed a flat line at 0. It now correctly plots historical activity.
- **Added a time-range selector to the chart:** 7D (default), 1M, 3M, YTD, 1Y, Max — matching a standard stock-chart style toolbar.
- **"Expand Chart" now actually works.** It used to open a static text ledger; it now opens a live, larger version of the same chart with its own range tabs.
- **Click-to-drill-down on the expanded chart.** Clicking any point on the expanded chart shows a details table below it listing every approved stock movement in that period (Tx ID, item, type, quantity, requester, date).
- **Fixed swapped Category/Warehouse expand panels.** "Product Categories → Expand" was incorrectly opening the Warehouse breakdown, and "Warehouse Locations → Expand" was opening the Category breakdown. Each now opens its correct panel.
- **Removed non-functional buttons.** "View Items" (Product Categories card), "View Maps" (Warehouse Locations card), and "Full Page →" (Products Directory Overview) were all placeholders that only triggered a "Module in development" alert — removed since they didn't do anything.
