# arsys-dns-update

Script to automate Arsys DNS entries

# Requirements

* Domain registered with Arsys provider
* Arsys DNS API enabled
* Arsys DNS API password

The API username is the same as the domain.

More info [here](https://pdc.arsys.es/descargas/Manual%20de%20Usuario%20API%20Hosting.pdf)

# Installation

1. Install required packages

```
sudo apt-get install $(cat packages.txt)
```

2. Install dependencies

```
./get-nusoap.sh
```

# Execution

1. Edit the provided example file:

```
vim example.php
```

2. And update below variables:

* domain. It will be used also as API username
* api_password. Should be optained from Arsys control panel. More info [here](https://pdc.arsys.es/descargas/Manual%20de%20Usuario%20API%20Hosting.pdf)
* dns_record. The DNS record to be updated
* record_type. Record type ('A', 'CNAME', etc.)

3. Execute it:

```
php example.com
```

Expected output similar to:

```
DNS site1.example.com does not exists yet
IP Changed
DNS site1.example.com does not exists yet
DNS entry not found
DNS created
```

4. Test

Check that the DNS record was actually created:

```
dig @1.1.1.1 site1.example.com +short
```

Expected output should be the public IP address of the server where you executed the script

# Cron

If you want to schedule a task to automate the DNS record IP update follow below instructions.

1. Create the log file

```
sudo touch /var/log/arsys-dns-update.php.log && sudo chmod 664 /var/log/arsys-dns-update.php.log && sudo chown root:`whoami` /var/log/arsys-dns-update.php.log
```

2. Add a cron entry, adjunsting the absolute path of the git project:

```
(crontab -l 2>/dev/null; echo "*/5 * * * * cd /home/user/arsys-dns-update && date >> /var/log/arsys-dns-update.php.log && php example.php >> /var/log/arsys-dns-update.php.log 2>&1") | crontab -
```
