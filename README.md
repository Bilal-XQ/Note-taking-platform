Student Note-Taking Website
Objective
The purpose of this project is to develop a simple and effective web application that helps students stay organized, take notes for their different modules, and improve their learning and revision process.

Key Features
Each student will be able to:

Add notes for different modules (e.g., Web, OOP, Math, etc.)

Edit their notes at any time

Delete notes they no longer need

Organize notes by module

Note: Organization by chapters or specific topics may be added in the future.

Technologies to Use
PHP — Handles server-side logic (create, edit, delete notes)

MySQL — Stores data (notes, modules, users)

CSS — Designs the website layout, colors, and structure

JavaScript — Adds interactivity (modals, alerts, dynamic buttons)

Future Enhancements (AI Integration)
Integrate Artificial Intelligence to:

Generate summaries from students��� notes

Create quizzes to help students review their content

---

Running the Project with Docker

This project includes Docker and Docker Compose configuration for easy setup and development.

Requirements
- Docker
- Docker Compose

Services
- **php-app**: PHP 8.2 FPM (Alpine), with required extensions (pdo, pdo_mysql, gd, intl, zip, mbstring) and Composer installed.
- **mysql-db**: MySQL (latest), with persistent storage.

Environment Variables
- The MySQL service uses the following environment variables (set in `docker-compose.yml`):
  - `MYSQL_ROOT_PASSWORD`: rootpassword  *(change this in production)*
  - `MYSQL_DATABASE`: notes_db
  - `MYSQL_USER`: notes_user
  - `MYSQL_PASSWORD`: notes_password  *(change this in production)*
- If you have additional environment variables (e.g., for PHP), you can add a `.env` file and uncomment the `env_file` line in the compose file.

Ports
- **php-app**: Exposes port `9000` (php-fpm). You may need to add a web server (e.g., nginx or apache) to serve the application in a browser.
- **mysql-db**: Exposes port `3306` for local database access.

Build and Run Instructions
1. Build and start the services:
   \```sh
   docker compose up --build
   \```
2. The PHP application will be running in the `php-app` container on port 9000 (php-fpm). To access the web interface, you may need to set up a web server container (not included by default).
3. The MySQL database will be available on port 3306 with the credentials specified above.

Special Configuration
- Application files are owned by a non-root user (`appuser`) for security.
- Persistent MySQL data is stored in the `mysql-data` Docker volume.
- The application code is copied into `/var/www/html` in the container.

For development, you can connect to the MySQL database using the credentials and port above. If you need to serve the PHP application via a web server, consider adding an nginx or apache service to your `docker-compose.yml` and link it to the `php-app` service.
