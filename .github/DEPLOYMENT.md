# Deployment Guide

This project deploys automatically to a cPanel server via GitHub Actions whenever code is pushed to the `main` branch.

## Required GitHub Secrets

Set the following secrets in your GitHub repository under **Settings > Secrets and variables > Actions**:

| Secret | Description |
|--------|-------------|
| `SSH_HOST` | The hostname or IP address of your cPanel server (e.g. `yourdomain.com` or `123.45.67.89`) |
| `SSH_PORT` | The SSH port — usually `22`, but cPanel hosts sometimes use a custom port (check with your host) |
| `SSH_USERNAME` | Your cPanel SSH username (same as your cPanel login username) |
| `SSH_PRIVATE_KEY` | The private SSH key used to authenticate (see below) |
| `DEPLOY_PATH` | Absolute path to the Laravel project on the server (e.g. `/home/yourusername/public_html`) |

## How to Get the SSH Private Key from cPanel

1. Log in to cPanel and navigate to **Security > SSH Access** (or **Manage SSH Keys**).
2. If no key exists, click **Generate a New Key** and follow the prompts. Leave the passphrase blank (GitHub Actions cannot handle passphrase-protected keys).
3. After generating, click **Manage** next to the key and then **Authorize** it so it can be used to log in.
4. Click **View/Download** on the private key (`id_rsa` or similar).
5. Copy the entire contents of the private key file (including the `-----BEGIN ... KEY-----` and `-----END ... KEY-----` lines).
6. Paste the copied text as the value of the `SSH_PRIVATE_KEY` GitHub secret.

> **Note:** If you already have an SSH key pair on your local machine, you can add the public key (`~/.ssh/id_rsa.pub`) to cPanel via SSH Access > Import Key, authorize it, and use your existing private key as the secret.

## Setting Up the Repository on the cPanel Server

Before automated deployments will work, you need to clone the repository on the server once:

1. SSH into your cPanel server:
   ```bash
   ssh -p <SSH_PORT> <SSH_USERNAME>@<SSH_HOST>
   ```

2. Navigate to the deployment directory:
   ```bash
   cd /home/yourusername/public_html   # or wherever your site root is
   ```

3. If the directory is empty, clone the repo directly into it:
   ```bash
   git clone https://github.com/yourusername/yourrepo.git .
   ```
   If the directory already has files, initialize git and set the remote:
   ```bash
   git init
   git remote add origin https://github.com/yourusername/yourrepo.git
   git fetch origin
   git checkout main
   ```

4. For private repositories you will need to authenticate. Options:
   - Use a [GitHub Personal Access Token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens) in the clone URL: `https://<token>@github.com/yourusername/yourrepo.git`
   - Add a deploy key to the repository (**Settings > Deploy keys**) and configure SSH on the server.

5. Install PHP dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

6. Set up the `.env` file (see below) and run the initial migration:
   ```bash
   php artisan migrate
   ```

## The .env File

**The `.env` file is never committed to the repository.** You must create it manually on the server.

1. Copy the example file:
   ```bash
   cp .env.example .env
   ```

2. Edit it with your production values:
   ```bash
   nano .env
   ```
   At minimum, set:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://yourdomain.com`
   - `APP_KEY=` (generate with `php artisan key:generate`)
   - Database credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
   - Any other service keys (mail, cache, queue, etc.)

3. Generate the application key if not already set:
   ```bash
   php artisan key:generate
   ```

The `.env` file on the server persists across deployments — the workflow does not touch it.

## Queue Workers

If your application uses Laravel queues, the workflow will restart the queue worker after deployment (so it picks up new code) — but only if a `queue:work` process is currently running.

To keep a queue worker running persistently on cPanel, consider using **cPanel > Cron Jobs** to start the worker on reboot, or ask your host about Supervisor support.
