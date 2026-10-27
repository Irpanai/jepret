---
paths:
  - app/Http/Controllers/MarketplaceController.php
---

# Controllers

## Gallery filtering logic and avoiding Eloquent model caching in MySQL
Always avoid caching full Eloquent model collections inside database cache drivers (due to null-byte serialization pitfalls with protected model properties). For gallery filters, calculate daypart dynamically from WITA (Asia/Makassar, UTC+8) timestamps when photos.daypart is null, combine event and photographer locations, and strip empty query parameters on form submission for clean shareable URLs.
