---
paths:
  - app/SubscriptionBilling.php
---

# App

## Accumulative photographer storage upon subscription purchase
When applying paid subscription orders (applyPaidOrder), photographer storage capacity must accumulate (total_storage = storage_existing + storage_granted_from_successful_purchase) inside entitlement_snapshot['storage_quota_bytes']. Never overwrite the historical storage quota with a single purchase snapshot. Ensure idempotent execution via processed_at and lockForUpdate inside DB transactions.
