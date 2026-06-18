# LiveKerala Core PHP Directory

This is a fresh core PHP mini-directory project with:
- Contact form saving to MySQL + email attempt via `mail()`
- Add listing form saving to MySQL
- Admin login and dashboard
- Dynamic categories and listings from MySQL

## Admin Login
- URL: `/admin/login.php`
- Email: `admin@livekerala.com`
- Password: `password`

## Setup
1. Upload files to your server.
2. Create a MySQL database.
3. Import `database/livekerala.sql`
4. Edit `config/config.php`
5. Update DB credentials.
6. Optionally set `BASE_URL` if project is in a subfolder.
7. Make sure PHP `mail()` works on your server if you want email from contact form.

## Notes
- This is a clean starter, not a full enterprise directory system.
- Listings submitted from the frontend go in as `Pending`.
- Admin can approve listings and toggle Featured / Premium.


## Packages added
The admin now includes a **Packages** section.

Default packages seeded:
- Basic
- Standard
- Advanced
- Premium

Each package stores:
- name
- slug
- price
- description
- features
- sort order
- status

Pricing page now loads packages dynamically from MySQL.


## Package duration update
This version adds package validity in days.

### New package field
- `duration_days`

### Listing fields
- `package_id`
- `expiry_date`

### Behavior
- Frontend listing submission requires selecting a package
- Listing expiry date is calculated from package duration
- Home and categories pages only show active, non-expired listings
- Admin listings page shows package and expiry date
