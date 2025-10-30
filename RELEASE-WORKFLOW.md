# Release Workflow

This document outlines the process for releasing new versions of the Duplicate Order Prevention for WooCommerce plugin.

## Release Checklist

### 1. Prepare Code Changes
- [ ] Make all code changes in the project
- [ ] Test thoroughly locally and in Docker
- [ ] Commit changes to GitHub main branch

### 2. Update Version Numbers
- [ ] Update version in `duplicate-order-prevention-for-woocommerce.php` (line 6 and line 29)
- [ ] Update version in `readme.txt` (Stable tag line)
- [ ] Commit version bump to GitHub with message: `v1.2.3: <description>`

### 3. Tag Release on GitHub
```bash
git tag v1.2.3
git push origin v1.2.3
```

### 4. Deploy to WordPress.org SVN
```bash
./deploy-to-svn.sh 1.2.3 svn_HS82Z3WGTgPimjZfkJ2RfTerfRwC6Wcae9ec32a
```

This script will:
- Checkout the SVN repo
- Copy your project files to trunk
- Remove development files (.git, *.md, docker-compose.yml, etc.)
- Commit trunk to SVN
- Create a tag in SVN

### 5. Verify Release
- [ ] Wait 15 minutes for WordPress.org to process
- [ ] Visit: https://wordpress.org/plugins/duplicate-order-prevention-for-woocommerce/
- [ ] Confirm new version is listed
- [ ] Check changelog is visible

## Files Excluded from SVN

The deploy script automatically removes these files:
- `.git/`, `.gitignore`
- `docker-compose.yml`
- `BUILD-ZIP.md`, `IMPLEMENTATION-SUMMARY.md`, `README.md`, `SUBMISSION-GUIDE.md`, `UPGRADE-SUMMARY.md`, `WORDPRESS-PLUGIN-ANALYSIS.md`
- `build-zip.sh`
- `duplicate-order-prevention-for-woocommerce.zip`
- `deploy-to-svn.sh`

## Files Included in SVN

- `duplicate-order-prevention-for-woocommerce.php` (main file)
- `readme.txt`
- `uninstall.php`
- `includes/` (all PHP files)
- `assets/` (CSS and JS)
- `languages/` (translation files)

## SVN Credentials

- **Username**: martinsplinter
- **Password**: Store securely (use environment variable or pass as argument)
- **SVN Repo**: https://plugins.svn.wordpress.org/duplicate-order-prevention-for-woocommerce/

## Troubleshooting

### Authentication Failed
- Verify SVN password is correct (starts with `svn_`)
- Check username is `martinsplinter`

### Commit Hangs
- Press Ctrl+C and try again
- Use `--non-interactive` flag to avoid prompts

### Files Not Added
- Run `svn status` to see what's pending
- Use `svn add *` to add all untracked files

## Future: GitHub Actions Automation

Once comfortable with manual process, we can automate this with GitHub Actions:
- Trigger on tag push (e.g., `git push origin v1.2.3`)
- Automatically run deploy script
- No manual SVN commands needed

