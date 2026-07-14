# Sagiris Elementor Job Openings

**A custom Elementor widget** for a company careers/job-openings list — backed by a real data layer (a job-listing custom post type queried through a shared service, filterable by department/location, with automatic closing-date exclusion) and exposed over both a custom REST endpoint and (when [WPGraphQL](https://www.wpgraphql.com/) is active) GraphQL, not just static repeater content.

A [Sagiris](https://sagirisdev.com) plugin. GPLv2-or-later.

> 🚧 **Status:** in development. The v1.0 spec lives in the [`ready-for-agent` PRD issue](../../issues).

## Overview

- **Custom Elementor widget** — extends `Elementor\Widget_Base`, registered via the `elementor/widgets/register` hook, rendering a list of open job postings with department/location filters and sort controls.
- **One shared data layer, three consumers** — a pure filter/sort module (department, location, closing-date exclusion, sort order) sits behind a thin service that the Elementor widget, a custom REST route, and an optional GraphQL type all call — the widget is a second, traditional server-rendered consumer of the same API-shaped data model, not a one-off `WP_Query` dropped into the widget file.
- **`job_listing` custom post type** — a fully public post type with its own single template (title, department, location, closing date, full description, and an Apply URL/`mailto:` button).
- **REST always available; GraphQL conditional** — the REST route works with just WordPress core; GraphQL support only registers itself if the WPGraphQL plugin happens to be active.
- Companion piece to [Signalboard](https://github.com/SagirisWebDev/signalboard) and [the Testimonial Slider](https://github.com/SagirisWebDev/sagiris-elementor-testimonial-slider) — where the Testimonial Slider demonstrated Elementor's controls/render API against static repeater content, this piece demonstrates the same Elementor API against a genuine, independently-queryable data layer.

## Documentation

Architecture notes and engineering-decision rationale will live in `docs/` as the build progresses.
