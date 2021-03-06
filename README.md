<!--
curl -s https://laravel.build/uReserve | bash 
sail up -d
sail artisan cache:clear
sail artisan config:clear

down()を実行後にup()を実行
sail php artisan migrate:refresh --seed
全テーブル削除してup()を実行
sail php artisan migrate:fresh --seed

sail php artisan migrate:fresh --seed --database=mysql.test
sail php artisan migrate:fresh --seed --env=testing

自動JSビルド
sail npm run watch

sail composer require barryvdh/laravel-debugbar

sail composer require laravel-lang/lang
cp ./vendor/laravel-lang/lang/locales/ja/ja.json ./lang/
cp -r ./vendor/laravel-lang/lang/locales/ja ./lang/


sail composer require laravel/jetstream
sail artisan jetstream:install livewire
sail npm install
sail npm run dev
sail artisan migrate

sail artisan storage:link

sail artisan vendor:publish --tag=jetstream-routes
sail artisan vendor:publish --tag=jetstream-views

sail artisan make:Controller LivewireTestController
sail artisan make:livewire counter
sail artisan make:livewire register

sail artisan make:controller AlpineTestController
sail artisan make:seeder UserSeeder

sail artisan vendor:publish --tag=laravel-errors
sail artisan make:model Event -a

sail artisan vendor:publish --tag=laravel-pagination

sail npm install flatpickr@^4 --save

sail artisan make:model Reservation -m
sail artisan make:Seed ReservationSeeder

sail artisan make:livewire Calendar

sail artisan make:Controller ReservationController
sail artisan make:Controller MyPageController

sail artisan make:request ReservationRequest

sail artisan make:test Services/EventServiceTest --unit
sail test tests/Unit/Services/EventServiceTest.php 

sail artisan make:test Services/MyPageServiceTest --unit
sail test tests/Unit/Services/MyPageServiceTest.php
sail artisan make:factory ReservationFactory --model=Reservation

sail artisan make:test Services/ReservationServiceTest --unit
sail test tests/Unit/Services/ReservationServiceTest.php

sail artisan make:test Auth/ManagerTest
sail test tests/Feature/Auth/ManagerTest.php

sail composer require --dev laravel/dusk
sail artisan dusk:install
sail dusk

sail composer require --dev phpunit/php-code-coverage
sail composer test:coverage-html

sail artisan make:test Controller/EventControllerTest
sail test tests/Feature/Controller/EventControllerTest.php

sail artisan make:test Controller/MyPageControllerTest
sail test tests/Feature/Controller/MyPageControllerTest.php

sail artisan make:test Controller/ReservationControllerTest
sail test tests/Feature/Controller/ReservationControllerTest.php

sail composer require --dev barryvdh/laravel-ide-helper
sail artisan ide-helper:model --nowrite
sail artisan ide-helper:generate
sail artisan ide-helper:meta
sail composer update

sail artisan livewire:publish --pagination

sail artisan dusk:make Livewire/RegisterTest
sail dusk:fails tests/Browser/Livewire/RegisterTest.php
sail dusk:fails tests/Browser/ExampleTest.php

sail composer require --dev nunomaduro/larastan
sail php ./vendor/bin/phpstan --memory-limit=1G analyse
sail php ./vendor/bin/phpstan analyse --generate-baseline

-->

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
