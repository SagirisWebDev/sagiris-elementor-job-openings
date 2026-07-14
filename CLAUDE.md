# Sagiris Elementor Job Openings

Custom Elementor widget: a company careers/job-openings list, backed by a real data layer rather than static repeater content. A Sagiris plugin. GPLv2-or-later.

- **Namespace / PSR-4:** `Sagiris\ElementorJobOpenings\` → `src/`
- **Text domain / prefix:** `sagiris-elementor-job-openings`
- **Tests:** PHPUnit via `composer test` — targets only the pure filter/sort module (`Job_Listing_Filter`), which is WP-bootstrap-free by design (it takes already-fetched plain data plus an injected "now" timestamp, so date-boundary logic is deterministic without a real WordPress environment). Lint via `composer lint` (PHPCS, WordPress Coding Standards).
- **CI:** GitHub Actions runs lint + the standalone PHPUnit suite on every push/PR; both are required status checks on `main` via branch protection. Deliberately does not spin up a full WordPress+MySQL test environment — nothing in this plugin's test suite needs one, since the repository fetch and all three adapters below are verified manually, not by automated tests.
- **Architecture:** one shared data layer, three consumers — `Job_Listing_Repository` (thin, WP-dependent `get_posts()` fetch) feeds `Job_Listing_Filter` (pure: department/location filtering, closing-date exclusion, sort order), wrapped by a shared service that the Elementor widget's `render()`, a custom REST route, and an optional GraphQL type all call. GraphQL support only registers if the WPGraphQL plugin is active; REST works with core WordPress alone; Elementor is the one hard `Requires Plugins` dependency.
- **`job_listing`** is a fully public custom post type with its own single template (no separate archive template — the Elementor widget is the list/archive view).
- **Testing scope:** only `Job_Listing_Filter` is automated-tested. The repository fetch, REST controller, GraphQL resolver, and Elementor widget are thin adapters verified manually (curl, GraphiQL, Playwright) each time they're touched — same workflow as the Testimonial Slider companion piece.

## Agent skills

### Issue tracker

Issues and PRDs live in this repo's GitHub Issues (`SagirisWebDev/sagiris-elementor-job-openings`), via the `gh` CLI. See `docs/agents/issue-tracker.md`.

### Triage labels

The five canonical triage roles map to identically-named labels (`needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`). See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `CONTEXT.md` + `docs/adr/` at the repo root (created lazily as terms/decisions get resolved). See `docs/agents/domain.md`.
