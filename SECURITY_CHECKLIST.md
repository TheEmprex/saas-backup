# Production Security Checklist

## Environment
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Use strong, unique `APP_KEY`
- [ ] Use HTTPS only (`APP_URL=https://...`)
- [ ] Secure database credentials
- [ ] Use production payment keys (Stripe live keys)

## Server Security
- [ ] Configure firewall (UFW)
- [ ] Disable SSH root login
- [ ] Use SSH keys instead of passwords
- [ ] Keep server updated
- [ ] Configure fail2ban
- [ ] Set up log monitoring

## Application Security
- [ ] Enable CSRF protection
- [ ] Validate all user inputs
- [ ] Use parameterized queries
- [ ] Implement rate limiting
- [ ] Set secure session cookies
- [ ] Configure proper CORS headers

## Database Security
- [ ] Use strong database passwords
- [ ] Limit database user privileges
- [ ] Enable database SSL
- [ ] Regular database backups
- [ ] Monitor database access

## SSL/TLS
- [ ] Install SSL certificate
- [ ] Force HTTPS redirects
- [ ] Configure HSTS headers
- [ ] Check SSL score (A+ rating)

## Monitoring
- [ ] Set up error tracking (Sentry)
- [ ] Configure application monitoring
- [ ] Set up uptime monitoring
- [ ] Monitor server resources
- [ ] Set up backup monitoring

## Backup Strategy
- [ ] Automated daily database backups
- [ ] File system backups
- [ ] Test backup restoration
- [ ] Offsite backup storage
- [ ] Document recovery procedures
