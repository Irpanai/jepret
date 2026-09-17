# JEPRETCFD — Super Admin Full Refactor & Activation Prompt for Codex

## Project

Repository: `https://github.com/Irpanai/jepret`  
Repository name: `Irpanai/jepret`  
Target branch: `main`

The IDE is already on `main` and `main` is already up to date.

You are acting as a **Senior Laravel Engineer, Software Architect, Database Engineer, QA Engineer, Security Reviewer, and Product Admin-System Engineer**.

Your task is to refactor the entire **Super Admin** area so it stops looking like a demo/mock dashboard and becomes a real, database-driven operational console that is fully connected to the existing Buyer, Photographer, Photo, Event, Transaction, Withdrawal, Storage, and Payment flows.

This is an implementation task, not a visual mockup task.

---

# 0. Critical Working Rules

Before editing anything:

```bash
git status
git branch --show-current
git log -1 --oneline
```

Expected branch:

```text
main
```

Do **not** automatically:

- `git pull`
- reset
- rebase
- checkout another branch
- force push
- push
- commit

unless explicitly requested.

If unexpected local changes exist, stop and report them before overwriting anything.

Do not rewrite the application architecture.

The project is Laravel + Blade + Tailwind + Alpine. Preserve that architecture.

---

# 1. Read the Repository First

Before implementation, read the current project thoroughly.

At minimum inspect:

```text
AGENTS.md
CLAUDE.md
README.md
composer.json
package.json
routes/web.php

app/Models/User.php
app/Models/Photo.php
app/Models/Transaction.php
app/Models/Withdrawal.php
app/Models/Event.php
app/Models/Camera.php
app/Models/Package.php
app/Models/FgLocation.php
app/Models/Report.php

app/Http/Controllers/SuperAdminController.php
app/Http/Controllers/FotograferController.php
app/Http/Controllers/PembeliController.php
app/Http/Controllers/PhotoController.php
app/Http/Controllers/PurchaseDownloadController.php
app/Http/Controllers/MarketplaceController.php

resources/views/layouts/superadmin.blade.php
resources/views/superadmin/dashboard.blade.php
resources/views/superadmin/compliance.blade.php
resources/views/superadmin/ledger.blade.php
resources/views/superadmin/withdrawal.blade.php
resources/views/superadmin/storage.blade.php
resources/views/superadmin/settings.blade.php

database/migrations/**
database/factories/**
database/seeders/**
tests/**
```

Also inspect every route, field, relation, enum/status, factory, and migration used by Super Admin.

Do not assume the prompt is more accurate than the current schema. If the implementation uses a slightly different field name, adapt the implementation while preserving the business rule described here.

---

# 2. Current Architecture Facts to Preserve

The current project already has these core relationships:

```text
Buyer
  ↓
Transaction
  ↓
Photo
  ↓
Photographer
  ↓
Event / Camera / Storage

Photographer
  ↓
Withdrawal
  ↓
Super Admin approval workflow
```

The current `Transaction` model already links:

- `photo`
- `pembeli`
- `fotografer`

and contains revenue snapshot-compatible fields/accessors.

The current `Photo` business rule is fixed at:

```text
Photographer = 90%
Platform / Super Admin = 10%
```

This split is **FINAL AND IMMUTABLE** for this task.

Do not add a setting that changes the revenue percentage.

Do not use 70/30, 80/20, or any other split.

---

# 3. Core Data Relationship Rule

Everything in Super Admin must be derived from real relational data.

The primary chain is:

```text
Buyer
→ purchase / transaction
→ purchased photo
→ photographer owner
→ event / location
→ 90% photographer share
→ 10% platform share
→ photographer balance
→ withdrawal request
→ Super Admin review
```

A change in one area must remain consistent everywhere else.

Examples:

If a paid Buyer transaction is created:

- it appears in Buyer purchase history;
- it appears in the relevant Photographer order list;
- it appears in Super Admin Ledger;
- it increases GMV;
- it increases Photographer 90% earnings;
- it increases Platform 10% revenue;
- it contributes to event/location analytics;
- it may increase Photographer withdrawable balance according to the existing balance workflow.

If a withdrawal is submitted:

- it appears in Photographer Ringkasan;
- it appears in Super Admin Withdrawal Management;
- its status must remain synchronized;
- approval/rejection/hold must not produce double-balance mutations.

Do not create isolated mock data sources for different dashboards.

---

# 4. No Dummy Data in Production Views

Search all Super Admin views for hardcoded production-looking data.

Remove production-view dummy values such as:

- fake GMV
- fake Month-over-Month percentages
- fake dates
- fake SLA
- fake BI-FAST status
- fake Cloudflare status
- fake S3 status
- fake bandwidth
- fake KYC match score
- fake NIK
- fake KTP
- fake face match
- fake fraud telemetry
- fake bucket names
- fake storage regions
- fake conversion rates
- fake gateway latency
- fake settlement percentage
- fake MDR totals
- fake transaction counts
- fake approved/rejected counts
- fake charts
- fake location distribution
- fake audit hashes
- fake AWS / Cloudflare infrastructure labels
- fake operational policies

Production UI may only display:

1. values calculated from the database;
2. real application configuration;
3. real integration state;
4. explicit `Not configured`, `No data`, or empty states.

Never replace one fake number with another fake number.

---

# 5. Development Seeders Are Required

Although production views cannot contain dummy values, development/test seeders **must** be created so the whole Super Admin system can be tested realistically.

Seed data must be relational and deterministic.

Create/update development seeders to produce a coherent dataset similar to:

- 1 Super Admin
- multiple Buyers
- multiple Photographers
- verified Photographers
- pending Photographers
- rejected Photographers if rejection state is implemented
- Photographer cameras
- Events in several locations
- active/inactive Photos
- Transactions spread across multiple days
- `paid`, `pending`, `failed`, and `expired` transactions according to current status support
- correct 90/10 transaction snapshot amounts
- realistic Photographer balances
- pending Withdrawals
- held Withdrawals
- successful Withdrawals
- rejected Withdrawals
- admin audit-log examples
- per-Photographer storage usage
- settings rows

The seed graph must be connected.

Example:

```text
Buyer A
→ buys Photo 101
→ Photo 101 belongs to Photographer B
→ Photo 101 belongs to Event C
→ Transaction stores 90/10 snapshot
→ Photographer B receives earnings
→ Photographer B later creates Withdrawal D
→ Super Admin reviews Withdrawal D
```

Do not rely on remote Unsplash/network resources for required seeder behavior.

Seeders are only for local/development/test environments. Never use seeded values as hardcoded production dashboard metrics.

---

# 6. Super Admin Navigation — Final Scope

Keep the Super Admin workspace structured around:

```text
Command Center
Photographer Compliance
Transaction Ledger
Creator Withdrawals
Storage Management
Platform Settings
```

Do not add fake operational modules just because they look impressive.

Every visible button must either:

- work,
- navigate somewhere real,
- submit a real action,
- open a real filter,
- or be intentionally non-interactive text.

There must be no decorative button that does nothing.

---

# 7. Command Center — Executive Dashboard

Route:

```text
/superadmin/dashboard
```

The Command Center must become fully database-driven.

## 7.1 Executive PDF Report — ACTIVATE

Activate:

```text
Download Executive Report (PDF)
```

The report should support a useful date range and contain real data such as:

- date range
- GMV
- number of transactions
- paid transaction count
- Photographer 90% share
- Platform 10% share
- pending withdrawal amount
- successful withdrawal amount
- active Photographer count
- storage utilization summary
- daily transaction/GMV table
- location/event distribution summary

Use real database queries.

Do not include fake infrastructure telemetry.

If the repository already has a PDF library, reuse it.

If generating a proper PDF requires installing a new Composer package, STOP and ask before installing the dependency.

Do not fake a `.pdf` by renaming HTML.

---

# 8. Command Center Metrics — Real DB + Seeders

Keep the important metric cards, but drive them from the database.

Required real metrics:

### GMV

Use transactions according to the app's business definition.

Prefer paid transaction GMV for financial reporting unless the current project explicitly defines total GMV differently.

Be consistent across Command Center and Ledger.

### Platform Revenue

Use actual transaction platform snapshot amounts:

```text
10%
```

Prefer `platform_amount` / transaction snapshot values over recalculating historical orders from current Photo price.

### Photographer Revenue

Use actual transaction Photographer snapshot amounts:

```text
90%
```

### Pending Withdrawals

Count and sum pending withdrawal requests.

### Photographer Count

Use real Photographer records.

### Storage

Use real user/photo storage usage.

No static TB values.

---

# 9. Remove Fake MoM and Replace with Real Daily/Period Comparison

Current hardcoded values such as:

```text
+22.4% MoM
vs Apr Rp...
SLA <2 hours
```

must be removed.

Where comparison is useful, calculate it from real transaction data.

Implement period comparison safely, for example:

```text
selected period
vs
previous equivalent period
```

Handle zero previous-period values correctly.

Do not show misleading percentages when the denominator is zero.

Seeder data must include transactions across multiple dates so this can be tested.

---

# 10. Daily Transaction & GMV Chart — ACTIVATE

Replace the mock SVG chart with a real daily chart.

Default:

```text
last 30 days
```

or selected dashboard date range.

Group paid transactions by calendar day.

At minimum provide:

```text
date
transaction count
GMV
platform revenue
photographer revenue
```

The visual chart must be generated from real data.

Prefer a lightweight solution.

Do not install a large chart framework unless necessary.

A minimal Blade + SVG/Canvas/native solution is preferred.

If there is no data, render a clean empty state instead of a fake graph.

---

# 11. Real Geographic / Event Distribution

Replace the hardcoded location values with real grouping from Event/location data.

Audit whether the source of truth is:

- Event location field,
- `FgLocation`,
- or another current relation.

Use the actual project schema.

Recommended metric:

```text
number of paid photo purchases grouped by event/location
```

Also expose the sold-photo count and percentage of total paid sales.

Do not hardcode locations in the Blade view.

Seeders should create several Events/locations so the distribution is meaningful in development.

---

# 12. Remove Bib & Face Search Conversion

Completely remove the current dummy:

```text
Bib & Face Search Conversion
```

from Super Admin.

Do not create a fake face-search analytics subsystem.

Do not create biometric metrics for this task.

---

# 13. Integration Status — Keep the Section, Remove Fake Telemetry

The current fake telemetry such as:

```text
API BI-FAST 99.98%
QRIS Webhook Connected
S3 JKT Healthy
Security Audit 0 Anomaly
latency in milliseconds
```

must not remain as fabricated numbers.

However, keep a compact **Integration Status** section.

It may show only real state derived from current configuration, for example:

```text
Database: Connected
Payment Mode: Simulated / Midtrans Configured / Not Configured
Storage Driver: local / s3
Queue Driver: sync / database / redis
Mail: Configured / Not Configured
```

Do not perform external destructive calls just to populate this section.

If an integration is unavailable, say:

```text
Not configured
```

Do not invent uptime or latency.

---

# 14. Photographer Compliance — Make It Real

Route:

```text
/superadmin/compliance
```

The page must stop pretending to be a full government KYC/biometric system.

It should become a real **Photographer account verification and moderation page** based on information the application actually stores.

---

# 15. Compliance Filters, Tabs, and Search — ACTIVATE

Activate:

- search by Photographer name
- email
- studio/brand if available
- category
- verification status

Tabs/filters should be database-backed.

Counts must be real.

Implement query-string filters so URLs are shareable and paginated filters persist.

---

# 16. Fix Photographer Approval Bug

The existing approval flow currently posts to the verify route.

Reproduce the current bug before changing it.

Create/update tests that demonstrate the expected behavior.

Expected approval flow:

```text
pending Photographer
→ Super Admin clicks Approve
→ authorization validated
→ Photographer becomes verified/approved
→ verified timestamp stored if schema supports it
→ status visibly updates
→ Photographer disappears from pending queue
→ admin audit event is written
```

Preserve backward compatibility with existing `is_verified` behavior if that field is already used elsewhere.

Do not break Photographer login or existing authorization.

---

# 17. Reject Photographer + Reason — ACTIVATE

Add a real rejection workflow.

Super Admin must be able to:

```text
Reject Photographer
→ enter required reason
→ confirm
→ Photographer verification state becomes rejected
→ reason stored
→ audit log stored
```

Use a non-destructive migration if new fields are required.

Suggested concepts:

```text
verification_status
verification_rejection_reason
verified_at
rejected_at
reviewed_by
```

But first inspect current schema and reuse existing fields when appropriate.

---

# 18. Remove KYC Batch ZIP

Remove:

```text
Download Batch Documents (ZIP)
```

Do not build this feature.

Remove the dead button and related fake UI.

---

# 19. Admin Verification Audit Log — ACTIVATE

Create a real admin audit log system or extend an existing one.

At minimum record:

```text
actor Super Admin
action
target type
target id
before state when useful
after state when useful
reason / note
timestamp
```

Must include:

- approve Photographer
- reject Photographer
- hold Withdrawal
- approve Withdrawal
- reject Withdrawal
- batch approve Withdrawals
- storage quota change
- settings change

Create seed data for audit log examples.

The Compliance page should allow viewing relevant verification history.

Do not show fake SHA-256 audit strings.

---

# 20. Remove Fake KTP / NIK / Dukcapil / Face Match UI

Remove the current mock KTP panel and fabricated personal details.

Remove:

- sample KTP
- hardcoded NIK
- hardcoded address
- `DUKCAPIL VERIFIED`
- fake selfie
- fake match percentages
- fake biometric verification
- fake hash
- fake AI match text

Do not pretend JepretCFD is integrated with Dukcapil unless such integration actually exists.

---

# 21. Real Photographer Review Panel

Replace fake KYC/biometric content with useful real Photographer context.

For the selected/pending Photographer, show real:

- name
- email
- studio/brand if available
- location
- category
- profile photo
- joined date
- verification state
- number of Photos
- number of Events
- recent Photos
- Cameras
- total paid sales if appropriate
- current Package
- storage usage

The sample Photo section must use Photos actually owned by that Photographer.

The camera section must use Camera records actually owned by that Photographer.

No Unsplash.

No static camera data unless the Photographer actually has it.

---

# 22. Transaction Ledger — Fully Activate

Route:

```text
/superadmin/orders
```

This must be the authoritative Super Admin transaction ledger.

It must connect:

```text
Buyer
Transaction
Photo
Photographer
Event
Revenue snapshot
Payment state
```

---

# 23. Ledger Search — ACTIVATE

Activate real search across fields that exist, such as:

- transaction/order reference
- Buyer name
- Buyer email
- Photographer name
- Event name
- Photo title

Use efficient query construction.

Use eager loading.

Avoid N+1.

Preserve search query across pagination.

---

# 24. Ledger Date Filter — ACTIVATE

Replace the hardcoded date with a real date-range filter.

Support:

```text
from
to
```

Default can be current month or last 30 days.

Metrics at the top of the Ledger should respect the selected date range.

---

# 25. Payment Gateway Filter — DEFER

Do not activate a gateway filter yet.

Midtrans support may be expanded later.

Remove/hide the non-functional filter control from the current UI.

Architect queries so adding:

```text
gateway=midtrans
```

later is easy, but do not build fake gateway selection now.

---

# 26. Export Ledger — ACTIVATE

Activate:

```text
Export Ledger
```

Use CSV unless the repository already includes a safe spreadsheet library.

CSV should export filtered results, not always the entire database.

Include useful columns such as:

```text
Transaction ID
Created At
Paid At
Buyer Name
Buyer Email
Photographer Name
Photo ID
Photo Title
Event
Location
Photo Price
Tip
Photographer Amount (90%)
Platform Amount (10%)
Payment Fee / MDR
Payment Method
Status
```

Only include fields that actually exist or are added as part of the real transaction model.

Make it Excel-friendly:

- UTF-8 BOM
- correct escaping
- stable column order
- numeric currency values

Protect against CSV formula injection for user-controlled strings beginning with:

```text
=
+
-
@
```

No new Excel package is required for CSV.

---

# 27. Transaction Status — Keep Current Taxonomy

Do not invent refund/dispute infrastructure in this task.

Use the transaction states currently supported by the application.

For example, if the current project supports:

```text
paid
pending
failed
expired
```

keep those statuses.

Do not render every row as a generic settled state unless that is actually the row state.

Status badges must reflect real data.

---

# 28. MDR / Payment Fee — Make It Real, Never Hardcoded

Remove hardcoded values such as:

```text
0.7%
fixed MDR totals
settlement percentages
```

MDR/payment fee may remain as a real metric, but it must come from stored transaction/payment data.

Audit the current payment schema.

If the project does not currently store actual payment fee:

add a non-destructive, future-compatible field only if necessary, for example conceptually:

```text
payment_gateway
payment_method
payment_fee_amount
```

Do not assume these exact names if equivalent fields already exist.

The checkout/payment flow should snapshot the actual fee used for the transaction.

For local simulated payments, seed/factory data may populate deterministic test fees.

If a transaction has no known fee:

display:

```text
Not recorded
```

or zero according to the accounting semantics.

Never fabricate a gateway fee in the view.

Gateway filtering remains deferred even if fee data is stored.

---

# 29. Creator Withdrawal Management

Route:

```text
/superadmin/earnings
```

This remains the Super Admin withdrawal/payout management page.

The Photographer-side withdrawal form must feed this exact workflow.

Do not create a second payout system.

---

# 30. Approve Withdrawal — Keep and Harden

The existing single-approve feature remains.

Make it safe and idempotent.

Expected:

```text
pending/held withdrawal
→ Super Admin approve
→ status success
→ approved metadata recorded
→ audit log recorded
```

Prevent:

- approving an already successful Withdrawal
- double balance mutation
- repeated POST side effects

Use DB transaction/locking where money state is changed.

---

# 31. Hold Withdrawal — ACTIVATE

Activate the current `Hold` button.

Add a real state such as:

```text
held
```

or use the cleanest current status architecture.

Hold should:

- keep the request open;
- not complete payout;
- not refund/duplicate funds;
- record who held it;
- optionally record a reason/note;
- create an audit log.

Allow a held request to later be approved or rejected.

---

# 32. Reject Withdrawal + Balance Restoration — REQUIRED

Add:

```text
Reject
```

with required rejection reason.

Audit the current Photographer withdrawal workflow carefully.

If the balance is deducted/reserved at withdrawal-request creation, rejecting the request must restore the correct amount **exactly once**.

If the system uses reserved balance instead, clear/release the reservation exactly once.

Use a database transaction and locking.

Never allow:

- double refund;
- negative balance;
- rejection after success;
- repeated rejection balance credit.

Record:

- rejection reason
- rejected by
- rejected at
- audit log

The Photographer must see the updated status in their own interface.

---

# 33. Batch Withdrawal Approval — ACTIVATE

Activate multi-select and:

```text
Approve Selected
```

or equivalent.

The current fake `select all` UI must become real.

Requirements:

- only pending/eligible items selectable;
- CSRF protection;
- authorization;
- backend validation;
- transaction-safe processing;
- each Withdrawal processed idempotently;
- failed item does not silently corrupt remaining balance;
- results reported clearly.

Do not call a fake BI-FAST API.

For now "approve" means the internal JepretCFD withdrawal reaches the current success state according to the app's existing payout model.

---

# 34. Remove Auto-Disbursement / BI-FAST Simulation

Remove:

- `Auto-Disbursement Schedule`
- `Ping BI-FAST`
- fake latency
- fake BI-FAST online labels
- fake RTGS/instant labels
- fake banking API health
- automatic daily batch statements

Do not implement them in this task.

Keep the payout workflow internal/manual until a real disbursement provider is added.

---

# 35. Full Withdrawal History — ACTIVATE

Replace `href="#"` links such as:

```text
View Full Ledger
```

with a real destination.

The Withdrawal page should support viewing:

```text
All
Pending
Held
Success
Rejected
```

through real query parameters or a dedicated history route.

Pagination must preserve the filter.

---

# 36. Remove Fake Withdrawal Policy Cards

Remove the current static claims about:

- minimum withdrawal as hardcoded page copy
- maximum daily withdrawal
- automatic payout schedules
- transfer subsidy
- BI-FAST rules

Do not display a policy unless it is backed by persisted Platform Settings.

The minimum withdrawal will be a real configurable setting described later.

No "Edit Parameter" button on the Withdrawal page.

---

# 37. Storage Management — Use Real Application Storage Data

Route:

```text
/superadmin/storage
```

Keep real total storage usage.

Remove fake infrastructure simulation.

---

# 38. Remove Storage/Cloud Dummy Infrastructure

Delete UI-only fake values and claims such as:

- AWS Jakarta cluster
- Cloudflare Tier-1
- Glacier
- fake global bucket allocation
- fake RAW totals
- fake WebP cache
- fake bandwidth
- fake CDN hit ratio
- fake encryption state
- fake multi-region failover
- fake S3 bucket names
- fake CDN latency
- fake checksum audit
- lifecycle savings percentage
- fake region health
- fake event buckets

Do not show external cloud services unless the actual app is configured to use them and real data is available.

---

# 39. Remove Storage Operational Buttons

Remove the currently non-functional:

- Purge Edge Cache
- Test Node Latency
- Download Telemetry
- Configure Lifecycle Rules
- SHA-256 Infrastructure Audit

Do not build them.

---

# 40. Replace Storage Table with Per-Photographer Usage

The primary Storage table should become real and operational.

Each row should represent a Photographer.

Suggested real columns:

```text
Photographer
Package
Effective Quota
Used Storage
Remaining Storage
Usage %
Photo Count
Last Upload
Quota Source
Action
```

Use real fields such as `storage_terpakai_mb` and Package quota based on the current schema.

No fake bucket URL.

---

# 41. Admin Storage Quota Override — ACTIVATE

Allow Super Admin to change a Photographer's storage quota manually.

Do this without breaking Package defaults.

Recommended architecture:

```text
package quota = normal default
optional photographer override = nullable
effective quota = override ?? package quota
```

Use the cleanest implementation based on current schema.

A quota update must:

- validate a positive value;
- never delete existing photos;
- not silently reduce quota below used storage without warning;
- write an admin audit log;
- immediately affect Photographer upload eligibility.

The Photographer Storage UI may continue using its current design but should reflect the effective quota.

---

# 42. Platform Settings — Make It Persisted and Minimal

Route:

```text
/superadmin/settings
```

The current page contains many fake controls.

Replace it with a small, real settings system.

Use a persistent database-backed settings model/table unless an equivalent already exists.

Do not store operational settings only in Blade/JS.

Add validation and typed accessors/service where appropriate.

---

# 43. Revenue Split — FIXED 90/10, READ-ONLY

Show:

```text
Photographer 90%
Platform 10%
```

as informational configuration.

Do not render an editable slider.

Do not allow Super Admin to modify it.

The source of truth remains application business logic and transaction snapshots.

---

# 44. Minimum Withdrawal Setting — ACTIVATE

Make:

```text
minimum withdrawal amount
```

a real persisted Platform Setting.

Photographer withdrawal validation must read the same setting.

Super Admin settings display/edit must use the same source.

Default seed value may be:

```text
100000
```

only as initial development/default configuration.

Do not hardcode this number independently in multiple controllers/views.

---

# 45. System Watermark Settings — ACTIVATE

Connect Settings to the system watermark pipeline used by Photographer photo uploads/public previews.

The system watermark asset remains conceptually:

```text
watermarksistemjepret.png
```

Support useful real settings such as:

- current system watermark image
- opacity
- relative scale
- position

Keep the scope practical.

The same settings must be read by Photo processing.

Do not build fake Cloudflare/WASM renderer controls.

Do not expose Photographer personal watermark in public marketplace previews if the Photographer flow specifies system-watermark-only previews.

---

# 46. EXIF Privacy Settings — ACTIVATE

Persist and enforce:

```text
strip GPS metadata from public/generated images
retain camera optics/exposure metadata
```

These toggles must affect actual processing, not merely the Settings UI.

Audit Intervention Image and current metadata workflow.

If a format cannot support a setting, handle it safely and document the limitation.

---

# 47. Settings Audit History — Recommended and Should Use the Same Audit System

The current `Configuration Change History` concept can remain only if it is real.

Use the admin audit log.

When settings change, record:

- actor
- setting keys changed
- previous values
- new values
- timestamp

Do not show fake daemon/version labels.

---

# 48. Payment Webhook Test — DEFER

Do not implement:

```text
Test Webhook Ping
```

now.

Remove/hide the button until the Midtrans production integration is ready.

---

# 49. Remove Sensitive/Fake Root Controls

Remove from Settings:

- Rotate Production API Key
- Emergency Maintenance Mode
- Require Super Admin 2FA
- Super Admin Session Timeout control

Do not implement these in this task.

This does not mean security should be weakened. Normal authentication, authorization, CSRF, validation, session security, and policies must remain intact.

---

# 50. Real-Time / Near-Real-Time Admin Data

Where useful, Super Admin should reflect new application data without relying on hard refreshes.

Do not install Reverb/Pusher automatically.

Preferred lightweight approach:

```text
secure polling every 15–30 seconds
```

for small summary counts such as:

- pending Photographer approvals
- pending Withdrawals
- recent paid Transactions

Only add polling where it improves usability.

Endpoints must require:

```text
auth
role:superadmin
```

Do not accept a role/user ID from the frontend as authorization.

If the current application already has a broadcast/realtime infrastructure, reuse it.

---

# 51. Buyer ↔ Photographer ↔ Super Admin Consistency

Audit these cross-role flows end to end.

## Purchase Flow

```text
Buyer adds photo
→ Buyer checkout
→ transaction/order is created
→ payment changes transaction status
→ paid transaction remains in Buyer Purchases
→ Photographer sees only their related sale
→ Super Admin Ledger sees the same transaction
```

## Revenue Flow

```text
Transaction paid
→ historical transaction snapshot stores Photographer amount
→ historical transaction snapshot stores Platform amount
→ 90 / 10 remains immutable
→ Super Admin metrics aggregate snapshots
→ Photographer metrics aggregate snapshots
```

## Withdrawal Flow

```text
Photographer eligible balance
→ Photographer submits withdrawal
→ Super Admin sees pending withdrawal
→ Super Admin holds / approves / rejects
→ Photographer sees same state
→ balance/reservation stays consistent
```

## Storage Flow

```text
Photographer Package
→ default quota
→ optional Super Admin override
→ effective quota
→ Photographer upload validation
→ Super Admin Storage table
```

## Verification Flow

```text
Photographer account
→ pending verification
→ Super Admin approve/reject
→ state is visible consistently
→ audit trail exists
```

Do not allow different pages to compute contradictory values.

---

# 52. Seeder Design — Cross-Role Demonstration

Create a dedicated development scenario that proves every module is connected.

Example deterministic scenario:

```text
Super Admin:
admin@jepret.test

Photographer 1:
verified
Starter/Creator package
owns multiple Events
owns multiple Photos
has paid sales
has storage usage
has pending Withdrawal

Photographer 2:
verified
different city
different sales

Photographer 3:
pending verification

Photographer 4:
rejected verification

Buyer 1:
has several paid purchases

Buyer 2:
has pending/failed/expired transactions
```

Spread paid transactions across at least 30 days so the daily chart works.

Spread Events across multiple locations so regional distribution works.

Seed Withdrawal statuses:

```text
pending
held
success
rejected
```

Ensure financial amounts reconcile.

For every seeded paid transaction:

```text
photographer_amount + platform_amount
=
photo price (+ tip handling according to existing business logic)
```

respecting the project's exact transaction rules.

---

# 53. Admin Audit Log — Additional Recommendation

Use a generic model/table rather than building separate history tables for each module.

Suggested concept:

```text
admin_audit_logs

id
admin_id
action
subject_type
subject_id
metadata/json
created_at
```

Adapt naming to project conventions.

Useful actions:

```text
photographer.approved
photographer.rejected
withdrawal.held
withdrawal.approved
withdrawal.rejected
withdrawal.batch_approved
storage.quota_changed
settings.updated
report.exported
ledger.exported
```

Do not store secrets in audit metadata.

---

# 54. Financial Integrity — Additional Recommendation

All money mutations must happen server-side.

Never trust:

- amount from JavaScript
- 90/10 split from form input
- withdrawal balance from browser
- platform fee from UI

Use integer IDR amounts.

Prefer DB transactions for:

- payment settlement
- withdrawal submission
- withdrawal approval
- withdrawal rejection/refund
- batch withdrawal processing

Where race conditions are possible, use database row locking appropriately.

---

# 55. Snapshot Integrity — Additional Recommendation

Historical transactions must never change because a Photographer later changes Photo price.

Super Admin Ledger and reports must use transaction snapshot values:

```text
harga_foto
photographer_amount
platform_amount
tip_amount
payment_fee_amount
```

as available in the final schema.

Do not recalculate a historical transaction from the current Photo price.

---

# 56. Avoid N+1 and Expensive Dashboard Queries

Use:

- eager loading
- aggregate SQL
- groupBy
- selectSub/withCount where appropriate

Do not load every paid Transaction into PHP just to calculate simple sums if SQL aggregation can do it safely.

Paginate large tables.

Recommended indexed/query fields should be reviewed, especially:

- transaction status
- transaction created_at
- transaction paid_at
- transaction fotografer_id
- transaction pembeli_id
- withdrawal status
- withdrawal fotografer_id
- user role
- verification state
- photo fotografer_id
- photo event_id

Add indexes through non-destructive migrations when clearly useful.

---

# 57. UI Rule — No Dead Controls

After this refactor, perform a visual audit.

For every:

```text
button
link
toggle
select
tab
search box
date field
checkbox
action icon
```

ask:

```text
Does this do something real?
```

If the answer is no:

- implement it if it is included in this specification;
- otherwise remove it.

Do not leave disabled-looking fake production controls unless the UI clearly labels them as unavailable.

---

# 58. Super Admin UI Design

Keep the current overall Super Admin visual direction:

- black
- white
- gray
- compact operations console
- professional
- information dense
- responsive

Do not redesign it into the public Awwwards-style website.

However:

- remove fake technical jargon;
- improve hierarchy;
- improve empty states;
- improve mobile overflow;
- keep action confirmation clear;
- make destructive/rejection actions explicit;
- keep tables readable.

---

# 59. Required Confirmation UX

Use confirmation dialogs/forms for important operations:

- reject Photographer
- approve Withdrawal
- reject Withdrawal
- batch approve Withdrawals
- storage quota override
- settings changes

For rejection, reason is mandatory.

For batch approval, show:

```text
number selected
total amount
```

before final confirmation.

---

# 60. Authorization

Every Super Admin mutation must be protected by:

```text
auth
role:superadmin
CSRF
backend authorization
validation
```

Do not rely on hidden fields or disabled buttons for security.

Buyer must never access Super Admin routes.

Photographer must never access Super Admin routes.

Super Admin queries may see platform-wide data, but related model access must be null-safe.

---

# 61. PDF, CSV, and Data Export Security

Exports must respect the active filters.

Do not expose:

- passwords
- authentication tokens
- payment secrets
- Google OAuth secrets
- API keys
- raw private Photo storage paths
- unnecessary personal secrets

CSV must be formula-injection safe.

PDF should show only the operational information needed for reporting.

---

# 62. Migration Strategy

All schema changes must be non-destructive.

Do not drop existing columns/tables as part of this task unless absolutely necessary and explicitly approved.

Possible new concepts may include:

- Photographer verification metadata/status
- verification rejection reason
- admin audit logs
- Platform Settings
- storage quota override
- payment fee snapshot
- Withdrawal hold/rejection metadata

First inspect existing migrations before adding any of these.

Reuse existing columns when possible.

---

# 63. Testing — Mandatory

Add/update feature/unit tests covering the real workflows.

## Photographer Verification

Test:

```text
pending Photographer appears
approve works
approve bug is fixed
approved disappears from pending
reject requires reason
reject stores reason
audit log written
```

## Ledger

Test:

```text
search Buyer
search Photographer
search transaction ID
date range
pagination
90/10 values
CSV export
CSV injection protection
```

## Dashboard

Test:

```text
GMV from real transactions
Platform revenue from snapshots
Photographer revenue from snapshots
daily chart data
regional distribution
pending withdrawals
no fake metrics
```

## Withdrawal

Test:

```text
approve
hold
approve held
reject
reject reason
balance restoration exactly once
cannot approve successful
cannot reject successful
batch approval
audit log
```

## Storage

Test:

```text
per-Photographer usage
package quota
quota override
effective quota
upload eligibility reacts to override
cannot silently set quota below used amount
```

## Settings

Test:

```text
minimum withdrawal persists
Photographer validation reads same setting
revenue is not editable
watermark settings persist
EXIF settings persist
audit log written
```

## Cross-role

Test:

```text
Buyer paid transaction
→ Buyer sees purchase
→ Photographer sees sale
→ Super Admin sees same transaction

Photographer Withdrawal
→ Super Admin sees request
→ Super Admin action
→ Photographer sees updated state
```

---

# 64. Run the Full Project Verification

After implementation run the relevant commands, including:

```bash
php artisan migrate --no-interaction
vendor/bin/pint --dirty
php artisan test --compact
npm run build
php artisan route:list --except-vendor
```

If the project has additional CI/lint commands, run them.

Do not declare success if tests/build fail.

---

# 65. Implementation Order

Follow this order to reduce regressions.

## Phase 1 — Audit

Report:

- all dummy values;
- all dead buttons;
- all current routes;
- current schema;
- current transaction status model;
- current withdrawal balance behavior;
- current verification behavior;
- current storage quota behavior;
- current settings persistence;
- current report/export dependencies.

## Phase 2 — Data Model & Seeders

Implement only required non-destructive migrations.

Create:

- relational development seeders;
- admin audit log;
- platform settings;
- required verification/withdrawal metadata;
- quota override/payment fee fields only if needed.

## Phase 3 — Command Center

Implement:

- real metrics;
- real period comparison;
- real daily chart;
- real location distribution;
- real pending queues;
- Integration Status;
- PDF report.

## Phase 4 — Photographer Compliance

Implement:

- real search/filter;
- approval bug fix;
- rejection with reason;
- real Photographer context;
- camera/photo samples;
- audit history;
- remove fake KYC/biometric data.

## Phase 5 — Transaction Ledger

Implement:

- search;
- date range;
- real status badges;
- real MDR/payment fee;
- CSV export;
- remove fake gateway controls.

## Phase 6 — Withdrawals

Implement:

- single approve hardening;
- hold;
- reject/refund;
- batch approve;
- full history;
- remove fake BI-FAST and policy simulation.

## Phase 7 — Storage

Implement:

- real totals;
- per-Photographer usage;
- quota override;
- remove all fake cloud telemetry.

## Phase 8 — Platform Settings

Implement:

- persisted minimum Withdrawal;
- read-only fixed 90/10;
- system Watermark settings;
- EXIF settings;
- settings audit history;
- remove fake root/security/payment controls.

## Phase 9 — Cross-Role Validation

Verify Buyer, Photographer, and Super Admin see consistent Transaction, revenue, balance, Withdrawal, verification, and storage state.

## Phase 10 — QA

Run tests/build and manually inspect every Super Admin page.

---

# 66. Explicit Decisions — Do Not Re-Ask

The following decisions are already approved.

### Activate

- Executive PDF Report
- DB-driven realtime/near-realtime Command Center
- Development seeders
- real period comparison
- daily transaction chart
- real region/event distribution
- Compliance filters/search
- Photographer approve flow, with bug fix
- Photographer reject with reason
- KYC/admin audit log
- real Photo/Camera review data
- Ledger search
- Ledger date filter
- Ledger export
- real MDR/payment-fee metric
- Withdrawal single approval
- Withdrawal hold
- Withdrawal reject/refund
- Withdrawal batch approve
- full Withdrawal history
- real storage totals
- per-Photographer storage table
- admin quota override
- persisted Platform Settings
- configurable minimum Withdrawal
- configurable system Watermark
- EXIF privacy settings

### Remove

- Bib & Face Search Conversion
- KYC Batch ZIP
- fake KTP/NIK/Dukcapil/face-match UI
- auto-disbursement
- BI-FAST ping/simulation
- fake Withdrawal policy cards
- fake cloud/storage infrastructure data
- Purge Cache
- Test Latency
- Telemetry Download
- editable 90/10 split
- API key rotation
- Maintenance Mode toggle
- Super Admin 2FA UI
- Session timeout UI

### Defer

- Payment Gateway filter, for future Midtrans work
- Webhook test button, for future Midtrans work

### Keep but make real/minimal

- Integration Status section:
  keep the section, remove all fabricated uptime/latency/health values.

---

# 67. Extra Recommendations Approved by This Prompt

Apply these additional engineering improvements because they directly support the requested behavior:

1. **Generic Admin Audit Log** instead of separate history tables.
2. **Withdrawal idempotency + DB locking** to protect balances.
3. **Transaction snapshot-first reporting** for historical accuracy.
4. **Storage quota override nullable architecture** so Package defaults remain intact.
5. **Single Platform Settings source** used by both Super Admin and Photographer validation.
6. **Deterministic relational seeders** instead of Blade dummy values.
7. **SQL aggregation/eager loading/index review** for scalable admin queries.
8. **CSV formula-injection protection**.
9. **No production UI metric unless it can be traced to DB/configuration**.
10. **Cross-role tests** so Buyer → Photographer → Super Admin data always reconciles.

---

# 68. Stop Conditions

Stop and ask before:

- installing a new PDF Composer package;
- adding Laravel Reverb/Pusher;
- changing payment provider architecture;
- enabling real Midtrans production credentials;
- changing storage provider;
- deleting existing database tables/columns;
- changing the fixed 90/10 revenue model;
- adding government identity/KYC integrations;
- adding real bank disbursement APIs;
- introducing a large frontend framework/library.

---

# 69. Final Acceptance Criteria

The task is complete only when:

- no important Super Admin metric is hardcoded;
- no visible operational button is dead;
- no fake KYC/biometric information remains;
- no fake cloud telemetry remains;
- no fake BI-FAST behavior remains;
- Photographer approval works;
- Photographer rejection works with reason;
- audit logs are real;
- Ledger search/date/export work;
- Ledger uses real transaction status and 90/10 snapshot values;
- payment fee/MDR is real or explicitly unavailable;
- Withdrawal approve/hold/reject/batch are safe;
- rejected Withdrawals restore/release balance exactly once;
- storage is real and shown per Photographer;
- quota override affects actual upload eligibility;
- Settings persist and are actually consumed by application logic;
- fixed 90/10 cannot be edited;
- minimum Withdrawal uses one source of truth;
- watermark/EXIF settings affect Photo processing;
- seeders produce a coherent Buyer ↔ Photographer ↔ Order/Transaction ↔ Withdrawal dataset;
- all tests pass;
- frontend build passes;
- routes are valid.

---

# 70. Final Codex Report

When finished, provide:

```text
AUDIT SUMMARY

FILES CHANGED

MIGRATIONS ADDED

MODELS / RELATIONS CHANGED

SEEDERS ADDED

COMMAND CENTER CHANGES

PDF REPORT STATUS

COMPLIANCE CHANGES

APPROVAL BUG ROOT CAUSE

REJECTION FLOW

AUDIT LOG ARCHITECTURE

LEDGER SEARCH/FILTER CHANGES

CSV EXPORT STATUS

MDR / PAYMENT FEE SOURCE

WITHDRAWAL STATE MACHINE

WITHDRAWAL BALANCE SAFETY

STORAGE CHANGES

QUOTA OVERRIDE LOGIC

PLATFORM SETTINGS

WATERMARK SETTINGS INTEGRATION

EXIF SETTINGS INTEGRATION

CROSS-ROLE DATA FLOW

REMOVED DUMMY UI

DEFERRED MIDTRANS ITEMS

TEST RESULTS

BUILD RESULT

KNOWN LIMITATIONS
```

Do not commit or push unless explicitly requested.

Start with **Phase 1 — Audit**, then implement phase by phase.
