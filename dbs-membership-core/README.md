# DBS Membership Core

The DBS Membership Core plugin powers the Dead Bastard Society initiation flow and member management.

## Included Features

- Admin member directory with search, promote, and delete actions
- JSON profile editor for each user
- Settings page for archetypes and naming rules
- Initiation form shortcode to register new members
- Confirmation shortcode to finish signup and found new towns
- Analytics dashboard showing member and scroll counts
- Profile viewer, scroll list, and geo map shortcodes
- Automatic scroll generator for new chapter founders
- Lucidus AI integration to keep profiles in sync

## Rank System

Members advance through three ranks:

1. **Initiate** – entry level after signup
2. **Acolyte** – granted after completing basic quests
3. **Bastard** – the highest standard rank

## Additional Tools

The plugin also provides:

- Undo scroll claim via the Scrolls admin page
- Admin alerts on new member registration
- Ability to lock and unlock users
- Scroll editing through the Scrolls manager
- Bulk member export to CSV
- Town claim reassignment if the first founder does not complete registration

## What's Still Optional

| Feature | Status | Notes |
| ------- | ------ | ----- |
| 🔄 Undo scroll claim | ✅ | Admin can delete a scroll and reassign the town |
| 📨 Admin alerts on join | ✅ | Emails and logs when members register |
| 🔒 Lock user (no login) | ✅ | Set `dbs_locked` meta to prevent login |
| 🔁 Scroll editing | ✅ | Edit stored scroll files through the admin page |
| 🗃️ Bulk user exporter (CSV) | ✅ | Download members list for spreadsheets |
| 🧭 Town claim reassignment queue | ✅ | Manage pending towns via Geo Queue |

## Compatibility

Tested with PHP **8.3.22**, WordPress **6.8.1**, and the Lucidus plugin **4.0.0**.

