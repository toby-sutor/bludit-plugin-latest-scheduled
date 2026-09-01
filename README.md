# Latest Scheduled

Puts the date of the **furthest scheduled article** in the Bludit **3.21** admin
sidebar, so you can see where the publishing queue ends without switching to
Content → Scheduled.

## Screenshot

<img width="276" height="531" alt="screenshot" src="https://github.com/user-attachments/assets/cccef850-679e-48e7-b624-a1054b15c801" />

---

## Installing

No build step. Copy the folder as it is:

```
bl-plugins/latest-scheduled/
├── plugin.php
├── metadata.json
├── css/latest-scheduled.css
└── languages/
```

1. Upload `latest-scheduled/` into your site's `bl-plugins/` directory.
2. Go to **Settings → Plugins** and click **Activate** on *Latest Scheduled*.
3. The date sits in the admin sidebar on every screen. Click it to open
 Content → Scheduled.

Keep the folder name. Bludit derives the plugin's storage directory from it, and
renaming it after install orphans the install record.

Requires Bludit **3.21**.  
Languages: English, German (`de_DE.json`).

---

## What it shows

- The calendar date (`15 Sep 2026`) of the scheduled page whose date is
 furthest in the future.
- Hover the entry for the full date-and-time, the page title, and how many
 pages are scheduled.
- If the queue is empty, the entry still appears and reads *None*.
- Authors only see their own scheduled pages. Admins and editors see all of
 them. Bludit's own sidebar hook is hidden from the Author role, so an Author
 does not see this entry at all.

The link is `admin/content#scheduled`, the same hash the dashboard already uses.

There are no settings. Activate it or deactivate it.

---

## Limitations

- **Author role.** Stock Bludit only renders `adminSidebar` for Admin and
 Editor. An Author already has Content → Scheduled for their own pages.
- **Nearest vs furthest.** This is the end of the queue, not the next page
 that will go live. That is the date you need when you are adding the next
 article in line.

---

## Changelog

### 0.1.0

- Initial release: furthest scheduled date in the admin sidebar, linking to
 Content → Scheduled.
