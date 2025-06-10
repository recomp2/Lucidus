# Dead Bastard Society

This repository contains the foundations of the Dead Bastard Society website. The project includes a custom WordPress theme and two plugins that power the Lucidus AI integration and membership system.

---

## Contents

- **dead-bastard-society** – custom theme directory
- **lucidus-terminal-pro** – Lucidus voice and chat terminal plugin
- **dbs-membership-core** – membership logic, initiation forms, and profile management
- **dbs-library** – location for JSON memory files and scroll data

The code is designed for a **WordPress.com Business Plan** environment.

---

## Theme Installation

1. Upload the `dead-bastard-society` directory to `/wp-content/themes/`.
2. Log in to your WordPress.com dashboard and activate the theme under **Appearance → Themes**.
3. Ensure the theme assets (`logo.svg`, `favicon.png`, and custom fonts) are present in `/dead-bastard-society/assets/branding/`.
4. Visit **Appearance → Customize** to configure colors, overlays, and other theme options.

Once activated, the theme provides templates for the initiation flow, member profiles, scroll walls, and the Lucidus terminal.

---

## Building Plugin ZIP Files

Both plugins can be zipped for easy installation. From the repository root:

```bash
zip -r lucidus-terminal-pro.zip lucidus-terminal-pro
zip -r dbs-membership-core.zip dbs-membership-core
```

Upload each ZIP through **Plugins → Add New → Upload Plugin** in your WordPress.com dashboard. Activate them after installation.

---

## JSON Memory Storage

Dynamic data such as member profiles, town information, and scroll histories are stored as JSON files. Place these files under:

```
/wp-content/dbs-library/
```

Example structure:

```
wp-content/
└── dbs-library/
    ├── memory-archive/
    │   └── profiles/
    │       └── {username}.json
    └── geo/
        └── {state}/{county}.json
```

Make sure the web server has read/write access to this directory so the plugins can save and retrieve memory files.

---

## Required WordPress.com Settings

For the full experience, enable the following features in your WordPress.com Business Plan:

1. **Plugins** – ensure plugin installation is allowed.
2. **REST API** – required for Lucidus terminal and membership endpoints.
3. **File Uploads** – JSON data and voice assets are stored in the `dbs-library` directory.
4. **Permalinks** – use "Post name" structure for clean URLs.

After activating the plugins, visit **Lucidus Terminal → Settings** and enter your API keys for OpenAI, Whisper, and ElevenLabs.

---

## Troubleshooting Lucidus API Keys

- **Invalid Key Error** – Double‑check that the API key you entered is correct and has not expired. Update it under **Lucidus Terminal → Settings**.
- **Network Issues** – The terminal requires outbound HTTPS requests. Ensure your hosting environment allows requests to the OpenAI and ElevenLabs endpoints.
- **Empty Responses** – If Lucidus does not respond, verify that your OpenAI usage limits have not been reached and that the key has access to the required models.
- **Voice Problems** – Whisper and ElevenLabs keys must also be set. Missing keys will disable voice input or TTS output.

If problems persist, enable WP_DEBUG mode in `wp-config.php` to capture error logs or contact your hosting provider for assistance.

---

## License

This project is provided under the MIT License. See `LICENSE` for details.

