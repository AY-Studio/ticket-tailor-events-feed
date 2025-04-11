# Ticket Tailor Upcoming Events Feed

**Contributors**: AY Studio  
**Tags**: ticketing, ticket tailor, events, api, shortcode  
**Requires at least**: 5.0  
**Tested up to**: 6.7  
**Requires PHP**: 7.4  
**Stable tag**: 1.1  
**License**: GPLv2 or later  
**License URI**: https://www.gnu.org/licenses/gpl-2.0.html  

Easily display upcoming Ticket Tailor events in a clean, responsive grid layout using a simple shortcode.

---

## Description

**AY - Ticket Tailor** is a lightweight WordPress plugin that connects to your Ticket Tailor account and displays a grid of upcoming events on any page or post using a shortcode.

✅ Built for flexibility, it works across any theme with no dependency on Bootstrap or external CSS frameworks.

### Features

- Pulls future **published** events from Ticket Tailor
- Responsive layout (1, 2, or 3 columns depending on screen size)
- Clean, theme-agnostic CSS
- Displays: Image, Title, and Date range
- Easy shortcode: `[ticket_tailor_events]`
- API key stored securely via the WordPress admin

---

## Installation

1. Upload the plugin to your `/wp-content/plugins/` directory or install via the WordPress dashboard.
2. Activate the plugin.
3. Go to **Settings → Ticket Tailor** and enter your **Ticket Tailor API key**.
4. Use the shortcode `[ticket_tailor_events]` in any page or post.

---

## How to Get Your Ticket Tailor API Key

1. Log in to your Ticket Tailor admin panel.
2. Go to **Settings → API keys**.
3. Create a new key and ensure it has **Events (read)** access.
4. Copy the API key (starts with `sk_`) and paste it into the plugin settings page in WordPress.

> 💡 You do **not** need to Base64 encode the key. Just paste it directly as provided by Ticket Tailor.

---

## FAQ

### Can I customize the layout or styling?
Yes! The plugin uses `.tt-events-grid` and `.tt-event-card` CSS classes for easy overrides. You can add custom styles in your theme or child theme.

### Does it support filtering by venue, tag, or category?
Not yet. This version shows all upcoming published events. Filters may be added in future updates.

---

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
