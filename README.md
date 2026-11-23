# Student Information System

A local Laravel and MySQL lab for student registration, academic records, and role-based access. The pages use Blade and a standalone CSS file, so no Node build is needed.

## Run locally

Use PHP 8.4, Composer, and MySQL 8.4. Enable the PHP MySQL PDO driver and the standard Laravel extensions. Create an empty MySQL database and a dedicated account with privileges limited to that database. For a fresh checkout in PowerShell:

```powershell
composer install
Copy-Item .env.example .env
# Set the MySQL host, port, database name, username, and password in .env.
php artisan key:generate
php artisan migrate
php artisan serve
```

Open the URL printed by Artisan and select **Register Student**. If this workspace already has a configured `.env`, reuse it.

### Existing Windows demo

The local demo binds Laravel and MySQL to the loopback interface. If the processes have stopped, run the following from the project folder to restart them:

```powershell
$sisRuntime = "$env:LOCALAPPDATA/SirDurango-runtime"
$sisMySql = "$sisRuntime/mysql-8.4.13-winx64"
if (!(Test-NetConnection 127.0.0.1 -Port 33306 -InformationLevel Quiet)) {
    Start-Process "$sisMySql/bin/mysqld.exe" -WindowStyle Hidden -ArgumentList "--no-defaults --basedir=`"$sisMySql`" --datadir=`"$sisRuntime/mysql-data`" --bind-address=127.0.0.1 --port=33306 --mysqlx=OFF"
}
& "$sisRuntime/php/php.exe" artisan serve --host=127.0.0.1 --port=8015
```

## Check the project

Run `php artisan test` with the PHP SQLite PDO extension enabled. Automated feature tests use an in-memory database. The browser demonstration uses MySQL. See [Demo Steps](docs/lab/Demo_Steps.md) for the database check and role walkthrough.

The demo enrolls students in school year 2026–2027, semester 1. The current term is defined in `app/Models/Enrollment.php`. Final grades are whole numbers from 0 to 100.

The lab submission includes the [architecture diagram](docs/lab/Student_System_Architecture.pdf) and the [written report](docs/lab/Surname_SystemIntegration_LabActivity.pdf). Replace the filename placeholder with the submitter's surname.
