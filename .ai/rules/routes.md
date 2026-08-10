---
paths:
  - 'routes/**'
---

# Routes

## The short link redirect is a catch-all and must stay last
`routes/web.php` ends with `Route::get('/{link}', RedirectShortLinkController::class)`, which matches any single-segment path. Register every new web route ABOVE it, or the redirect will swallow it and return 404 for an unknown code.

Codes that would shadow a real path are blocked by `StoreLinkRequest::RESERVED_CODES` (currently api, up, storage). If you add a top-level route or path, add its first segment to that list too.
