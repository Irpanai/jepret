# JEPRET — Photographer Creator Center Refactor & Activation Prompt for Codex

## Project

Repository: `https://github.com/Irpanai/jepret`  
Repository name: `Irpanai/jepret`  
Target branch: `main`

The IDE is already on `main` and `main` is already up to date.

You are acting as a **Senior Laravel Engineer, Software Architect, Product Engineer, Database Engineer, QA Engineer, Security Reviewer, and Photographer Workflow Engineer**.

Your task is to refactor and activate the entire **Photographer Creator Center** so it stops behaving like a visual demo and becomes a real, relational, production-oriented Photographer workspace.

This task must stay consistent with the current Buyer flow and the Super Admin implementation described in `superadmin.md`.

The system must behave as one connected product:

```text
Buyer
→ Purchase / Transaction
→ Photo
→ Photographer
→ Earnings / Balance
→ Withdrawal
→ Super Admin
```

This is an implementation task, not a UI mockup task.

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

- run `git pull`
- reset
- rebase
- checkout another branch
- force push
- push
- commit

unless explicitly requested.

If unexpected local changes exist, stop and report them before overwriting anything.

Do not rewrite the framework architecture.

The project uses:

```text
Laravel
Blade
Tailwind CSS
Alpine.js
```

Preserve the existing architecture.

---

# 1. Read the Repository First

Before implementation, read and understand the latest repository.

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

app/Http/Controllers/FotograferController.php
app/Http/Controllers/PhotoController.php
app/Http/Controllers/PembeliController.php
app/Http/Controllers/PurchaseDownloadController.php
app/Http/Controllers/SuperAdminController.php
app/Http/Controllers/MarketplaceController.php

resources/views/layouts/fg.blade.php

resources/views/fotografer/dashboard.blade.php
resources/views/fotografer/orders.blade.php
resources/views/fotografer/earnings.blade.php
resources/views/fotografer/storage.blade.php
resources/views/fotografer/portfolio.blade.php

resources/views/fotografer/photos/**
resources/views/fotografer/events/**
resources/views/fotografer/cameras/**

resources/views/superadmin/withdrawal.blade.php
resources/views/superadmin/storage.blade.php
resources/views/superadmin/settings.blade.php

resources/views/purchases/**
resources/views/checkout/**
resources/views/galeri.blade.php

resources/css/app.css
resources/js/app.js

database/migrations/**
database/factories/**
database/seeders/**
tests/**
```

Read `superadmin.md` if it exists in the repository root.

Photographer behavior must remain compatible with the Super Admin rules defined there.

---

# 2. Core Business Rules — Final

These rules are non-negotiable.

## Revenue Split

```text
Photographer = 90%
Super Admin / Platform = 10%
```

This is fixed.

Do not create a Photographer setting that can change it.

Do not use 70/30 or any other split.

Historical Transactions must use stored transaction snapshot values.

---

# 3. Cross-Role Relationship — Source of Truth

The Photographer Creator Center is not an isolated module.

Everything must stay connected to Buyer and Super Admin.

## Buyer Purchase Flow

```text
Buyer
→ Galeri
→ Cart
→ Checkout
→ Payment
→ Transaction becomes Paid
→ Buyer can download purchased file
```

When the Transaction becomes `paid`:

```text
same transaction
→ appears in Buyer Purchases
→ appears in Photographer Orders
→ increases Photographer sales metrics
→ increases Photographer earnings/balance according to current financial logic
→ appears in Super Admin Ledger
→ contributes to Platform 10% reporting
```

Do not create separate fake Photographer sales records.

The `Transaction` remains the financial source of truth.

---

# 4. Withdrawal Relationship

The Photographer withdrawal flow must use the same `Withdrawal` system used by Super Admin.

```text
Photographer
→ sees available balance
→ submits Withdrawal
→ Withdrawal status = pending
→ Super Admin sees the same Withdrawal
→ Super Admin can hold / approve / reject
→ Photographer sees the updated status
```

Never create a second Photographer-only payout table.

Read the current Super Admin withdrawal implementation and `superadmin.md`.

---

# 5. Storage Relationship

Photographer storage must remain connected to:

```text
Package default quota
+
optional Super Admin quota override
=
effective quota
```

If `superadmin.md` introduces a storage quota override, Photographer upload validation must use the **effective quota**, not only the Package quota.

Do not redesign the existing Photographer Storage page unless required for data correctness.

---

# 6. Platform Settings Relationship

Photographer behavior must consume the same persisted Platform Settings that Super Admin manages.

Examples:

```text
minimum withdrawal amount
system watermark configuration
EXIF privacy configuration
```

Do not hardcode these values separately inside Photographer controllers.

There must be a single source of truth.

---

# 7. Main Photographer Navigation — Final

The Photographer sidebar should contain:

```text
Ringkasan
Kamera
Foto
Pesanan & Transaksi
Penggunaan Storage
Profil & Portofolio
Pengaturan Akun
```

Remove the separate Photographer menu/page:

```text
Pendapatan & Pencairan
```

The earnings and withdrawal functionality must be moved into:

```text
Ringkasan
```

Do not remove financial models or Super Admin withdrawal logic.

Only remove the redundant Photographer page/menu after its useful functionality is available in Ringkasan.

---

# 8. Pages That Must NOT Be Redesigned Heavily

## Camera

The Camera feature is already acceptable.

Do not redesign or rewrite the Camera CRUD unless a small compatibility change is required for Photo upload metadata.

## Storage

Keep the current Photographer Storage page design and general behavior.

Do not redesign it.

Only change underlying effective quota calculations if required by Super Admin quota override support.

---

# 9. No Dummy Data in Photographer Views

Search:

```text
resources/views/fotografer/**
```

for:

- hardcoded names
- hardcoded money
- fake sales totals
- fake views
- fake conversion percentages
- fake charts
- fake order counts
- fake transaction rows
- `rand()`
- Unsplash portfolio images
- fake events
- fake profile stats
- fake badges
- fake status
- fake storage values
- fake recent sales

Production Photographer views may only show:

1. real database values;
2. real application configuration;
3. real integration state;
4. clear zero/empty states.

Never replace one dummy value with another.

---

# 10. Development Seeders

Photographer-related seeders must be relational and compatible with `superadmin.md`.

Seed development/test data for:

- verified Photographer
- pending Photographer
- Photographer Profile data
- Camera records
- multiple Events
- multiple Photos
- active and inactive Photos
- multiple paid Transactions
- pending/failed/expired Transactions if current status taxonomy supports them
- transaction values spread across multiple days
- storage usage
- current Package
- Photographer personal watermark
- watermark locked/unlocked scenarios
- pending Withdrawal
- held Withdrawal
- successful Withdrawal
- rejected Withdrawal

Seed values must reconcile with Buyer and Super Admin.

Example:

```text
Buyer A
→ buys Photographer B's Photo
→ Transaction Paid
→ Photographer B sees sale
→ Super Admin sees same Transaction
→ Photographer B balance updates
→ Photographer B requests Withdrawal
→ Super Admin sees that Withdrawal
```

Do not hardcode seeder values in production Blade files.

---

# 11. Ringkasan — Main Photographer Dashboard

Route should remain based on the current Photographer dashboard route.

The page title/concept:

```text
Ringkasan
```

This becomes the main Photographer operational overview.

---

# 12. Ringkasan Header

Replace any hardcoded welcome name with:

```text
authenticated Photographer name
```

Example:

```text
Welcome back, {{ photographer.name }}
```

Remove visual labels that look operational but have no backing data.

Keep useful actions such as:

```text
Upload New Photo
```

if they work.

No dead button.

---

# 13. Realtime / Near-Realtime Buyer Order Notifications

Activate Photographer order notifications.

Trigger:

```text
Transaction becomes paid
AND
Transaction photo belongs to the authenticated Photographer
```

The Photographer must never receive notifications for another Photographer's order.

Notification content should include real data:

```text
Order / Transaction ID
Buyer name or email where appropriate
Photo
Event
Sale price
Photographer 90% amount
Time
```

Notification click:

```text
→ Photographer Orders
```

or transaction detail if such route exists.

---

# 14. Realtime Implementation Rule

Do not install Laravel Reverb, Pusher, WebSockets, or another realtime package automatically.

Audit the current stack.

If there is no realtime infrastructure, use lightweight secure polling.

Recommended:

```text
new order notifications: every 10–15 seconds
dashboard metrics: every 15 seconds
recent sales: every 15 seconds
```

Use Alpine/native JS where possible.

Do not reload the whole page.

All polling endpoints must be:

```text
authenticated
role:fotografer
scoped to authenticated user
```

Never trust `fotografer_id` sent by the browser.

If the application already uses broadcasting, reuse it instead.

---

# 15. Four Main Metric Cards — Required

Ringkasan must show exactly these four primary metrics:

```text
PENDAPATAN BERSIH
FOTO TERJUAL
TOTAL KUNJUNGAN
RASIO KONVERSI BELI
```

All values must be real.

---

# 16. PENDAPATAN BERSIH

Use paid Transactions belonging to the authenticated Photographer.

Prefer stored transaction snapshot:

```text
photographer_amount
```

or the current equivalent/accessor.

Formula conceptually:

```text
SUM(paid transaction Photographer share)
```

Do not calculate from the current Photo price.

Display:

```text
Rp0
```

when no paid transactions exist.

---

# 17. FOTO TERJUAL

Definition:

```text
number of paid photo purchases belonging to this Photographer
```

Use the correct Transaction / order-item structure in the latest repository.

If one Transaction maps to one Photo, count paid Transactions.

If the repository has been refactored to order items, count paid purchased photo items.

Do not display fake capacity-like values such as:

```text
184 / 420
```

unless both values have a real meaning.

---

# 18. TOTAL KUNJUNGAN

Use actual Photo view data.

If `views_count` already exists on Photo, aggregate:

```text
SUM(views_count)
```

for Photos belonging to the Photographer.

If view tracking is incomplete, audit the public Photo Detail route and implement a safe view counter.

Do not count dashboard loads as public Photo views.

Do not show fake visit values.

---

# 19. RASIO KONVERSI BELI

Use real data.

Recommended formula:

```text
paid photo purchases / total Photo views × 100
```

for the authenticated Photographer.

Handle:

```text
views = 0
```

as:

```text
0%
```

Do not show fake platform benchmarks or fake "outperforming" labels.

---

# 20. Ringkasan Chart — Real Only

If the Photographer dashboard keeps a sales chart, it must be real.

Recommended:

```text
last 30 days
group paid Transactions by day
sum Photographer amount
```

No mock SVG values.

No fake Event names.

If there is no data, render an empty state.

Prefer a lightweight native chart.

Do not install a large chart library without approval.

---

# 21. Saldo Siap Tarik — Real and Connected to Super Admin

Add to Ringkasan:

```text
Saldo Siap Tarik
```

Use the existing real Photographer wallet/balance field or balance service.

Do not invent another balance variable.

This balance must reconcile with:

```text
paid Transactions
existing withdrawal workflow
Super Admin Withdrawal Management
```

---

# 22. Withdrawal Form in Ringkasan

Add a real withdrawal form directly to Ringkasan.

Fields:

```text
Withdrawal Amount

Method:
- Bank
- E-Wallet

If Bank:
- Bank Name
- Account Number
- Account Holder Name if supported

If E-Wallet:
- Provider
- E-Wallet / Phone Number
```

Reuse current User/withdrawal schema where possible.

Do not duplicate bank data if the Photographer profile already stores reusable payout information.

---

# 23. Minimum Withdrawal Must Come from Platform Settings

Do not hardcode minimum withdrawal in the Photographer controller/view.

Read the same persisted setting managed by Super Admin.

Example concept:

```text
platform_settings.minimum_withdrawal
```

or an equivalent service.

If the setting is missing, use a clearly defined application default in one centralized place only.

---

# 24. Withdrawal Submission Safety

When Photographer submits a withdrawal:

```text
validate amount
validate effective minimum
validate available balance
validate payout destination
create Withdrawal
status = pending
```

Then it must appear immediately in Super Admin withdrawal management.

Prevent:

- negative balance
- double submit
- submitting more than available
- duplicate rapid-click requests
- race conditions

Use database transactions and locking where balance mutation occurs.

Match the same balance semantics implemented in `superadmin.md`.

---

# 25. Withdrawal Status Display

Ringkasan should show recent withdrawal requests.

Supported final operational states should align with Super Admin:

```text
pending
held
success
rejected
```

If the current schema uses slightly different values, normalize carefully.

Photographer must see the same state Super Admin sees.

For rejected requests, show the rejection reason when appropriate.

---

# 26. Recent Sales — Realtime / Near-Realtime

Add:

```text
Penjualan Terkini
```

to Ringkasan.

Use real paid Transactions belonging to the authenticated Photographer only.

Minimum columns/content:

```text
Order ID
Photo thumbnail
Photo / Event
Buyer
Sale Price
Photographer 90% Amount
Time
```

Use:

```text
5 records per view
```

with real:

```text
Previous
Next
```

controls.

Do not hardcode counts.

Add:

```text
Lihat Semua
```

link to:

```text
/fotografer/orders
```

or the actual named route.

---

# 27. Camera — Leave As-Is

The Camera section is already acceptable.

Do not redesign it.

Do not rewrite its controller.

Only ensure Camera selection continues to work as Photo metadata.

---

# 28. Photo System — Major Refactor

Audit the full Photo pipeline before changing it.

Read:

- Photo model
- Photo migrations
- PhotoController
- Photographer Photo views
- Marketplace preview
- Buyer purchase/download flow
- Storage paths
- Image processing code
- current watermark behavior

Do not overwrite original raw files.

---

# 29. Photographer Personal Watermark — Required

Every Photographer must have a personal watermark available for purchased-download processing.

Use image watermark files, not generated text.

Prefer formats that support transparency such as:

```text
PNG
WEBP
```

if supported.

---

# 30. Photographer Watermark Lock / Unlock Logic

Implement a Photographer watermark state:

```text
LOCKED
UNLOCKED
```

Business meaning:

## LOCKED

If Photographer uploaded Watermark A and locked it:

```text
future Photo upload
→ Photographer does NOT need to upload watermark again
→ system automatically reuses Watermark A
```

## UNLOCKED

If Photographer watermark state is unlocked:

```text
future Photo upload
→ do not automatically reuse saved watermark
→ Photographer must upload/select a watermark again before successful Photo upload
```

Once a new watermark is provided, Photographer may lock it again.

This lock/unlock state is about **watermark reuse**, not Buyer access and not Photo Active status.

---

# 31. Watermark UI

Inside Photographer Photos, add a clear watermark section.

Example:

```text
Photographer Watermark

[ Watermark Preview ]

Status: Locked / Unlocked

Actions:
Upload / Replace
Lock
Unlock
```

If locked:

```text
Default watermark will be reused for new uploads.
```

If unlocked:

```text
A watermark is required for the next upload.
```

Do not mix this with Photo status controls.

---

# 32. Photographer Watermark Data Model

Inspect the schema first.

Possible architecture:

```text
users.photographer_watermark_path
users.photographer_watermark_locked
```

or a dedicated:

```text
photographer_watermarks
```

table.

Choose the cleanest design for the current project.

Use non-destructive migrations.

Do not delete existing watermark data.

---

# 33. System Watermark Asset — Required

The system watermark asset is:

```text
watermarksistemjepret.png
```

Find the local file in the project.

Do not replace it with generated text `JEPRET` when the asset exists.

System watermark settings must come from the same Super Admin Platform Settings defined in `superadmin.md`.

---

# 34. Final Watermark Pipeline

This is the final flow.

## Raw Original

```text
RAW ORIGINAL
private
never public
never directly exposed
```

## Public Marketplace Preview

Every public preview must use:

```text
SYSTEM WATERMARK ONLY
```

using:

```text
watermarksistemjepret.png
```

Do **not** show the Photographer personal watermark on the public marketplace preview.

## Paid Buyer Download

After a Buyer owns a paid Transaction:

```text
SYSTEM WATERMARK REMOVED
PHOTOGRAPHER WATERMARK PRESENT
```

The Buyer-download file is **not** the unwatermarked raw original.

Raw original stays private.

This must remain compatible with Buyer `/purchases` repeat-download authorization.

---

# 35. Paid Buyer Re-Download Compatibility

A Buyer may re-download purchased photos repeatedly from:

```text
/purchases
```

if:

```text
Transaction belongs to Buyer
AND
Transaction status = paid
```

Each authorized download must deliver:

```text
Photographer-watermarked purchased variant
```

not:

```text
system-watermarked marketplace preview
```

and not the private raw original.

Photographer-side implementation must not break this Buyer rule.

---

# 36. Watermark Scaling

Both system and Photographer watermark images must scale relative to the uploaded Photo dimensions.

Do not use one fixed pixel size for every Photo.

Preserve watermark aspect ratio.

Use a proportional size such as a configured percentage of image width.

System watermark:

- use Super Admin-configured opacity;
- use Super Admin-configured scale;
- use Super Admin-configured position.

Photographer watermark:

- preserve transparency;
- scale consistently;
- use a sensible purchase-download placement.

Do not stretch watermark assets.

---

# 37. Photo Variants

For image Photos, conceptually manage these variants:

```text
A. Raw Original
   private

B. Marketplace Preview
   system watermark
   public/protected preview

C. Purchased Download Variant
   Photographer watermark
   private / authorization required
```

Do not overwrite A.

Variant C may be:

- generated during upload; or
- generated on demand and cached.

Choose the safest practical design.

Document the choice.

---

# 38. Multi-Upload — Required

Replace single-file Photo upload with multi-upload.

Input concept:

```text
photos[]
multiple
```

Photographer may select many files.

UI must show a real upload queue:

```text
preview
filename
size
status
progress where practical
error
success
```

Do not make the user submit every file separately.

---

# 39. Required Upload Formats

User requirement:

```text
.png
.jpg
.jpeg
.web
.mov
```

Audit MIME/processing capabilities before implementation.

Important:

`.web` is not automatically assumed to mean `.webp`.

If `.web` is not a valid supported media format and the current project cannot identify it:

STOP and ask whether `.web` means `.webp`.

Do not silently rename the requirement.

JPG/JPEG/PNG are required.

MOV must be handled according to actual backend capability.

---

# 40. MOV / Video Rule

Current image-processing libraries may not support MOV watermark rendering.

Audit whether FFmpeg or equivalent is already installed and configured.

If yes:

implement MOV pipeline safely.

If not:

do not install FFmpeg automatically.

Stop and report:

```text
MOV upload storage is possible, but watermark/transcoding requires FFmpeg.
FFmpeg is not currently available.
May I add/configure it?
```

Do not fake video watermark processing.

---

# 41. No Arbitrary Laravel Per-File Size Cap

Remove the current application-level arbitrary cap such as:

```text
max:10240
```

for Photo uploads.

The requirement is:

```text
no arbitrary app-level maximum per file
```

However this does **not** mean true infinite infrastructure upload size.

The system still has:

- effective Photographer storage quota
- PHP upload limit
- web server request limit
- host limits
- memory/process constraints

If infrastructure rejects a file, show a clear error.

Do not claim the system has unlimited upload capacity.

---

# 42. Storage Quota Still Applies

Multi-upload must respect effective Photographer quota.

Before committing a batch:

```text
current usage
+
total accepted batch size
<=
effective quota
```

where:

```text
effective quota
=
Super Admin override
??
Package quota
```

If storage is insufficient:

```text
fail predictably
show "Storage tidak mencukupi"
```

Avoid leaving a half-completed inconsistent batch.

If partial success is intentionally used, it must be explicit and robust.

Atomic batch validation is preferred where practical.

---

# 43. Event Photo Catalog — Fully Activate

Inside the Photo area, activate the existing Event/Album/Photo Catalog UI.

Audit every visible:

```text
button
link
card
filter
count
edit action
delete action
publish action
view action
```

Every control must work.

Use real:

- Event
- Photo
- Camera
- category
- taken_at
- daypart
- price
- status
- photo count
- thumbnail
- location

No `rand()`.

No fake event counts.

No dummy card.

---

# 44. Photo Statuses — Keep Three Concepts Separate

Do not mix these concepts.

## Photo Publication Status

```text
Active
Inactive
```

Active:

```text
available in marketplace
```

Inactive:

```text
not available in marketplace
```

## Buyer Purchase Access

```text
Locked
Unlocked
```

Locked:

```text
Buyer has not paid / does not own it
```

Unlocked:

```text
Buyer has a paid purchase
```

## Photographer Watermark Reuse State

```text
Watermark Locked
Watermark Unlocked
```

These are three separate state domains.

Do not reuse one field for another.

---

# 45. Orders & Transactions — Real Photographer Scope Only

Route:

```text
/fotografer/orders
```

The page must query only Transactions belonging to the authenticated Photographer.

Never accept a browser-provided Photographer ID as the scope.

Use:

```text
auth()->id()
```

or equivalent authenticated relation.

Photographer A must never see Photographer B's order.

---

# 46. Photographer Orders Data

Show real:

```text
Order ID
Photo
Event
Buyer
Price
Photographer 90% Amount
Created/Paid Time
Status
```

Use transaction snapshot amounts.

Do not use current Photo price for old Transactions.

---

# 47. Remove Status Filter Controls

Remove the current status filter buttons/dropdown from Photographer Orders.

Do not keep dead controls such as:

```text
All Status
Success
Pending
```

unless explicitly requested later.

Transaction status may still appear as an informational table badge/column.

---

# 48. Real Photographer Orders Pagination

Replace fake counters and fake Previous/Next.

Use real pagination.

Example:

```text
15 per page
```

Footer:

```text
Showing 1–15 of 43 transactions
Previous
Next
```

Must work.

---

# 49. Export CSV — Required

Activate:

```text
Export CSV
```

for Photographer Orders.

Export only Transactions belonging to the authenticated Photographer.

Suggested columns:

```text
Order ID
Date
Time
Buyer Name
Buyer Email
Photo
Event
Photo Price
Photographer Revenue 90%
Platform Revenue 10%
Status
```

Use actual available fields.

CSV requirements:

- UTF-8 BOM
- proper escaping
- stable column order
- numeric-friendly money values
- safe filename
- current Photographer scope only

Protect against CSV formula injection for strings beginning with:

```text
=
+
-
@
```

---

# 50. CSV vs Styled Excel

CSV cannot contain:

- colors
- fonts
- borders
- black/white Jepret styling

Therefore:

`Export CSV` must produce a clean Excel-compatible CSV.

If the project already contains an XLSX library, optionally add:

```text
Export Excel
```

with Jepret black/white formatting.

If XLSX requires a new dependency, STOP and ask before installing it.

CSV itself must work without a new spreadsheet package.

---

# 51. Remove Separate Earnings Page

Remove Photographer:

```text
Pendapatan & Pencairan
```

from the sidebar.

Remove the dedicated page/route only after:

- earnings metric is on Ringkasan;
- available balance is on Ringkasan;
- withdrawal form is on Ringkasan;
- withdrawal history/status is available on Ringkasan;
- recent Transactions are on Ringkasan.

Do not remove `Withdrawal` model or Super Admin payout management.

Do not remove earnings calculations.

---

# 52. Profile & Portfolio — Refactor

Route/view should use real Photographer data.

Remove all dummy profile content.

No:

- Unsplash cover
- hardcoded profile role
- hardcoded biography
- hardcoded fake public URL
- random portfolio images
- `rand()`
- fake sold count
- fake event count

---

# 53. Remove These Stats

Remove:

```text
1.2K FOTO TERJUAL
48 EVENTS
```

Do not replace them with another fake statistic.

If real stats are later shown elsewhere, they must be calculated from DB.

---

# 54. Edit Profile — Required

Activate:

```text
Edit Profile
```

Photographer may edit their own supported profile fields.

Audit schema first.

Use actual fields such as:

```text
name
studio_name
username / slug
avatar
location
category
bio
WhatsApp
```

only if they exist or are legitimately required.

If missing fields are necessary for the current product flow, add non-destructive migrations.

Do not invent unused fields.

---

# 55. Profile Validation and Ownership

Photographer may only edit their own profile.

Validate:

- name
- avatar type/size using sensible project rules
- slug uniqueness if applicable
- phone format
- location
- category
- bio length

Never allow one Photographer to edit another Photographer by changing a URL ID.

---

# 56. Real Portfolio Photo Cards

Portfolio cards must come from real Photos owned by the authenticated Photographer.

Use:

```text
latest photos
```

or current portfolio/public selection logic.

Display:

- actual preview
- title
- Event
- date

No remote random images.

No `for` loop with fake cards.

---

# 57. Public Profile Relationship

The internal Profile & Portfolio page should align with the Photographer's public profile.

Use actual route/slug.

Do not hardcode:

```text
jepret.com/p/dwivisual
```

Provide:

```text
View Public Profile
```

using the real route.

Public profile should continue to separate:

```text
Karya Pilihan / portfolio
```

from:

```text
Foto Tersedia / marketplace
```

according to the existing public flow.

---

# 58. Verification Relationship with Super Admin

Photographer verification status must reflect the Super Admin compliance system.

If Super Admin approves:

```text
Photographer sees verified state
```

If rejected:

```text
Photographer sees rejected state
and reason where appropriate
```

Do not create fake `Verified Photographer` badges independent of the real verification state.

Do not change publication restrictions unless the current application already uses verification as a permission gate or the repository logic explicitly requires it.

If introducing a new restriction such as "unverified Photographer cannot publish", STOP and ask first unless it already exists.

---

# 59. Account Settings

Keep Photographer Account Settings functional.

Do not add fake preference toggles.

Any visible setting must work.

Do not duplicate profile editing fields unnecessarily.

---

# 60. Security — Photo Ownership

Every Photographer Photo mutation must verify ownership.

For:

- edit
- delete
- activate
- deactivate
- metadata update
- watermark operation
- Event assignment

the Photo must belong to the authenticated Photographer.

Do not trust route IDs alone.

---

# 61. Security — Raw Original

Raw original media is private.

Do not place raw original Photo URLs in:

- public Blade markup
- marketplace HTML
- public JSON
- open storage path
- CSV export

Only authorized internal processing may access it.

Buyer authorized download uses the purchased Photographer-watermarked variant.

---

# 62. Revenue Integrity

Photographer earnings must only count paid Transactions.

Do not count:

```text
pending
failed
expired
```

toward Photographer earnings.

Use:

```text
transaction snapshot
```

not current Photo price.

If tips exist in the current schema, follow the current business rule consistently.

---

# 63. Performance

Avoid N+1 queries.

Use:

- eager loading
- `withCount`
- SQL aggregates
- paginated queries
- grouped daily queries

Photographer dashboard must not load every historical Transaction into memory just to calculate totals.

Review useful indexes, especially:

```text
transactions.fotografer_id
transactions.status
transactions.paid_at
photos.fotografer_id
photos.event_id
withdrawals.fotografer_id
withdrawals.status
```

Add only non-destructive indexes when clearly beneficial.

---

# 64. Responsive UI

Keep the Photographer Creator Center design:

```text
black
white
gray
minimal
ERP-like
clean
professional
```

Do not redesign it into the public Awwwards aesthetic.

However:

- eliminate tiny unreadable desktop text;
- remove excessive dead whitespace;
- fix table overflow;
- make forms mobile-friendly;
- make upload queue responsive;
- make metric cards readable;
- keep sidebar responsive.

Camera and Storage UI should remain visually intact as much as possible.

---

# 65. No Dead Controls

Audit every visible Photographer control.

For every:

```text
button
link
tab
filter
toggle
dropdown
action icon
form
pagination control
```

ask:

```text
Does this do something real?
```

If no:

- implement it if required by this specification;
- otherwise remove it.

Do not leave dummy action buttons.

---

# 66. Audit Log Relationship

Photographer actions do not need to duplicate the Super Admin audit log, but sensitive lifecycle changes should remain traceable where useful.

At minimum Super Admin audit logs cover:

- withdrawal status actions
- verification actions
- quota override
- settings changes

Photographer transaction/order records remain traceable through database timestamps/status.

Do not store secrets in audit metadata.

---

# 67. Testing — Mandatory

Add/update tests.

## Ringkasan Metrics

Test real data for:

```text
Net Earnings
Photos Sold
Total Visits
Purchase Conversion
```

Ensure no hardcoded values.

## Realtime/Notification Scope

Test:

```text
Photographer A receives/queries own paid order
Photographer B cannot see Photographer A order
```

## Withdrawal

Test:

```text
minimum withdrawal from Platform Settings
sufficient balance
insufficient balance
pending creation
duplicate submission safety
Super Admin sees request
hold state visible
success state visible
rejected state visible
rejection reason visible
balance restoration semantics remain correct
```

## Personal Watermark

Test:

```text
upload Watermark A
lock Watermark A
upload Photo 1 without new watermark → success
upload Photo 2 without new watermark → success

unlock Watermark
upload Photo 3 without watermark → validation failure

upload Watermark B
upload Photo 3 → success
```

## System Watermark

Test:

```text
marketplace preview contains system-watermark variant
public preview does not expose Photographer watermark
```

## Purchased Download Variant

Test:

```text
paid Buyer
→ system watermark not delivered
→ Photographer watermark delivered

unpaid Buyer
→ denied

different Buyer
→ denied

repeat paid download
→ allowed
```

## Multi-Upload

Test:

```text
single image
multiple images
invalid format
storage insufficient
effective quota override
large file without arbitrary Laravel max validation
```

## Orders

Test:

```text
only own Transactions
real pagination
90% snapshot value
CSV export
CSV formula injection protection
```

## Profile

Test:

```text
Photographer edits own profile
cannot edit another Photographer
portfolio uses own Photos
no dummy stats
```

## Cross-Role

Test:

```text
Buyer paid Transaction
→ Buyer Purchase History
→ Photographer Orders
→ Photographer metrics
→ Super Admin Ledger
```

---

# 68. MOV / Video Stop Condition

If MOV processing requires FFmpeg and it is not already installed:

STOP and ask.

Do not install system-level dependencies automatically.

Do not claim video watermarking is implemented when it is not.

---

# 69. Other Stop Conditions

Stop and ask before:

- adding Reverb/Pusher;
- installing FFmpeg;
- installing a new Excel/XLSX package;
- changing storage provider;
- changing payment provider;
- changing fixed 90/10 revenue;
- deleting existing tables/columns;
- introducing a major frontend framework;
- exposing raw original downloads;
- introducing a new Photographer verification publication restriction.

---

# 70. Implementation Order

Follow this order.

## Phase 1 — Audit

Report:

- Photographer routes
- Photographer views
- all dummy content
- all dead controls
- current metric data availability
- current `views_count` behavior
- current balance logic
- current Withdrawal submission behavior
- current personal watermark schema
- current system watermark code
- current public preview paths
- current purchased download path
- current upload formats and limits
- current Event catalog behavior
- current Orders pagination/export
- current Profile schema
- current Package/storage quota logic
- Super Admin Platform Settings relationship

## Phase 2 — Shared Data Foundation

Implement only required non-destructive migrations/services:

- personal watermark state
- Platform Settings consumption
- effective storage quota helper/service
- any missing transaction/payment snapshot support required by current flow

Do not duplicate concepts introduced by `superadmin.md`.

## Phase 3 — Ringkasan

Implement:

- authenticated name
- real metrics
- real sales chart
- order notifications
- available balance
- withdrawal form
- withdrawal status
- recent real sales
- real Previous/Next

## Phase 4 — Photo Pipeline

Implement:

- Photographer watermark upload
- lock/unlock
- system watermark asset
- watermark settings from Super Admin
- private raw original
- marketplace system-watermarked preview
- purchased Photographer-watermarked variant
- multi-upload
- format handling
- effective storage quota check

## Phase 5 — Event Catalog

Activate all real Event/Photo catalog actions and remove dummy values.

## Phase 6 — Orders

Implement:

- own real Transactions
- real pagination
- remove status filter controls
- CSV export
- near-realtime refresh if appropriate

## Phase 7 — Profile & Portfolio

Implement:

- edit profile
- real portfolio Photos
- public profile link
- remove fake statistics/content

## Phase 8 — Remove Photographer Earnings Page

Move/verify all required financial functions exist on Ringkasan.

Then remove:

```text
Pendapatan & Pencairan
```

from Photographer sidebar/page.

## Phase 9 — Cross-Role Validation

Validate:

```text
Buyer
↔ Photographer
↔ Super Admin
```

for:

- Transactions
- revenue
- Withdrawals
- storage
- verification
- watermark/download behavior

## Phase 10 — QA

Run all tests/build.

---

# 71. Required Project Verification

After implementation run:

```bash
php artisan migrate --no-interaction
vendor/bin/pint --dirty
php artisan test --compact
npm run build
php artisan route:list --except-vendor
```

Run other repository-provided checks if present.

Do not declare success if tests or build fail.

---

# 72. Final Acceptance Criteria

The Photographer task is complete only when:

- Ringkasan has no fake numbers;
- order notifications use real Transactions;
- four metric cards use DB data;
- available balance is real;
- withdrawal form creates the same Withdrawal seen by Super Admin;
- withdrawal status remains synchronized;
- Camera remains intact;
- Storage UI remains intact;
- effective quota can respect Super Admin override;
- Photographer watermark Lock/Unlock works;
- `watermarksistemjepret.png` is used for public preview;
- Photographer watermark is not shown publicly;
- paid Buyer download uses Photographer-watermarked variant;
- raw original remains private;
- Buyer can repeat-download purchased file safely;
- multi-upload works;
- application-level arbitrary 10MB Photo limit is removed;
- storage quota remains enforced;
- Event Photo Catalog buttons are real;
- Photographer Orders show only their own Transactions;
- Photographer status filter controls are removed;
- real pagination works;
- CSV export works;
- separate Photographer Earnings page/menu is removed only after functionality migration;
- Edit Profile works;
- Portfolio uses real Photos;
- fake `1.2K FOTO TERJUAL` and `48 EVENTS` are gone;
- Buyer, Photographer, and Super Admin data reconcile;
- all tests pass;
- frontend build passes.

---

# 73. Explicit Decisions — Do Not Re-Ask

The following decisions are already approved.

### Activate

- realtime/near-realtime paid-order notification
- Net Earnings card
- Photos Sold card
- Total Visits card
- Purchase Conversion card
- real withdrawable balance
- withdrawal form for bank/e-wallet
- recent sales with Previous/Next
- Photographer watermark upload
- Photographer watermark Lock/Unlock
- system watermark `watermarksistemjepret.png`
- multi-upload
- Event Photo Catalog actions
- real own Orders
- CSV export
- Edit Profile
- real Portfolio image cards

### Keep

- Camera as-is
- Photographer Storage page as-is
- fixed 90/10 split

### Remove

- all Photographer dummy data
- Photographer status-filter controls in Orders
- separate Photographer `Pendapatan & Pencairan` page/menu
- fake Portfolio statistics:
  - `1.2K FOTO TERJUAL`
  - `48 EVENTS`

### Special Watermark Rule

```text
Public user preview
= System watermark only

Paid Buyer download
= Photographer watermark only

Raw original
= Private
```

---

# 74. Final Codex Report

When finished, report:

```text
AUDIT SUMMARY

FILES CHANGED

MIGRATIONS ADDED

MODELS / RELATIONS CHANGED

SEEDERS CHANGED

RINGKASAN REAL DATA

NOTIFICATION IMPLEMENTATION

METRIC FORMULAS

WITHDRAWAL INTEGRATION

SUPER ADMIN RELATIONSHIP

PERSONAL WATERMARK ARCHITECTURE

WATERMARK LOCK / UNLOCK

SYSTEM WATERMARK INTEGRATION

PHOTO VARIANT ARCHITECTURE

MULTI-UPLOAD IMPLEMENTATION

SUPPORTED FORMATS

MOV / FFMPEG STATUS

STORAGE QUOTA LOGIC

EVENT CATALOG CHANGES

ORDERS CHANGES

CSV EXPORT STATUS

PROFILE CHANGES

PORTFOLIO CHANGES

REMOVED DUMMY CONTENT

REMOVED EARNINGS PAGE

BUYER DOWNLOAD COMPATIBILITY

CROSS-ROLE DATA CONSISTENCY

TEST RESULTS

BUILD RESULT

KNOWN LIMITATIONS
```

Do not commit or push unless explicitly requested.

Start with **Phase 1 — Audit**, then implement phase by phase.
