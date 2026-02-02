# About

Below is a complete tiny **“wireframe share”** app using PHP + SQLite + HTML/CSS/JS.

What you get:

1. Upload a full-page wireframe image (PNG/JPG/WebP)

2. Give it a name + choose a device size preset

3. After submit, it appears as a card with:

- name

- Copy link button

- Visit button

> Visiting the link shows the wireframe inside a device-sized frame (like a real device viewport), scrollable.

**Folder structure**

Create a folder like wireframe-share/:
```
wireframe-share/
  index.php
  upload.php
  view.php
  db.php
  schema.sql
  /data
    app.sqlite
  /uploads
  /assets
    style.css
    app.js
```

Important permissions

> data/ and uploads/ must be writable by PHP.

Run app : 
```
php -S localhost:8000
```

Or use Docker