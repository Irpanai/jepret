# JEPRETCFD — Complete Final QA Prompt for Antigravity

## Project

Repository: `https://github.com/Irpanai/jepret`  
Branch: `main`

The project is considered feature-complete. Your task is now to inspect, test, clean, optimize, and fix the entire application before production.

This QA covers both **Frontend and Backend** and must validate the complete system across:

- Public Visitor
- Buyer
- Photographer
- Super Admin

The application must be production-ready on mobile, tablet, and desktop.

---

# 0. Critical Working Rule

Before modifying code:

1. Use the most relevant available **skill** and/or **planning mode**.
2. Read the repository first.
3. Create a short prioritized QA execution plan.
4. Then execute the plan.

For bugs or improvements that are clearly correct:
**IMPLEMENT THEM DIRECTLY.**

Ask me first only when:

- business behavior is ambiguous
- a feature must be removed
- destructive migration is required
- a new major dependency is required
- payment behavior would change
- storage architecture would change
- revenue split 90/10 would change
- an existing user flow would materially change

Do not commit or push unless I explicitly ask.

---

# 1. Read the Whole Project First

Audit the latest codebase before editing.

Read at minimum:

```text
README.md
composer.json
package.json
routes/**

app/Models/**
app/Http/Controllers/**
app/Services/**
middleware
requests / validation

database/migrations/**
database/factories/**
database/seeders/**

tests/**

resources/views/**
resources/css/**
resources/js/**

public assets

config files related to:
payment
filesystem
session
queue
mail
```

Understand the complete role architecture:

```text
Public Visitor
Buyer
Photographer
Super Admin
```

Understand the main relationship:

```text
Buyer
→ Cart
→ Checkout
→ Transaction
→ Photo
→ Photographer
→ Revenue 90/10
→ Withdrawal
→ Super Admin
```

Do not create duplicated logic between modules.

---

# 2. QA Strategy

Use this workflow:

```text
PHASE 1 — Audit
PHASE 2 — Prioritized QA Plan
PHASE 3 — Confirmed Bug Fixes
PHASE 4 — Frontend Responsive QA
PHASE 5 — Backend QA
PHASE 6 — Performance Optimization
PHASE 7 — SEO + Accessibility
PHASE 8 — Automated Tests
PHASE 9 — Final Production Audit
```

Do not stop after reporting obvious bugs.

Directly fix confirmed issues that do not require product decisions.

---

# 3. Frontend QA — Entire Application

Audit all frontend pages.

## Public

- Landing
- Gallery
- Photographer listing
- Photographer detail
- Photo detail
- Pricing/About-related pages
- Login
- Register
- Terms
- Privacy

## Buyer

- Cart
- Checkout
- Payment
- Checkout success
- Purchases
- Purchase detail
- Download flow

## Photographer

- Ringkasan
- Camera
- Photos
- Event catalog
- Orders
- Storage
- Profile & Portfolio
- Account settings

## Super Admin

- Command Center
- Photographer Compliance
- Ledger
- Withdrawals
- Storage
- Platform Settings
- Photographer management

Check:

```text
layout
spacing
typography
buttons
forms
tables
cards
images
icons
dropdowns
modals
pagination
filters
empty states
validation errors
loading states
error states
navigation
sidebar
header
footer
```

There must be no dead UI control.

---

# 4. Mobile Responsiveness — High Priority

The entire application must be genuinely mobile friendly.

Test approximately:

```text
320px
360px
375px
390px
430px
768px
```

Check every page.

Fix:

- horizontal overflow
- clipped content
- overlapping text
- overflowing tables
- images exceeding viewport
- buttons outside screen
- tiny buttons
- tiny fonts
- excessive padding
- broken grids
- bad card stacking
- broken navbar
- broken mobile menu
- dropdown positioning
- form overflow
- modal overflow
- sticky elements covering content

Touch targets should be comfortable on mobile.

Buttons/icons should generally have usable touch areas around 44px where appropriate.

Important content must never require horizontal scrolling unless the component is intentionally a data table.

For tables on mobile:
choose the cleanest approach between responsive scrolling and a mobile card layout.

Do not simply shrink desktop UI until it becomes unreadable.

---

# 5. Desktop Responsiveness

Test approximately:

```text
1024px
1280px
1366px
1440px
1600px
1920px
```

Fix layouts that create unnecessary large empty spaces.

Avoid very narrow content floating inside a huge desktop viewport unless intentionally designed that way.

Review:

- max-width containers
- grid columns
- dashboard workspace width
- table width
- hero proportions
- sidebar/content balance
- gallery density
- forms
- profile pages
- checkout
- admin dashboards

Use desktop screen space efficiently while keeping the existing minimal design.

Do not fill space with unnecessary decorative content.

---

# 6. Typography QA

Review text readability across all pages.

Fix text that is:

- too small
- too large
- cramped
- incorrectly wrapped
- inconsistent between similar components

Maintain hierarchy:

```text
Page title
Section heading
Card heading
Body text
Helper text
Table content
Labels
```

Mobile typography must remain readable.

Desktop typography should use available space properly.

---

# 7. Navbar & Routing QA — Important

Audit all links and routes.

Check:

- navbar
- mobile navbar
- footer
- CTA buttons
- cards
- dashboard sidebar
- breadcrumbs
- profile dropdown
- redirects after authentication
- redirects by role

I do NOT want navbar navigation relying on hash URLs such as:

```text
/#tentang
/#pricing
```

Use proper slash-based Laravel routes and named routes instead.

Examples conceptually:

```text
/about
/pricing
```

or another clean route structure that fits the current application.

Use named routes instead of hardcoded URLs wherever practical.

Do not leave:

```text
href="#"
javascript:void(0)
fake links
dead buttons
```

If homepage sections need to be reused for route-backed pages, reuse components where practical rather than duplicating large markup.

Ensure Buyer, Photographer, and Super Admin are redirected only to pages they are authorized to access.

Run a complete route audit.

---

# 8. Animation QA

Audit every existing animation.

All intended animations must work correctly on:

- mobile
- tablet
- desktop

Including current:

- landing reveal animation
- hero animation
- gallery reveal
- gallery image hover where supported
- gallery infinite-loading animation
- footer animation
- dropdown transitions
- other current UI transitions

Do not disable animation on mobile simply because the viewport is small.

Fix animations that:

- never trigger
- remain `opacity: 0`
- cause hidden content
- trigger before element enters viewport
- repeatedly trigger unexpectedly
- cause layout shifts
- reduce scrolling performance
- break after AJAX/infinite-scroll insertion

Preserve:

```text
prefers-reduced-motion
```

for accessibility.

Do not add excessive new animation.

Focus on making existing animations reliable.

---

# 9. Image QA

Audit every image.

Check:

- missing images
- incorrect image path
- broken preview
- distorted aspect ratio
- CLS
- oversized assets
- blurry images
- incorrect object-fit
- missing alt text
- loading behavior

Use:

```html
loading="lazy"
```

for below-the-fold/non-critical images where appropriate.

Do not lazy-load the primary above-the-fold hero/LCP image.

For important hero/LCP images, use appropriate eager loading and/or fetch priority when beneficial.

Do not eagerly load dozens of Gallery images.

Preserve image aspect ratio.

Add width/height or aspect-ratio information where practical to reduce layout shift.

Check especially:

- landing hero
- Gallery
- Photographer cards
- public Photographer profile
- Photo detail
- Portfolio
- Purchases
- dashboard thumbnails

---

# 10. Frontend Loading Experience

Audit page and interaction loading behavior.

Important interactions must provide useful feedback:

- Add to Cart
- Checkout
- Payment action
- infinite Gallery loading
- upload
- multi-upload
- CSV/PDF export
- Profile update
- Withdrawal submission
- Super Admin approve/reject
- search/filter

Prevent double submit.

Buttons performing network actions should have an appropriate disabled/loading state where useful.

Do not create unnecessary skeleton loaders everywhere.

Avoid content flashing and layout jumps.

---

# 11. Backend QA — Entire Application

Audit backend code quality and correctness.

Check:

- controllers
- models
- services
- validation
- middleware
- authentication
- authorization
- database transactions
- route model binding
- null safety
- exception handling
- file handling
- payment handling
- withdrawal handling
- watermark processing
- storage accounting
- CSV/PDF export
- Photographer verification
- Buyer purchase authorization

Find and fix:

- duplicated code
- extremely large methods
- incorrect responsibilities
- unreachable code
- unused imports
- old stub comments
- dead routes
- inconsistent naming
- hardcoded production data
- hidden N+1 queries
- unnecessary DB queries
- unsafe user input
- missing ownership checks
- race conditions
- double submissions

Do not refactor merely for style if it risks breaking working functionality.

Prefer safe, targeted improvements.

---

# 12. Eager Loading / N+1 QA — High Priority

Audit every Eloquent query used by pages that render relationships.

Pay special attention to:

- Gallery
- Photo Detail
- Photographer listing
- Photographer profile
- Purchases
- Photographer dashboard
- Photographer orders
- Storage
- Super Admin dashboard
- Compliance
- Ledger
- Withdrawals

Detect N+1 queries.

Use appropriate:

```text
with()
withCount()
withSum()
select()
aggregate SQL
subqueries
```

where they improve performance.

Do not eager-load huge unused relations.

Select only required fields for large lists where practical.

Do not move simple SQL aggregation into large PHP collections.

---

# 13. Database Query Performance

Review frequently queried fields.

Check appropriate indexing for fields such as:

```text
role
status
payment_status
fotografer_id
pembeli_id
photo_id
event_id
created_at
paid_at
verification status
```

Do not add unnecessary indexes.

If a safe index is clearly missing on a frequently filtered relational column, add a non-destructive migration.

Ask first if a database change is uncertain.

---

# 14. Pagination / Memory QA

Avoid loading unlimited large datasets.

Review use of:

```php
get()
```

on potentially large collections.

Use pagination or cursor pagination where suitable.

Pay particular attention to:

- Buyer Purchases
- Gallery
- Photographer Orders
- Super Admin Ledger
- Withdrawals
- Photographer management
- Storage lists

Preserve query-string filters across pagination.

---

# 15. File / Media Security QA

Verify:

```text
RAW ORIGINAL PHOTOS MUST REMAIN PRIVATE
```

Public users must only receive authorized public previews.

Paid Buyer downloads must verify ownership/payment.

Test:

- Buyer A cannot download Buyer B's purchase
- Photographer A cannot modify Photographer B's Photos
- Photographer A cannot modify Photographer B's Events
- Photographer A cannot modify Photographer B's Cameras
- public users cannot guess raw-original URLs
- Super Admin access follows intentional permissions

Audit path traversal and unsafe filenames.

---

# 16. Watermark QA

Verify the complete watermark pipeline.

Expected:

```text
Raw original
→ private

Marketplace preview
→ system watermark

Paid Buyer download
→ Photographer watermark variant
```

Test:

- portrait image
- landscape image
- square image
- high resolution
- small resolution
- transparent watermark
- watermark position
- watermark scale
- watermark opacity

No stretched watermark.

No preview should expose the wrong variant.

---

# 17. Transaction / Financial QA

Revenue remains fixed:

```text
Photographer = 90%
Platform = 10%
```

Audit every calculation.

Historical Transactions must use stored snapshots.

Changing a Photo's current price must not change old Transaction values.

Check:

```text
pending
paid
failed
expired
```

Only valid paid Transactions should affect Photographer earnings according to current business rules.

Test transaction creation for multiple Photos.

Test duplicate payment requests/idempotency.

---

# 18. Withdrawal QA

Audit:

```text
Photographer request
→ pending
→ held / success / rejected
→ Super Admin
→ Photographer state
```

Test:

- insufficient balance
- minimum withdrawal
- duplicate request
- double approve
- double reject
- reject refund
- batch approval
- held request
- concurrent actions

No balance may become negative because of a race condition.

Rejected amount must not be refunded twice.

---

# 19. Cart & Checkout QA

Test:

- guest Cart behavior
- authenticated Cart
- add duplicate Photo
- remove Photo
- unavailable Photo during checkout
- Photographer becomes inactive before checkout
- multiple Photos in one order
- refresh Payment page
- duplicate payment submission
- Checkout Success
- Purchase History
- repeat download

Fix inconsistent Cart counters or stale state.

---

# 20. Authentication / Role QA

Audit:

- Login
- Register
- Google auth
- Logout
- role conversion / Photographer registration
- dashboard redirects
- verified middleware
- role middleware

Test direct URL access.

A user must never gain another role's access by manually entering a URL.

---

# 21. SEO Basic QA

Audit SEO for PUBLIC pages only.

Check:

- unique `<title>`
- meta description
- canonical
- robots
- Open Graph
- Twitter card
- semantic H1
- heading hierarchy
- sitemap
- structured data
- internal links
- image alt
- clean route URLs

Private pages such as:

- Cart
- Checkout
- Payment
- Purchases
- Photographer dashboard
- Super Admin

should generally be `noindex` where appropriate.

Ensure Sitemap exposes only valid public entities.

Do not overengineer SEO.

---

# 22. Accessibility Basic QA

Check:

- button vs anchor semantics
- label/input association
- `aria-expanded`
- dropdown keyboard behavior
- focus-visible state
- alt text
- form errors
- contrast
- touch targets
- keyboard navigation

Do not sacrifice the current design.

---

# 23. JavaScript QA

Audit Alpine/native JavaScript.

Check:

- console errors
- null selectors
- double initialization
- duplicate `DOMContentLoaded` listeners
- infinite-scroll observer cleanup
- mobile menu state
- dropdown state
- Cart AJAX
- Gallery AJAX
- dynamically inserted Gallery items

Ensure JavaScript failure does not destroy basic server-rendered navigation.

Keep progressive enhancement.

---

# 24. CSS QA / Cleanup

Audit:

```text
resources/css/app.css
```

and Blade utility classes.

Find:

- conflicting rules
- duplicate rules
- obsolete classes
- accidental mobile-only overrides
- excessive fixed dimensions
- broken breakpoint logic

Do not aggressively rewrite Tailwind styling.

Only clean confirmed issues.

---

# 25. Code Cleanliness

Search the project for:

```text
TODO
FIXME
HACK
STUB
MOCK
dummy
rand(
console.log
dd(
dump(
var_dump
href="#"
```

Review each result.

Remove obsolete development/debug code.

Do not remove legitimate test seeders simply because they contain development data.

Production pages must not depend on fake values.

---

# 26. Error / Empty States

Every important data page must behave correctly with zero records.

Check:

- no Photos
- no Events
- no Transactions
- no Purchases
- no Withdrawals
- no Photographers
- empty search
- no search results

Do not allow undefined-variable or null-relationship errors.

---

# 27. HTTP / Security QA

Review:

- CSRF
- POST/PATCH/DELETE semantics
- GET routes causing mutation
- validation
- authorization
- rate/double-submit protections
- unsafe redirects
- mass assignment
- escaped Blade output

Do not expose secrets in responses or HTML.

---

# 28. Route Cleanup

Run:

```bash
php artisan route:list --except-vendor
```

Review every application route.

Check:

- duplicate paths
- obsolete routes
- routes to removed controller actions
- wrong HTTP methods
- inconsistent naming
- confusing redirects
- unused resource actions

Do not delete a route simply because it appears unused without confirming the feature.

Clean obvious stale/stub routes where safe.

---

# 29. Performance / Page Speed

Improve obvious performance issues.

Focus on:

- DB queries
- image loading
- lazy loading
- LCP
- CLS
- blocking assets
- repeated queries
- very large DOM
- unnecessary network calls
- infinite-scroll behavior

Do not add a large optimization dependency.

Do not prematurely optimize tiny details.

---

# 30. Testing Viewport Matrix

Manually inspect or browser-test representative pages at:

## Mobile

```text
375×812
390×844
430×932
```

## Tablet

```text
768×1024
```

## Laptop

```text
1366×768
```

## Desktop

```text
1440×900
1920×1080
```

Test with both short and long real content.

Do not validate responsiveness only with empty data.

---

# 31. Automated Verification

Run at minimum:

```bash
php artisan migrate --no-interaction
php artisan test --compact
vendor/bin/pint
npm run build
php artisan route:list --except-vendor
git diff --check
```

Also run relevant project-specific validation available in the environment.

If browser-testing capability exists, use it.

Check browser console and network failures.

---

# 32. Do Not Break Current Working Features

Preserve:

- fixed 90/10 split
- Photographer verification
- Buyer purchase authorization
- private originals
- watermark system
- withdrawal workflow
- storage quota
- CSV export
- PDF report
- Photographer/Super Admin integration
- existing seeders
- existing animations

Do not clean up a working feature simply because you would design it differently.

---

# 33. Final Expectation

The final application should feel production-ready on:

```text
mobile
tablet
desktop
```

There should be no obvious:

- broken route
- dead button
- horizontal overflow
- fake production metric
- console error
- PHP error
- N+1 issue
- duplicate financial mutation
- unauthorized access
- broken image
- broken animation
- giant empty desktop gap
- unreadably small mobile text

---

# 34. Final Report

After completing QA, report:

```text
QA PLAN USED

BUGS FOUND

BUGS FIXED

FRONTEND FIXES

MOBILE FIXES

DESKTOP FIXES

NAVIGATION / ROUTE FIXES

ANIMATION FIXES

IMAGE / LAZY LOADING FIXES

BACKEND FIXES

QUERY / EAGER LOADING FIXES

SEO FIXES

ACCESSIBILITY FIXES

SECURITY FIXES

CODE CLEANUP

DATABASE / INDEX CHANGES

FILES CHANGED

TEST RESULTS

BUILD RESULT

ROUTE AUDIT RESULT

KNOWN LIMITATIONS

ITEMS THAT STILL REQUIRE MY DECISION
```

Do not commit or push unless I explicitly ask.

Start by reading the repository and using an appropriate skill/planning workflow.

Then execute the QA plan and directly fix confirmed issues.
