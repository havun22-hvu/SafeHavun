# End Session Command

Voer de volgende stappen uit om de sessie netjes af te ronden:

## 1. Git commit & push (LOKAAL)
- `git add .`
- Maak een duidelijke commit message met samenvatting van de wijzigingen
- `git push origin master`

**BELANGRIJK:** Altijd via GitHub deployen! Nooit rsync/scp gebruiken.

## 2. Deploy naar server (via git pull)
```bash
ssh root@188.245.159.115 << 'EOF'
cd /var/www/safehavun/production
git pull origin master
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
EOF
```

## 3. Branch cleanup
- Check op open branches: `git branch -a`
- Verwijder gemergte lokale branches: `git branch --merged | grep -v master | xargs git branch -d`

## 4. Bevestig aan gebruiker
- Geef korte samenvatting van wat er gedaan is
- Vermeld eventuele openstaande items
- Bevestig dat deploy is gelukt

## 5. Sluit af
- Zeg: "Sessie afgerond. SafeHavun is gedeployed naar https://safehavun.havun.nl"
