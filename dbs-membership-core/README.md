# DBS Membership Core

This plugin stores membership data for the Dead Bastard Society using JSON files located in `/wp-content/dbs-library/`.

Shortcodes provided:
- `[dbs_initiation_form]` – Join form that posts to the REST API.
- `[member_profile]` – Displays and edits the current user profile.
- `[scroll_wall]` – Simple message wall shared by members.

The plugin exposes REST endpoints under `/wp-json/dbs/v1/` used by these shortcodes.
