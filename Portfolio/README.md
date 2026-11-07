# Philip Antonio Dineros — Portfolio

A portable PHP portfolio and small JSON-based content manager for XAMPP. It has no database and no third-party runtime dependencies.

## Open it with XAMPP

1. Place this complete `Portfolio` folder inside `C:\xampp\htdocs\`.
2. Start **Apache** in the XAMPP Control Panel.
3. In your browser, open `http://localhost/Portfolio/public/`.
4. Create your admin password at `http://localhost/Portfolio/public/admin/login.php` the first time.

The Windows folder path `C:\xampp\htdocs\Portfolio\public\` is where the files live. To run PHP, open the matching browser address through Apache: `http://localhost/Portfolio/public/`.

No virtual host or hosts-file edit is needed. If you previously added a custom virtual host, remove its `<VirtualHost *:80>` block from `C:\xampp\apache\conf\extra\httpd-vhosts.conf` so `localhost` uses XAMPP's normal `htdocs` document root. The include line in `httpd.conf` may remain enabled.

The app detects the `/Portfolio/public/` subfolder automatically, so CSS, images, links, and admin routes work under `localhost`. Keep the folder name `Portfolio` or use that folder name in the browser address.

## Admin and content

Use **Alt + Shift + A** on the portfolio to open the admin login. The shortcut only reveals the login; it does not grant access. The panel edits homepage text and images, projects, About, skills, music, links, and résumé. The Album Covers panel provides three square slots; add an image and album name, or remove an album to free a slot. Content is stored in JSON and images in `storage/uploads/`. Each content save creates a dated backup.

Copy the entire folder when moving to another device, including `app`, `public`, and `storage`. Keep `storage/admin.json` to preserve admin access and `storage/content` to preserve your portfolio content. Use a current XAMPP installation with PHP 8.x and Apache.
