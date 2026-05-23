# UGC Metrics Platform

A comprehensive analytics platform for tracking and measuring User Generated Content (UGC) across social media channels like Instagram, TikTok, and YouTube.

## Overview

UGC Metrics is a Laravel-based web application designed to help marketers, content creators, and brands track the performance of influencer marketing campaigns and user-generated content across multiple social media platforms. The application allows users to monitor key metrics, generate reports, and gain insights into content performance.

## Features

- **Multi-Platform Support**: Track metrics from Instagram, TikTok, and YouTube
- **Influencer Management**: Add, edit, and organize influencers and their social profiles
- **Metric Tracking**: Monitor engagement, reach, impressions, and other key performance indicators
- **Custom Reports**: Generate and export custom reports based on different metrics
- **User Management**: Role-based access control for teams

## Requirements

- PHP 8.2 or higher
- Composer
- SQLite, MySQL 5.7+ or MariaDB 10.3+
- Node.js and NPM (for frontend assets)
- Laravel requirements (BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/ugcmetrics.git
   cd ugcmetrics
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   npm run build
   ```

4. Create and configure your environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Run migrations and seed the database:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. Link storage:
   ```bash
   php artisan storage:link
   ```

7. Serve the application:
   ```bash
   php artisan serve
   ```

## Database Structure

The application uses the following main models:

- **Influencer**: Represents content creators across platforms
- **Platform**: Supported social media platforms (Instagram, TikTok, YouTube)
- **SocialProfile**: Links influencers to their profiles on specific platforms
- **Metric**: General metrics applicable to any platform
- **[Platform]Metrics**: Platform-specific metrics (Instagram, TikTok, YouTube)

## Seeders

The application includes seeders to populate your database with test data:

- `PlatformSeeder`: Creates the supported social media platforms
- `InfluencerSeeder`: Creates sample influencers
- `MetricSeeder`: Populates sample metrics
- `InstagramInfluencersSeeder`: Imports influencers from a text file

To run a specific seeder:
```bash
php artisan db:seed --class=InstagramInfluencersSeeder
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- [Laravel](https://laravel.com/)
- [Livewire](https://laravel-livewire.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [FluxUI](https://fluxui.dev/) - Tailwind based UI components library
- [Heroicons](https://heroicons.com/) - Beautiful hand-crafted SVG icons